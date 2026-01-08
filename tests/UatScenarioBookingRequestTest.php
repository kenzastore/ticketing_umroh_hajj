<?php

use PHPUnit\Framework\TestCase;

class UatScenarioBookingRequestTest extends TestCase
{
    public function testBookingRequestScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/01_new_booking_request.md';
        
        $this->assertFileExists($scenarioPath, "The New Booking Request scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: New Booking Request', $content);
        $this->assertStringContainsString('**Module:** Request Management', $content);
        $this->assertStringContainsString('multi-segment flight', $content);
        $this->assertStringContainsString('Validation', $content);
    }
}
