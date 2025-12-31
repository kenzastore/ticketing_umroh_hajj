<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    $new_pnr = trim($_POST['pnr_code'] ?? '');

    // Automated transition if PNR is provided
    if (!empty($new_pnr)) {
        $new_status = 'PNR_ISSUED';
    }

    try {
        $pdo->beginTransaction();

        // Get old values for logging
        $stmt = $pdo->prepare("SELECT status, pnr_code FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $old = $stmt->fetch();

        // Update booking
        $stmt = $pdo->prepare("UPDATE bookings SET status = ?, pnr_code = ? WHERE id = ?");
        $stmt->execute([$new_status, $new_pnr ?: null, $booking_id]);

        // Log status change if it differs
        if ($old['status'] !== $new_status) {
            $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], 'UPDATE_STATUS', 'booking', $booking_id, $old['status'], $new_status]);
        }

        // Log PNR change if it differs
        if (($old['pnr_code'] ?? '') !== $new_pnr) {
            $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], 'UPDATE_PNR', 'booking', $booking_id, $old['pnr_code'], $new_pnr]);
        }

        $pdo->commit();
        header("Location: request_detail.php?id=$request_id&success=1");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error updating status: " . $e->getMessage());
    }
}