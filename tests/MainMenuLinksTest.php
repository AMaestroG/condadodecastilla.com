<?php
use PHPUnit\Framework\TestCase;

class MainMenuLinksTest extends TestCase {
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

    public static function urlProvider(): array {
        ob_start();
        include __DIR__ . '/../fragments/menus/main-menu.php';
        $html = ob_get_clean();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $urls = [];
        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if ($href !== '') {
                if ($href[0] !== '/') {
                    $href = '/' . $href;
                }
                $urls[] = [$href];
            }
        }
        return $urls;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testLinkLoads(string $href): void {
        $path = __DIR__.'/..'.$href;
        if (is_dir($path)) {
            $path .= '/index.php';
        }
        $this->assertFileExists($path, "Missing file for $href");
        if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            [$status, $out, $err] = $this->runPage($path);
            $this->assertSame(0, $status, $err);
            $this->assertNotEmpty($out);
        } else {
            $content = file_get_contents($path);
            $this->assertNotFalse($content, "Failed to read $path");
        }
    }
}
?>
