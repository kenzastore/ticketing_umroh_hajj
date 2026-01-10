<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        header('Location: movement_fullview.php');
        exit;
    }

    // List of allowed fields to update from the form
    $fields = [
        'pnr', 'category', 'tour_code', 'agent_name', 'carrier', 'passenger_count',
        'dp1_status', 'dp2_status', 'fp_status', 'ticketing_done',
        'flight_no_out', 'sector_out', 'dep_seg1_date', 'ticketing_deadline',
        'belonging_to', 'duration_days', 'add1_days', 'ttl_days',
        'nett_balance_amount', 'sell_balance_amount',
        'deposit1_airlines_amount', 'deposit1_airlines_date', 'deposit1_eemw_date',
        'deposit2_airlines_amount', 'deposit2_airlines_date', 'deposit2_eemw_date',
        'fullpay_airlines_date', 'fullpay_eemw_date'
    ];

    $data = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $val = $_POST[$field];
            // Convert empty strings to null for optional database fields
            $data[$field] = ($val === '') ? null : $val;
        }
    }

    // Special handling for checkbox
    $data['ticketing_done'] = isset($_POST['ticketing_done']) ? 1 : 0;

    error_log("Updating Movement ID $id with data: " . json_encode($data));
    
    if (Movement::update($id, $data, $_SESSION['user_id'])) {
        error_log("Update successful for ID $id");
        header('Location: movement_fullview.php?msg=updated&pnr=' . urlencode($data['pnr'] ?? ''));
    } else {
        $error = Movement::getLastError() ?: 'update_failed';
        error_log("Update failed for ID $id: $error");
        header('Location: edit_movement.php?id=' . $id . '&error=' . urlencode($error));
    }
} else {
    header('Location: movement_fullview.php');
}
exit;
