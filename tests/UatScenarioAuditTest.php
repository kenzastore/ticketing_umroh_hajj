<?php

use PHPUnit\Framework\TestCase;

class UatScenarioAuditTest extends TestCase
{
    public function testAuditScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/08_audit_logging.md';
        
        $this->assertFileExists($scenarioPath, "The Audit Logging scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: Audit Logging', $content);
        $this->assertStringContainsString('**Module:** Security & Audit', $content);
        $this->assertStringContainsString('Action', $content);
        $this->assertStringContainsString('Timestamp', $content);
    }
}
