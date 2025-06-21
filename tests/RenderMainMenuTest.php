<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/menu.php';

class RenderMainMenuTest extends TestCase {
    public function testMainMenuHasRoleAttribute(): void {
        ob_start();
        render_main_menu();
        $html = ob_get_clean();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $ul = $dom->getElementById('main-menu');
        $this->assertNotNull($ul, 'Main menu element not found');
        $this->assertSame('nav-links', $ul->getAttribute('class'));
        $this->assertSame('menu', $ul->getAttribute('role'));
    }

    public function testNestedListsHaveSubmenuClass(): void {
        $items = [
            [
                'label' => 'Parent',
                'url' => '#',
                'children' => [
                    ['label' => 'Child', 'url' => '#child']
                ]
            ]
        ];

        ob_start();
        render_menu_items($items);
        $html = ob_get_clean();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $submenus = $xpath->query('//ul[contains(@class, "submenu")]');
        $this->assertGreaterThan(0, $submenus->length, 'No submenu lists found');
        foreach ($submenus as $submenu) {
            $this->assertSame('submenu', $submenu->getAttribute('class'));
        }
    }

    public function testMenuGroupRendered(): void {
        ob_start();
        render_main_menu();
        $html = ob_get_clean();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $groups = $xpath->query('//li[contains(@class, "menu-group")]');
        $this->assertGreaterThan(0, $groups->length, 'No menu group element found');
        $first = $groups->item(0);
        $this->assertStringContainsString(t('group_historia_cultura'), $first->textContent);
    }
}
?>
