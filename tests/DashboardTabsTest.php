<?php
use PHPUnit\Framework\TestCase;

class DashboardTabsTest extends TestCase {
    public function testTabStructure() {
        $html = file_get_contents(__DIR__ . '/../public/admin/dashboard.php');
        
        // Check for nav-tabs
        $this->assertStringContainsString('id="deadlineTabs"', $html);
        
        // Check for matching targets and panes
        $categories = ['ticketing', 'dp1', 'dp2', 'fp'];
        foreach ($categories as $cat) {
            $this->assertStringContainsString('data-bs-target="#' . $cat . '"', $html, "Missing target for $cat");
            $this->assertStringContainsString('id="' . $cat . '"', $html, "Missing pane for $cat");
        }
        
        // Check for data-bs-toggle
        $this->assertStringContainsString('data-bs-toggle="tab"', $html);
    }
}
