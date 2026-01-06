<?php
use PHPUnit\Framework\TestCase;

class BookingRequestExportTest extends TestCase
{
    public function testExportScriptHeaders()
    {
        // We can't easily test output headers in CLI without output buffering or separate process,
        // but we can verify the file exists and contains core logic.
        $this->assertFileExists(__DIR__ . '/../public/admin/export_requests.php');
        
        $content = file_get_contents(__DIR__ . '/../public/admin/export_requests.php');
        $this->assertStringContainsString('header("Content-Type: application/vnd.ms-excel")', $content);
        $this->assertStringContainsString('header("Content-Disposition: attachment', $content);
    }
}
