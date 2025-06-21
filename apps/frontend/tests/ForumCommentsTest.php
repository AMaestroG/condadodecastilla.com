<?php
use PHPUnit\Framework\TestCase;

class ForumCommentsTest extends TestCase {
    private string $dbFile;

    protected function setUp(): void {
        $this->dbFile = tempnam(sys_get_temp_dir(), 'forum');
    }

    protected function tearDown(): void {
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }

    private function runForum(array $env = []): array {
        $script = realpath(__DIR__.'/../foro/index.php');
        $prepend = realpath(__DIR__.'/fixtures/forum_prepend.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env = array_merge([
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script,
            'TEST_SQLITE_PATH' => $this->dbFile,
            'FORUM_COMMENT_COOLDOWN' => '2',
        ], $env);
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    private function parseHeaders(string $out): array {
        $headers = [];
        foreach (explode("\n", trim($out)) as $line) {
            if ($line === '' || str_starts_with($line, 'Status:')) {
                continue;
            }
            if (strpos($line, ':') !== false) {
                [$k, $v] = array_map('trim', explode(':', $line, 2));
                $headers[$k][] = $v;
            }
        }
        return $headers;
    }

    public function testValidCommentInserted(): void {
        [$status, $out, $err] = $this->runForum([
            'REQUEST_METHOD' => 'POST',
            'FORUM_AGENT' => 'historian',
            'FORUM_COMMENT' => 'Hola',
        ]);
        $this->assertSame(0, $status, $err);
        $pdo = new PDO('sqlite:' . $this->dbFile);
        $count = $pdo->query('SELECT COUNT(*) FROM forum_comments')->fetchColumn();
        $this->assertSame(1, (int)$count);
    }

    public function testInvalidCsrfRejected(): void {
        [$status, $out, $err] = $this->runForum([
            'REQUEST_METHOD' => 'POST',
            'FORUM_AGENT' => 'guide',
            'FORUM_COMMENT' => 'fail',
            'FORUM_TOKEN' => 'bad',
        ]);
        $this->assertSame(0, $status, $err);
        $pdo = new PDO('sqlite:' . $this->dbFile);
        $count = $pdo->query('SELECT COUNT(*) FROM forum_comments')->fetchColumn();
        $this->assertSame(0, (int)$count);
    }

    public function testRateLimiting(): void {
        [$status1, $out1, $err1] = $this->runForum([
            'REQUEST_METHOD' => 'POST',
            'FORUM_AGENT' => 'guide',
            'FORUM_COMMENT' => 'first',
        ]);
        $this->assertSame(0, $status1, $err1);
        $headers = $this->parseHeaders($out1);
        preg_match('/PHPSESSID=([^;]+)/', $headers['Set-Cookie'][0] ?? '', $m);
        $sid = $m[1] ?? '';
        $this->assertNotEmpty($sid);

        [$status2, $out2, $err2] = $this->runForum([
            'REQUEST_METHOD' => 'POST',
            'FORUM_AGENT' => 'guide',
            'FORUM_COMMENT' => 'second',
            'PHP_SESSION_ID' => $sid,
        ]);
        $this->assertSame(0, $status2, $err2);

        $pdo = new PDO('sqlite:' . $this->dbFile);
        $count = $pdo->query('SELECT COUNT(*) FROM forum_comments')->fetchColumn();
        $this->assertSame(1, (int)$count);
    }
}
?>
