<?php

use PHPUnit\Framework\TestCase;

class UatScenarioPaymentTest extends TestCase
{
    public function testPaymentScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/06_payment_recording_and_reconciliation.md';
        
        $this->assertFileExists($scenarioPath, "The Payment Recording scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: Payment Recording & Reconciliation', $content);
        $this->assertStringContainsString('**Module:** Payment Tracking', $content);
        $this->assertStringContainsString('Payment Report', $content);
        $this->assertStringContainsString('Payment Advise', $content);
        $this->assertStringContainsString('Receipt', $content);
    }
}
