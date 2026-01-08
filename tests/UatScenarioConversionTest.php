<?php

use PHPUnit\Framework\TestCase;

class UatScenarioConversionTest extends TestCase
{
    public function testConversionScenarioExistsAndIsValid()
    {
        $scenarioPath = __DIR__ . '/../conductor/uat/scenarios/02_request_to_movement_conversion.md';
        
        $this->assertFileExists($scenarioPath, "The Request to Movement Conversion scenario should exist.");
        
        $content = file_get_contents($scenarioPath);
        
        $this->assertStringContainsString('# UAT Scenario: Request to Movement Conversion', $content);
        $this->assertStringContainsString('**Module:** Movement Monitoring', $content);
        $this->assertStringContainsString('PNR', $content);
        $this->assertStringContainsString('Tour Code', $content);
    }
}
