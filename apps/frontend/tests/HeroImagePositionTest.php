<?php
use PHPUnit\Framework\TestCase;

class HeroImagePositionTest extends TestCase {
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
            [__DIR__.'/../empresa/index.php'],
            [__DIR__.'/../camino_santiago/camino_santiago.php']
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testHeroImagesHavePositionClasses(string $path): void {
        [$status, $out, $err] = $this->runPage($path);
        $this->assertSame(0, $status, $err);
        $this->assertStringContainsString('object-center', $out);
        $this->assertStringContainsString('md:object-top', $out);
    }
}
?>
