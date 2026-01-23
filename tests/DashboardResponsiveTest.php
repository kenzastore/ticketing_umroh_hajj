<?php
use PHPUnit\Framework\TestCase;

class DashboardResponsiveTest extends Tests\TestCase
{
    public function testDashboardContainsRequiredGridClasses()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/dashboard.php');

        // Check for responsive grid classes
        $this->assertStringContainsString('row g-4', $content);
        $this->assertStringContainsString('col-xl-4', $content);
        $this->assertStringContainsString('col-md-6', $content);
    }

    public function testCardsContainButtonsAndDescriptions()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/dashboard.php');

        // Verify specific card elements
        $this->assertStringContainsString('card-description', $content);
        $this->assertStringContainsString('btn btn-primary btn-sm', $content);
        $this->assertStringContainsString('aria-label=', $content);
    }

    public function testNavigationLinksAreCorrect()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/dashboard.php');

        // Check key routing
        $this->assertStringContainsString('href="booking_requests.php"', $content);
        $this->assertStringContainsString('href="movement_fullview.php"', $content);
        $this->assertStringContainsString('href="../finance/dashboard.php"', $content);
    }
}