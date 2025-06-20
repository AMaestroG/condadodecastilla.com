<?php

use PHPUnit\Framework\TestCase;

class BlendClassTest extends TestCase
{
    private function runPage(string $script): array
    {
        $prepend = realpath(__DIR__.'/fixtures/page_prepend.php');
        $cmd = sprintf(
            'php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env = [
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script
        ];
        $proc = proc_open($cmd, [1 => ['pipe','w'], 2 => ['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    public static function pageProvider(): array
    {
        return [
            [__DIR__.'/../ruinas/index.php', ['class="blend-overlay"']],
            [__DIR__.'/../galeria/galeria_colaborativa.php', ['class="blend-overlay"', 'class="blend-screen"']],
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testBlendClassesPresent(string $page, array $expected): void
    {
        [$status, $out, $err] = $this->runPage($page);
        $this->assertSame(0, $status, $err);
        foreach ($expected as $class) {
            $this->assertStringContainsString($class, $out);
        }
    }
}
