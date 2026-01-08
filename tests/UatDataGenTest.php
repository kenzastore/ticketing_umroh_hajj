<?php
use PHPUnit\Framework\TestCase;

class UatDataGenTest extends TestCase {
    protected function setUp(): void {
        require_once __DIR__ . '/../database/seed_uat_data.php';
    }

    public function testConstantsAreDefined() {
        // This test will fail because the constants are not yet defined
        $this->assertTrue(defined('INDONESIAN_NAMES'));
        $this->assertTrue(defined('INDONESIAN_AGENTS'));
        $this->assertTrue(defined('INDONESIAN_CORPORATES'));
    }
}
