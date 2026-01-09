<?php
namespace Tests;

require_once __DIR__ . '/../app/models/AuditLog.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';

/**
 * @covers \AuditLog
 */
class AuditLogE2ETest extends TestCase
{
    public function testEndToEndAuditLogging()
    {
        // 1. Setup Actor
        // Create a unique user to act as the admin
        $username = 'e2e_admin_' . time();
        $userId = \User::create([
            'username' => $username, 
            'password' => 'pass', 
            'role_id' => 1, 
            'full_name' => 'E2E Admin'
        ]);
        $this->assertNotFalse($userId, "Failed to create E2E user");
        
        // Mock Session
        $_SESSION['user_id'] = $userId;
        \AuditLog::setCurrentUser($userId);

        // 2. Perform Actions
        
        // A. Create Booking Request
        $brId = \BookingRequest::create(
            ['corporate_name' => 'E2E Corp', 'group_size' => 5], 
            [], 
            $userId
        );
        $this->assertNotFalse($brId, "Failed to create Booking Request");

        // B. Create Movement (Simulate Conversion)
        // Note: conversion script does this + logs manually. 
        // We will just test Movement::create which logs automatically.
        // We'll also manually log the conversion event to match the process.
        $mvId = \Movement::create(['pnr' => 'E2E-PNR', 'category' => 'UMRAH'], [], $userId);
        $this->assertNotFalse($mvId, "Failed to create Movement");
        
        \AuditLog::log($userId, 'CONVERTED_TO_MOVEMENT', 'booking_request', $brId, null, ['movement_id' => $mvId]);

        // C. Update Movement
        \Movement::update($mvId, ['tour_code' => 'TC-E2E'], $userId);

        // D. Create Invoice
        $invId = \Invoice::create(['invoice_no' => 'INV-E2E', 'amount_idr' => 5000], [], [], $userId);
        $this->assertNotFalse($invId, "Failed to create Invoice");

        // E. Record Payment
        $payId = \Payment::create([
            'invoice_id' => $invId,
            'amount_paid' => 5000,
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'CASH'
        ]);
        $this->assertNotFalse($payId, "Failed to record Payment");


        // 3. Verify Logs via AuditLog::readAll (Data Source for UI)
        
        // Filter for this user to avoid noise
        $logs = \AuditLog::readAll(100, 0, ['user_id' => $userId]);
        
        $this->assertGreaterThanOrEqual(5, count($logs), "Expected at least 5 logs");

        // We expect logs in reverse chronological order (newest first)
        // Actions: PAYMENT_RECORDED, CREATE (Invoice), UPDATE (Movement), CONVERTED..., CREATE (Movement), CREATE (BookingRequest), USER_CREATED
        
        $actionsFound = array_column($logs, 'action');
        
        $this->assertContains('PAYMENT_RECORDED', $actionsFound);
        $this->assertContains('CREATE', $actionsFound); // Multiple
        $this->assertContains('UPDATE', $actionsFound);
        $this->assertContains('CONVERTED_TO_MOVEMENT', $actionsFound);
        // USER_CREATED is logged with user_id=NULL (System) so it won't appear in this filtered list
        // $this->assertContains('USER_CREATED', $actionsFound); 
        
        // Let's check specifically for the PAYMENT log
        $payLog = null;
        foreach ($logs as $log) {
            if ($log['action'] === 'PAYMENT_RECORDED' && $log['entity_id'] == $payId) {
                $payLog = $log;
                break;
            }
        }
        $this->assertNotNull($payLog, "Payment Log not found");
        $payVal = json_decode($payLog['new_value'], true);
        $this->assertEquals(5000, $payVal['amount_paid']);
        $this->assertEquals('CASH', $payVal['payment_method']);

        // Check Movement Update Log (Old vs New)
        $mvLog = null;
        foreach ($logs as $log) {
            if ($log['action'] === 'UPDATE' && $log['entity_type'] === 'movement' && $log['entity_id'] == $mvId) {
                $mvLog = $log;
                break;
            }
        }
        $this->assertNotNull($mvLog, "Movement Update Log not found");
        $oldMv = json_decode($mvLog['old_value'], true);
        $newMv = json_decode($mvLog['new_value'], true);
        
        $this->assertNotEquals('TC-E2E', $oldMv['tour_code'] ?? ''); // Should be null or previous default
        $this->assertEquals('TC-E2E', $newMv['tour_code']);
    }
}
