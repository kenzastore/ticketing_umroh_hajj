<?php
use PHPUnit\Framework\TestCase;

class DashboardStructureTest extends TestCase
{
    public function testDashboardContainsRequiredMenuItems()
    {
        $dashboardContent = file_get_contents(__DIR__ . '/../public/admin/dashboard.php');

        $this->assertStringContainsString('Booking Request', $dashboardContent, 'Dashboard must contain Booking Request menu item');
        $this->assertStringContainsString('Movement', $dashboardContent, 'Dashboard must contain Movement menu item');
        $this->assertStringContainsString('Payment Report', $dashboardContent, 'Dashboard must contain Payment Report menu item');
        $this->assertStringContainsString('Invoice', $dashboardContent, 'Dashboard must contain Invoice menu item');
        $this->assertStringContainsString('Rangkuman', $dashboardContent, 'Dashboard must contain Rangkuman menu item');
        $this->assertStringContainsString('Payment Advise', $dashboardContent, 'Dashboard must contain Payment Advise menu item');
        $this->assertStringContainsString('Urgent Deadlines', $dashboardContent, 'Dashboard must contain Time Limit section');
    }
}
