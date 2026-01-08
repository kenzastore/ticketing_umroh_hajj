<?php

use PHPUnit\Framework\TestCase;

class UatScenarioRemindersTest extends TestCase
{
    public function testRemindersScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/04_time_limit_reminders.md';
        
        $this->assertFileExists($scenarioPath, "The Time Limit Reminders scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: Time Limit Reminders', $content);
        $this->assertStringContainsString('**Module:** Notification System', $content);
        $this->assertStringContainsString('H-3', $content);
        $this->assertStringContainsString('Time Limit', $content);
    }
}
