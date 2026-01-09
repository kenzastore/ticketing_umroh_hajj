<?php
namespace Tests;

/**
 * @covers \LoginPage
 */
class LoginPageLayoutTest extends TestCase
{
    public function testLoginUsesSplitScreenWrapper()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('class="login-wrapper"', $content);
        $this->assertStringContainsString('class="info-panel"', $content);
        $this->assertStringContainsString('class="access-panel"', $content);
    }

    public function testLoginIncludesCustomCss()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('href="/assets/css/login.css"', $content);
    }

    public function testLoginContainsWorkflowSection()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('class="workflow-stepper"', $content);
        $this->assertStringContainsString('Demand Intake', $content);
        $this->assertStringContainsString('Financial Settlement', $content);
    }

    public function testLoginContainsDemoCredentials()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('admin_demo', $content);
        $this->assertStringContainsString('op_demo', $content);
        $this->assertStringContainsString('finance_demo', $content);
        $this->assertStringContainsString('monitor_demo', $content);
        $this->assertStringContainsString('password123', $content);
    }

    public function testLoginContainsRoleBadges()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('Staff Operasional', $content);
        $this->assertStringContainsString('Finance Officer', $content);
        $this->assertStringContainsString('Administrator', $content);
    }
}