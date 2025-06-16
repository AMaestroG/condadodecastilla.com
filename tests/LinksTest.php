<?php
use PHPUnit\Framework\TestCase;

class LinksTest extends TestCase {
    private function collectInternalLinks(string $file): array {
        $html = file_get_contents($file);
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $links = [];
        foreach ($doc->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if ($href === '') continue;
            if (str_starts_with($href, 'http') ||
                str_starts_with($href, 'mailto:') ||
                str_starts_with($href, 'tel:') ||
                str_starts_with($href, '#') ||
                str_starts_with($href, 'javascript:')) {
                continue;
            }
            $links[] = $href;
        }
        return $links;
    }

    private function resolvePath(string $href, string $baseDir): string {
        $path = strtok($href, "?#");
        if ($path === false) {
            $path = $href;
        }
        if (str_starts_with($path, '/')) {
            $resolved = ltrim($path, '/');
        } else {
            $resolved = rtrim($baseDir, '/').'/'.$path;
        }
        $resolved = preg_replace('#\./#', '', $resolved);
        return $resolved;
    }

    public function fileProvider(): array {
        return [
            [__DIR__.'/../fragments/menus/main-menu.html'],
            [__DIR__.'/../_header.html'],
        ];
    }

    /**
     * @dataProvider fileProvider
     */
    public function testInternalLinksExist(string $file): void {
        $baseDir = dirname($file);
        $links = $this->collectInternalLinks($file);
        foreach ($links as $href) {
            $path = $this->resolvePath($href, $baseDir);
            $exists = file_exists($path);
            if (!$exists) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $noExt = $ext ? substr($path, 0, -(strlen($ext)+1)) : $path;
                if ($ext !== 'php') {
                    $exists = file_exists($noExt.'.php');
                }
                if (!$exists && $ext !== 'html') {
                    $exists = file_exists($noExt.'.html');
                }
                if (!$exists && $ext === '') {
                    $exists = is_dir($path) && (file_exists($path.'/index.php') || file_exists($path.'/index.html'));
                }
            }
            $this->assertTrue($exists, "Missing file for href '$href' resolved to '$path'");
        }
    }

    public function testLinkCheckerScript(): void {
        $cmd = 'bash '.escapeshellarg(__DIR__.'/../check_links_extended.sh');
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        $this->assertSame(0, $status, $err);
        $this->assertStringNotContainsString("\n  BROKEN", $out, $out);
    }
}
