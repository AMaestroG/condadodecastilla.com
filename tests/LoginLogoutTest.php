<?php

use PHPUnit\Framework\TestCase;

class LoginLogoutTest extends TestCase
{
    private string $dbFile;

    protected function setUp(): void
    {
        $this->dbFile = tempnam(sys_get_temp_dir(), 'login');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }

    private function runScript(string $script, array $env): array
    {
        $prepend = realpath(__DIR__.'/fixtures/login_prepend.php');
        $cmd = sprintf(
            'php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env['PATH'] = getenv('PATH');
        $env['TEST_SQLITE_PATH'] = $this->dbFile;
        $proc = proc_open($cmd, [1 => ['pipe','w'], 2 => ['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    private function parseHeaders(string $out): array
    {
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

    public function testLoginLogout(): void
    {
        $env = [
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => __DIR__.'/../dashboard/login.php',
            'REQUEST_METHOD' => 'POST',
            'PHP_USERNAME' => 'admin',
            'PHP_PASSWORD' => 'secret'
        ];
        [$status, $out, $err] = $this->runScript(__DIR__.'/../dashboard/login.php', $env);
        $this->assertSame(0, $status, $err);
        $headers = $this->parseHeaders($out);
        $this->assertSame('/dashboard/index.php', $headers['Location'][0] ?? null);
        $this->assertArrayHasKey('Set-Cookie', $headers);
        preg_match('/PHPSESSID=([^;]+)/', $headers['Set-Cookie'][0], $m);
        $this->assertNotEmpty($m);
        $sid = $m[1];

        $sessionFile = ini_get('session.save_path').'/sess_'.$sid;
        $data = file_get_contents($sessionFile);
        $this->assertNotFalse($data);
        session_start();
        session_decode($data);
        $this->assertSame('admin', $_SESSION['user_role']);
        session_write_close();

        $env = [
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => __DIR__.'/../dashboard/logout.php',
            'REQUEST_METHOD' => 'GET',
            'PHP_SESSION_ID' => $sid
        ];
        [$status2, $out2, $err2] = $this->runScript(__DIR__.'/../dashboard/logout.php', $env);
        $this->assertSame(0, $status2, $err2);
        $headers2 = $this->parseHeaders($out2);
        $this->assertSame('login.php', $headers2['Location'][0] ?? null);
        $this->assertFileDoesNotExist($sessionFile);
    }
}
