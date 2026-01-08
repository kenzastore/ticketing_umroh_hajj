<?php

use PHPUnit\Framework\TestCase;

class UatMasterPlanTest extends TestCase
{
    public function testMasterPlanExistsAndIsValid()
    {
        $planPath = __DIR__ . '/../conductor/uat/uat_master_plan.md';
        
        $this->assertFileExists($planPath, "The UAT Master Plan should exist.");
        
        $content = file_get_contents($planPath);
        
        $this->assertStringContainsString('# UAT Master Plan: Ticketing Umroh & Haji', $content);
        $this->assertStringContainsString('## Introduction', $content);
        $this->assertStringContainsString('## Execution Instructions', $content);
        $this->assertStringContainsString('## Test Scenarios Index', $content);
        $this->assertStringContainsString('01_new_booking_request.md', $content);
        $this->assertStringContainsString('08_audit_logging.md', $content);
    }
}
