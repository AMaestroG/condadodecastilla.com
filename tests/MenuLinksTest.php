<?php
use PHPUnit\Framework\TestCase;

class MenuLinksTest extends TestCase {
    private string $rootDir;

    protected function setUp(): void {
        $this->rootDir = realpath(__DIR__ . '/..');
        // Include header fragments to ensure they exist
        ob_start();
        include $this->rootDir . '/includes/header.php';
        ob_end_clean();
    }

    private function targetExists(string $href): bool {
        $path = parse_url($href, PHP_URL_PATH) ?: '';
        $full = $this->rootDir . $path;
        if (file_exists($full)) {
            return true;
        }
        if (is_dir($full)) {
            return file_exists($full . '/index.php') || file_exists($full . '/index.html');
        }
        $dir = dirname($full);
        return (
            is_dir($dir) && (
                file_exists($dir . '/index.php') || file_exists($dir . '/index.html')
            )
        );
    }

    public function testMenuLinksTargetsExist(): void {
        $html = file_get_contents($this->rootDir . '/fragments/menus/main-menu.html');
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        foreach ($dom->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');
            if (preg_match('/^(https?:|mailto:)/', $href)) {
                continue;
            }
            $this->assertTrue(
                $this->targetExists($href),
                "Missing target for href: $href"
            );
        }
    }
}
