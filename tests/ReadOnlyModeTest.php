<?php
use PHPUnit\Framework\TestCase;

class ReadOnlyModeTest extends TestCase {
    private function runPage(string $script): array {
        $prepend = realpath(__DIR__.'/fixtures/page_prepend_nodb.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env = [
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script
        ];
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    public function pageProvider(): array {
        return [
            [__DIR__.'/../index.php'],
            [__DIR__.'/../alfoz/alfoz.php'],
            [__DIR__.'/../contacto/contacto.php']
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPagesShowReadOnlyNotice(string $page): void {
        [$status, $out, $err] = $this->runPage($page);
        $this->assertSame(0, $status, $err);
        $this->assertStringContainsString('Contenido en modo lectura: base de datos no disponible.', $out);
    }
}
?>
