<?php
use PHPUnit\Framework\TestCase;

class MuseumPagesTest extends TestCase {
    private function runPage(string $script): array {
        $cmd = sprintf('php -d auto_prepend_file=%s %s',
            escapeshellarg(__DIR__.'/fixtures/prepend.php'),
            escapeshellarg($script)
        );
        $descriptor = [1 => ['pipe','w'], 2 => ['pipe','w']];
        $proc = proc_open($cmd, $descriptor, $pipes);
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
