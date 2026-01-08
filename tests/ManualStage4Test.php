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
        $this->assertStringContainsString('# Tahap 4: Manajemen & Audit', $content);
        $this->assertStringContainsString('## Pendahuluan', $content);
        $this->assertStringContainsString('Administrator', $content);
        $this->assertStringContainsString('Manajemen Pengguna', $content);
        $this->assertStringContainsString('Log Audit', $content);
    }
}
