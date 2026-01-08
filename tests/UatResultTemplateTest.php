<?php

use PHPUnit\Framework\TestCase;

class UatResultTemplateTest extends TestCase
{
    public function testResultTemplateExistsAndIsValid()
    {
        $templatePath = __DIR__ . '/../conductor/uat/templates/result_template.md';
        
        $this->assertFileExists($templatePath, "The UAT result template should exist.");
        
        $content = file_get_contents($templatePath);
        
        $this->assertStringContainsString('# UAT Execution Log', $content);
        $this->assertStringContainsString('**Tester Name:**', $content);
        $this->assertStringContainsString('**Date:**', $content);
        $this->assertStringContainsString('| Step | Status (Pass/Fail) | Actual Result | Comments/Defects |', $content);
        $this->assertStringContainsString('## Overall Status', $content);
        $this->assertStringContainsString('## Sign-off', $content);
    }
}
