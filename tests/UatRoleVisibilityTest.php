<?php
use PHPUnit\Framework\TestCase;

class UatRoleVisibilityTest extends Tests\TestCase
{
    public function testRolePermissionsExist()
    {
        $stmt = self::$pdo->query("SELECT name FROM roles");
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $this->assertContains('admin', $roles);
        $this->assertContains('finance', $roles);
        $this->assertContains('monitor', $roles);
    }

    public function testFinanceDashboardAccessibility()
    {
        // Check if finance dashboard logic is accessible via code presence
        $this->assertFileExists(__DIR__ . '/../public/finance/dashboard.php');
        $content = file_get_contents(__DIR__ . '/../public/finance/dashboard.php');
        $this->assertStringContainsString("check_auth(['finance', 'admin'])", $content);
    }

    public function testMonitorDashboardAccessibility()
    {
        $this->assertFileExists(__DIR__ . '/../public/admin/movement_fullview.php');
        $content = file_get_contents(__DIR__ . '/../public/admin/movement_fullview.php');
        $this->assertStringContainsString("check_auth(['admin', 'monitor'])", $content);
    }
}
