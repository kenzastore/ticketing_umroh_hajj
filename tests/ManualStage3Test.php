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
        $this->assertStringContainsString('# Tahap 3: Penyelesaian Keuangan (Financial Settlement)', $content);
        $this->assertStringContainsString('Pembuatan Proforma Invoice', $content);
        $this->assertStringContainsString('Instruksi Pembayaran Maskapai', $content);
        $this->assertStringContainsString('Titik Verifikasi', $content);
    }
}