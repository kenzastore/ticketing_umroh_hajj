<?php

use PHPUnit\Framework\TestCase;

class ManualStage3Test extends TestCase
{
    public function testStage3FileExists()
    {
        $this->assertFileExists(__DIR__ . '/../docs/manual/stage3_financial_settlement.md');
    }

    public function testStage3ContentIsValid()
    {
        $content = file_get_contents(__DIR__ . '/../docs/manual/stage3_financial_settlement.md');
        $this->assertStringContainsString('# Tahap 3: Penyelesaian Keuangan (Invoicing & Payments)', $content);
        $this->assertStringContainsString('## Pendahuluan', $content);
        $this->assertStringContainsString('Finance Officer', $content);
        $this->assertStringContainsString('Pembuatan Invoice (External)', $content);
        $this->assertStringContainsString('Pembuatan Payment Advice (Internal)', $content);
    }
}
