<?php
use PHPUnit\Framework\TestCase;

class ActiveLinkTest extends TestCase {
    private function runPage(string $script): array {
        $prepend = realpath(__DIR__.'/fixtures/page_prepend.php');
        // Forzar la carga de pdo_sqlite para esta invocación de php-cgi
        $cmd = sprintf('php-cgi -d extension=pdo_sqlite.so -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $rel = substr($script, strlen(__DIR__ . '/..') + 1);
        $env = [
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script,
            'SCRIPT_NAME' => '/' . $rel
        ];
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    public static function pageProvider(): array {
        return [
            ['index.php'],
            ['historia/historia.php'],
            ['museo/galeria.php'],
        ];
    }

    private function findActiveHref(string $html): ?string {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        foreach ($dom->getElementsByTagName('a') as $a) {
            $class = $a->getAttribute('class');
            if (preg_match('/\bactive-link\b/', $class)) {
                return $a->getAttribute('href');
            }
        }
        return null;
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageSetsActiveLink(string $rel): void {
        $path = __DIR__.'/..'.'/'.$rel;
        $this->assertFileExists($path);
        [$status, $out, $err] = $this->runPage($path);
        $this->assertSame(0, $status, $err);
        $active = $this->findActiveHref($out);
        $this->assertSame($rel, $active);
    }
}
?>

