<?php
use PHPUnit\Framework\TestCase;

class SoundAssetsTest extends TestCase {
    public function testSoundFilesExist(): void {
        $baseDir = realpath(__DIR__ . '/../assets/sounds');
        $this->assertNotFalse($baseDir, 'assets/sounds directory not found');
        $this->assertFileExists($baseDir . '/menu-open.mp3');
        $this->assertFileExists($baseDir . '/menu-close.mp3');
    }
}
?>
