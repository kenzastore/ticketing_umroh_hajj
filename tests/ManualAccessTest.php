<?php
namespace Tests;

/**
 * @coversNothing
 */
class ManualAccessTest extends TestCase
{
    public function testManualPageIsProtectedByCheckAuth()
    {
        $content = file_get_contents(__DIR__ . '/../public/shared/manual.php');
        $this->assertStringContainsString("check_auth('admin')", $content);
    }

    public function testManualRequiresAuthAndDbConnect()
    {
        $content = file_get_contents(__DIR__ . '/../public/shared/manual.php');
        $this->assertStringContainsString("require_once __DIR__ . '/../../includes/auth.php'", $content);
        $this->assertStringContainsString("require_once __DIR__ . '/../../includes/db_connect.php'", $content);
    }
}
