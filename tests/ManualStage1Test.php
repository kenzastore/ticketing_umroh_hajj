<?php

use PHPUnit\Framework\TestCase;

class ManualStage1Test extends TestCase
{
    public function testStage1FileExists()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/stage1_demand_intake.md');
    }

    public function testStage1ContentIsValid()
    {
        $content = file_get_contents(__DIR__ . '/../docs/manual/stage1_demand_intake.md');
        $this->assertStringContainsString('# Tahap 1: Penerimaan Permintaan (Demand Intake)', $content);
        $this->assertStringContainsString('Booking Request Baru', $content);
        $this->assertStringContainsString('Titik Verifikasi', $content);
    }
}