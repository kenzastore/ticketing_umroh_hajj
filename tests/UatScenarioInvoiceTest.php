<?php

use PHPUnit\Framework\TestCase;

class UatScenarioInvoiceTest extends TestCase
{
    public function testInvoiceScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/05_invoice_generation.md';
        
        $this->assertFileExists($scenarioPath, "The Invoice Generation scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: External Invoice Generation', $content);
        $this->assertStringContainsString('**Module:** Invoice Generator', $content);
        $this->assertStringNotContainsString('Internal', $content);
        $this->assertStringContainsString('External', $content);
        $this->assertStringContainsString('PDF', $content);
    }
}
