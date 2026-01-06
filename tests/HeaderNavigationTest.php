<?php
use PHPUnit\Framework\TestCase;

class HeaderNavigationTest extends TestCase
{
    public function testHeaderContainsNewNavigationLinks()
    {
        $headerContent = file_get_contents(__DIR__ . '/../public/shared/header.php');

        $this->assertStringContainsString('href="/admin/dashboard.php"', $headerContent, 'Header must link to Dashboard');
        $this->assertStringContainsString('href="/admin/booking_requests.php"', $headerContent, 'Header must link to Booking Requests');
        $this->assertStringContainsString('href="/admin/movement_fullview.php"', $headerContent, 'Header must link to Movement/Flight Monitor');
        // Check for Finance dropdown items if we decide to structure it that way, or just check for existence of links
        $this->assertStringContainsString('href="/finance/dashboard.php"', $headerContent, 'Header must link to Finance Dashboard');
        // Invoices might be under finance dashboard or separate
    }
}
