<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ManualStage2Test extends TestCase
{
    public function testStage2FileExists()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/stage2_operational_execution.md');
    }

    public function testStage2ContentIsValid()
    {
        $content = file_get_contents(__DIR__ . '/../docs/manual/stage2_operational_execution.md');
        $this->assertStringContainsString('# Tahap 2: Eksekusi Operasional (Operational Execution)', $content);
        $this->assertStringContainsString('Konversi Request ke Movement', $content);
        $this->assertStringContainsString('PNR', $content);
        $this->assertStringContainsString('Titik Verifikasi', $content);
    }
}