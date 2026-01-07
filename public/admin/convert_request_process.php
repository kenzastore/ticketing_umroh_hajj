<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = $_POST['booking_request_id'] ?? null;
    $category = $_POST['category'] ?? 'UMRAH';
    $pnr = $_POST['pnr'] ?? '';
    $tour_code = $_POST['tour_code'] ?? '';
    $carrier = $_POST['carrier'] ?? '';

    if (!$requestId || empty($pnr) || empty($tour_code) || empty($carrier)) {
        header('Location: convert_request.php?id=' . $requestId . '&error=missing_fields');
        exit;
    }

    $r = BookingRequest::readById($requestId);
    if (!$r) {
        header('Location: dashboard.php?error=request_not_found');
        exit;
    }

    // Prepare Movement Data
    $movementData = [
        'category' => $category,
        'agent_id' => $r['agent_id'],
        'agent_name' => $r['agent_name'],
        'created_date' => date('Y-m-d'),
        'pnr' => strtoupper($pnr),
        'tour_code' => $tour_code,
        'carrier' => $carrier,
        'passenger_count' => $r['group_size'],
        'approved_fare' => $r['gp_approved_fare'],
        'selling_fare' => $r['selling_fare'],
        'nett_selling' => $r['nett_fare'],
        'total_selling' => $r['group_size'] * $r['selling_fare'],
        'duration_days' => $r['duration_days'],
        'add1_days' => $r['add1_days'],
        'ttl_days' => $r['ttl_days']
    ];

    // Map legs to movement summary columns (Supporting up to 4 legs)
    if (!empty($r['legs'])) {
        foreach ($r['legs'] as $index => $leg) {
            $legNo = $index + 1;
            if ($legNo === 1) {
                $movementData['flight_no_out'] = $leg['flight_no'];
                $movementData['sector_out'] = $leg['sector'];
                $movementData['dep_seg1_date'] = $leg['flight_date'];
            } elseif ($legNo === 2) {
                $movementData['dep_seg2_date'] = $leg['flight_date'];
            } elseif ($legNo === 3) {
                $movementData['arr_seg3_date'] = $leg['flight_date'];
            } elseif ($legNo === 4) {
                $movementData['flight_no_in'] = $leg['flight_no'];
                $movementData['sector_in'] = $leg['sector'];
                $movementData['arr_seg4_date'] = $leg['flight_date'];
            }
        }
        
        // Safety: If only 2 legs, ensure the second leg is treated as the return (ARR4)
        if (count($r['legs']) === 2) {
            $lastLeg = $r['legs'][1];
            $movementData['flight_no_in'] = $lastLeg['flight_no'];
            $movementData['sector_in'] = $lastLeg['sector'];
            $movementData['arr_seg4_date'] = $lastLeg['flight_date'];
            unset($movementData['dep_seg2_date']); // Ensure DEP2 is empty if it was set
        }
    }

    $movementId = Movement::create($movementData, $r['legs'] ?? [], $_SESSION['user_id']);

    if ($movementId) {
        // Mark the booking request as converted to avoid redundancy in the main list
        $stmtUpdate = $pdo->prepare("UPDATE booking_requests SET is_converted = 1 WHERE id = ?");
        $stmtUpdate->execute([$requestId]);
        
        header('Location: movement_fullview.php?msg=converted&pnr=' . urlencode($pnr));
    } else {
        header('Location: convert_request.php?id=' . $requestId . '&error=conversion_failed');
    }
} else {
    header('Location: dashboard.php');
}
exit;
