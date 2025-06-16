<?php
use PHPUnit\Framework\TestCase;

class MenuLinksTest extends TestCase {
    public function testMenuLinksTargetsExist(): void {
        $menuPath = __DIR__ . '/../fragments/menus/main-menu.html';
        $this->assertFileExists($menuPath, 'Menu file not found');

        $html = file_get_contents($menuPath);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        $this->assertGreaterThan(0, $links->length, 'No links found in menu');

        $repoRoot = realpath(__DIR__ . '/..');

        foreach ($links as $a) {
            $href = $a->getAttribute('href');
            if ($href === '' || preg_match('/^(https?:|mailto:|tel:|javascript:|#)/i', $href)) {
                continue;
            }

            $clean = parse_url($href, PHP_URL_PATH);
            if (strpos($clean, '/') === 0) {
                $target = $repoRoot . $clean;
            } else {
                $target = dirname($menuPath) . '/' . $clean;
            }
            $this->assertFileExists($target, "Link target missing: $href resolves to $target");
        }
    }
}
