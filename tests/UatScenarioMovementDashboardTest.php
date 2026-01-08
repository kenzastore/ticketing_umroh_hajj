<?php

use PHPUnit\Framework\TestCase;

class UatScenarioMovementDashboardTest extends TestCase
{
    public function testMovementDashboardScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/03_movement_dashboard_and_fullview.md';
        
        $this->assertFileExists($scenarioPath, "The Movement Dashboard scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: Movement Dashboard & FullView', $content);
        $this->assertStringContainsString('**Module:** Movement Monitoring', $content);
        $this->assertStringContainsString('FullView', $content);
        $this->assertStringContainsString('filtering', $content);
    }
}
