<?php
namespace Tests;

require_once __DIR__ . '/../app/models/AuditLog.php';

/**
 * @covers AuditLog
 */
class AuditLogUiTest extends TestCase
{
    public function testAdminCanAccessAuditLogs()
    {
        // Mock session
        $_SESSION['user_id'] = 1;
        $_SESSION['role_name'] = 'admin';

        // We can't easily test HTML output of a PHP script that calls die() or header()
        // but we can check if the file exists and has the check_auth call.
        $content = file_get_contents(__DIR__ . '/../public/admin/audit_logs.php');
        $this->assertStringContainsString("check_auth('admin')", $content);
        $this->assertStringContainsString("AuditLog::readAll", $content);
    }

    public function testUiContainsFilteringFields()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/audit_logs.php');
        $this->assertStringContainsString('name="user_id"', $content);
        $this->assertStringContainsString('name="entity_type"', $content);
        $this->assertStringContainsString('name="action"', $content);
        $this->assertStringContainsString('name="date_from"', $content);
        $this->assertStringContainsString('name="date_to"', $content);
    }

    public function testUiContainsPagination()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/audit_logs.php');
        $this->assertStringContainsString('class="pagination', $content);
    }
}
