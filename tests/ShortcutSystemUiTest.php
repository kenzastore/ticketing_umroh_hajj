<?php
use PHPUnit\Framework\TestCase;

class ShortcutSystemUiTest extends TestCase
{
    public function testFooterContainsShortcutSystemScript()
    {
        $footerContent = file_get_contents(__DIR__ . '/../public/shared/footer.php');
        $this->assertStringContainsString('shortcut-system.js', $footerContent);
        $this->assertStringContainsString('ShortcutSystem.init()', $footerContent);
    }

    public function testFooterContainsShortcutFloatingIcon()
    {
        $footerContent = file_get_contents(__DIR__ . '/../public/shared/footer.php');
        $this->assertStringContainsString('id="shortcut-cue"', $footerContent);
    }
}
