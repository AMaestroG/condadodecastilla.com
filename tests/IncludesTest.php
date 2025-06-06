<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/text_manager.php';

class IncludesTest extends TestCase {
    protected function setUp(): void {
        if (session_status() == PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION = [];
    }

    public function testCsrfToken(): void {
        $token1 = get_csrf_token();
        $this->assertNotEmpty($token1);
        $token2 = get_csrf_token();
        $this->assertSame($token1, $token2);
        $this->assertTrue(verify_csrf_token($token1));
        $this->assertFalse(verify_csrf_token('invalid'));
    }

    public function testIsAdminLoggedIn(): void {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = ADMIN_ROLE;
        $this->assertTrue(is_admin_logged_in());
        $_SESSION['user_role'] = 'user';
        $this->assertFalse(is_admin_logged_in());
    }

    public function testGetTextContentFromDB(): void {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE site_texts (text_id TEXT PRIMARY KEY, text_content TEXT, updated_at TEXT)");
        $result = getTextContentFromDB('hello', $pdo, 'default');
        $this->assertSame('default', $result);
        $stmt = $pdo->prepare('SELECT text_content FROM site_texts WHERE text_id = ?');
        $stmt->execute(['hello']);
        $this->assertSame('default', $stmt->fetchColumn());

        $pdo->prepare('UPDATE site_texts SET text_content = ? WHERE text_id = ?')->execute(['changed','hello']);
        $result = getTextContentFromDB('hello', $pdo, 'default');
        $this->assertSame('changed', $result);
    }
}
