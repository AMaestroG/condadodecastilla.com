<?php
use PHPUnit\Framework\TestCase;

class MuseumPagesTest extends TestCase {
    private function runPage(string $script): array {
        $prepend = realpath(__DIR__.'/fixtures/prepend.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env = [
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script
        ];
        $descriptor = [1 => ['pipe','w'], 2 => ['pipe','w']];
        $proc = proc_open($cmd, $descriptor, $pipes, null, $env);
        $output = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $output, $err];
    }

    public function pageProvider(): array {
        return [
            [__DIR__ . '/../museo/subir_pieza.php'],
            [__DIR__ . '/../museo/galeria.php'],
            [__DIR__ . '/../museo/museo_3d.php'],
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPagesLoad(string $path): void {
        [$status, $out, $err] = $this->runPage($path);
        $this->assertSame(0, $status, $err);
        $this->assertNotEmpty($out);
    }
}
