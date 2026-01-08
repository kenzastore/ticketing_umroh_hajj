<?php

use PHPUnit\Framework\TestCase;

class UatTemplateTest extends TestCase
{
    public function testScenarioTemplateExistsAndIsValid()
    {
        $templatePath = __DIR__ . '/../conductor/uat/templates/scenario_template.md';
        
        $this->assertFileExists($templatePath, "The UAT scenario template should exist.");
        
        $content = file_get_contents($templatePath);
        
        $this->assertStringContainsString('## Scenario ID:', $content);
        $this->assertStringContainsString('## Description', $content);
        $this->assertStringContainsString('## Prerequisites', $content);
        $this->assertStringContainsString('## Test Steps', $content);
        $this->assertStringContainsString('## Expected Result', $content);
        $this->assertStringContainsString('| Step | Action | Expected Outcome |', $content);
    }
}
