<?php
use PHPUnit\Framework\TestCase;

class MenuLinksTest extends TestCase {
    public function testMainMenuLinksExist(): void {
        $menuPath = __DIR__ . '/../fragments/menus/main-menu.php';
        $html = file_get_contents($menuPath);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $links = [];
        foreach ($xpath->query('//a[@href]') as $node) {
            $links[] = $node->getAttribute('href');
        }

        foreach ($links as $href) {
            if (str_starts_with($href, 'http://') ||
                str_starts_with($href, 'https://') ||
                str_starts_with($href, 'mailto:') ||
                str_starts_with($href, 'javascript:') ||
                str_starts_with($href, '#')) {
                continue; // ignore external or anchor links
            }
            $path = ltrim($href, '/');
            $path = strtok($path, '#?');
            $fullPath = __DIR__ . '/../' . $path;
            $this->assertFileExists($fullPath, "Link target $href does not exist");
        }
    }
}
