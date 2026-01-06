<?php
use PHPUnit\Framework\TestCase;

class BookingRequestTableTest extends TestCase
{
    public function testTableStructure()
    {
        $content = file_get_contents(__DIR__ . '/../public/admin/booking_requests.php');

        // Check for required headers based on spec
        $this->assertStringContainsString('FLIGHT LEG 1', $content, 'Table must have Flight Leg 1 header');
        $this->assertStringContainsString('FLIGHT LEG 4', $content, 'Table must have Flight Leg 4 header');
        $this->assertStringContainsString('TCP', $content, 'Table must have TCP header');
        $this->assertStringContainsString('TTL DAYS', $content, 'Table must have TTL DAYS header');
        
        // Check for loop structure handling legs
        $this->assertStringContainsString('$r[\'legs\']', $content, 'Table should iterate or access legs');
    }
}

