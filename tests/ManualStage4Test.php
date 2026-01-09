<?php

use PHPUnit\Framework\TestCase;

class ManualStage4Test extends TestCase
{
    public function testStage4FileExists()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/stage4_management_audit.md');
    }

    public function testStage4ContentIsValid()
    {
        $content = file_get_contents(__DIR__ . '/../docs/manual/stage4_management_audit.md');
        $this->assertStringContainsString('# Tahap 4: Manajemen & Audit (Management & Audit)', $content);
        $this->assertStringContainsString('Pemantauan Batas Waktu', $content);
        $this->assertStringContainsString('Peninjauan Log Audit', $content);
        $this->assertStringContainsString('Titik Verifikasi', $content);
    }
}