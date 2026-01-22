<?php
use PHPUnit\Framework\TestCase;

class PaymentReportUiTest extends Tests\TestCase
{
    public function testUiContainsDualTablesAndRequiredFields()
    {
        $content = file_get_contents(__DIR__ . '/../public/finance/payment_report.php');

        // Check for table titles
        $this->assertStringContainsString('ELANG EMAS WISATA', $content);
        $this->assertStringContainsString('COST / AIRLINE', $content);

        // Check for new columns
        $this->assertStringContainsString('TIME LIMIT PAYMENT BY', $content);
        $this->assertStringContainsString('ACC NO', $content);
        $this->assertStringContainsString('FARE PER-PAX', $content);

        // Check for summary section
        $this->assertStringContainsString('INCENTIVE (Profit)', $content);
        $this->assertStringContainsString('FINAL BALANCE', $content);
    }

    public function testModelIntegration()
    {
        // Verify that the UI uses the new model methods
        $content = file_get_contents(__DIR__ . '/../public/finance/payment_report.php');
        $this->assertStringContainsString("renderPaymentTable('ELANG EMAS WISATA', \$report['sales_lines']", $content);
        $this->assertStringContainsString("renderPaymentTable('COST / AIRLINE', \$report['cost_lines']", $content);
    }
}
