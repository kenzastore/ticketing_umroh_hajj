<?php

use PHPUnit\Framework\TestCase;

class UatScenarioRbacTest extends TestCase
{
    public function testRbacScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/07_role_based_access_control.md';
        
        $this->assertFileExists($scenarioPath, "The RBAC scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: Role-Based Access Control', $content);
        $this->assertStringContainsString('**Module:** Security & Access', $content);
        $this->assertStringContainsString('Admin', $content);
        $this->assertStringContainsString('Finance', $content);
        $this->assertStringContainsString('Monitor', $content);
    }
}
