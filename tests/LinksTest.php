<?php
use PHPUnit\Framework\TestCase;

class LinksTest extends TestCase {
    private string $root;

    protected function setUp(): void {
        $this->root = realpath(__DIR__ . '/..');
    }

    /**
     * Parse an HTML file and return internal href values.
     */
    private function collectLinks(string $file): array {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(file_get_contents($file));
        libxml_clear_errors();
        $links = [];
        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if ($href === '' || $href[0] === '#') {
                continue;
            }
            if (preg_match('#^(https?:|mailto:|tel:|javascript:)#i', $href)) {
                continue;
            }
            $links[] = $href;
        }
        return $links;
    }

    /**
     * Determine if a file exists for a given href.
     */
    private function linkTargetExists(string $href): bool {
        $path = preg_replace('/[?#].*/', '', $href);
        if ($path === '') {
            return true;
        }
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }
        $abs = $this->root . '/' . $path;
        if (file_exists($abs)) {
            return true;
        }
        if (file_exists($abs . '.php') || file_exists($abs . '.html')) {
            return true;
        }
        if (is_dir($abs)) {
            return file_exists($abs . '/index.php') || file_exists($abs . '/index.html');
        }
        return false;
    }

    public function testLinksExist(): void {
        $files = [
            __DIR__ . '/../fragments/menus/main-menu.php',
            __DIR__ . '/../_header.php',
        ];
        foreach ($files as $file) {
            foreach ($this->collectLinks($file) as $href) {
                $this->assertTrue(
                    $this->linkTargetExists($href),
                    "Missing target for link $href in $file"
                );
            }
        }
    }

    public function testLinkCheckerScript(): void {
        $cmd = 'bash ' . escapeshellarg(__DIR__ . '/../check_links.sh');
        $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        $this->assertSame(0, $status, $out . $err);
        $report = file_get_contents(__DIR__ . '/../broken_links_report.txt');
        $this->assertStringNotContainsString('BROKEN', $report, $report);
    }
}
