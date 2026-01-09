<?php
namespace Tests;

/**
 * @covers \LoginPage
 */
class LoginPageLayoutTest extends TestCase
{
    public function testLoginUsesCenteredCardLayout()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('class="login-container"', $content);
        $this->assertStringContainsString('class="minimal-card card shadow-lg"', $content);
    }

    public function testLoginIncludesMinimalCss()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('href="/assets/css/minimal-login.css"', $content);
    }

    public function testLoginContainsWorkflowAccordion()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('id="workflowAccordion"', $content);
        $this->assertStringContainsString('How the system works', $content);
        $this->assertStringContainsString('Demand Intake', $content);
    }

    public function testLoginContainsDemoCredentialsFooter()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('class="demo-footer"', $content);
        $this->assertStringContainsString('admin_demo', $content);
        $this->assertStringContainsString('password123', $content);
    }

    public function testLoginContainsPasswordToggle()
    {
        $content = file_get_contents(__DIR__ . '/../public/login.php');
        $this->assertStringContainsString('togglePassword()', $content);
        $this->assertStringContainsString('id="toggleIcon"', $content);
    }
}
