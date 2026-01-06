<?php
use PHPUnit\Framework\TestCase;

class DashboardResponsiveTest extends TestCase
{
    public function testDashboardHasResponsiveClasses()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/dashboard.php');

        // Check for grid classes
        $this->assertStringContainsString('col-md-4', $content, 'Should have medium column definition');
        $this->assertStringContainsString('col-sm-6', $content, 'Should have small column definition');
        $this->assertStringContainsString('col-12', $content, 'Should have full width definition for mobile/headers');
        
        // Check for viewport meta tag in header (indirectly)
        $headerContent = file_get_contents(__DIR__ . '/../public/shared/header.php');
        $this->assertStringContainsString('<meta name="viewport" content="width=device-width, initial-scale=1.0">', $headerContent, 'Header must have viewport meta tag');
        $this->assertStringContainsString('navbar-toggler', $headerContent, 'Header must have navbar toggler for mobile');
    }
}
