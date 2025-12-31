<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $pnr_code = trim($_POST['pnr_code'] ?? '');

    // Automated transition if PNR is provided
    if (!empty($pnr_code)) {
        $status = 'PNR_ISSUED';
    }

    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ?, pnr_code = ? WHERE id = ?");
        $stmt->execute([$status, $pnr_code ?: null, $booking_id]);

        header("Location: request_detail.php?id=$request_id&success=1");
    } catch (Exception $e) {
        die("Error updating status: " . $e->getMessage());
    }
}
