<?php
use PHPUnit\Framework\TestCase;

class FixedHeaderElementsTest extends TestCase {
    private function runPage(string $script): array {
        $prepend = realpath(__DIR__.'/fixtures/page_prepend.php');
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

    public static function pageProvider(): array {
        return [
            [__DIR__.'/../index.php'],
            [__DIR__.'/../historia/historia.php'],
            [__DIR__.'/../museo/galeria.php'],
            [__DIR__.'/../contacto/contacto.php'],
            [__DIR__.'/../secciones_index/memoria_hispanidad.html'],
            [__DIR__.'/../personajes/indice_personajes.html'],
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testFixedHeaderElementsPresent(string $path): void {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            [$status, $out, $err] = $this->runPage($path);
            $this->assertSame(0, $status, $err);
            $content = $out;
        } else {
            $content = file_get_contents($path);
            $this->assertNotFalse($content, "Failed to read $path");
        }
        $this->assertStringContainsString('#fixed-header-elements', $content);
    }
}
?>
