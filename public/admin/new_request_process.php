<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Agent.php';
require_once __DIR__ . '/../../app/models/Corporate.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $corporate_id = !empty($_POST['corporate_id']) ? $_POST['corporate_id'] : null;
    $agent_id = !empty($_POST['agent_id']) ? $_POST['agent_id'] : null;
    
    $corporate_name = null;
    if ($corporate_id) {
        $corp = Corporate::readById($corporate_id);
        $corporate_name = $corp['name'] ?? null;
    }

    $agent_name = null;
    $skyagent_id = null;
    if ($agent_id) {
        $agent = Agent::readById($agent_id);
        $agent_name = $agent['name'] ?? null;
        $skyagent_id = $agent['skyagent_id'] ?? null;
    }

    $header = [
        'request_no' => $_POST['request_no'] ?: null,
        'corporate_id' => $corporate_id,
        'corporate_name' => $corporate_name,
        'agent_id' => $agent_id,
        'agent_name' => $agent_name,
        'skyagent_id' => $skyagent_id,
        'group_size' => $_POST['group_size'],
        'tcp' => $_POST['tcp'] ?: null,
        'selling_fare' => $_POST['selling_fare'] ?: null,
        'nett_fare' => $_POST['nett_fare'] ?: null,
        'duration_days' => $_POST['duration_days'] ?: null,
        'add1_days' => $_POST['add1_days'] ?: null,
        'ttl_days' => $_POST['ttl_days'] ?: null,
        'notes' => $_POST['notes'] ?: null
    ];

    $legs = [];
    if (!empty($_POST['legs'])) {
        foreach ($_POST['legs'] as $index => $leg) {
            // Only add if at least one field is filled
            if (!empty($leg['flight_date']) || !empty($leg['flight_no']) || !empty($leg['sector'])) {
                // Validate Sector Length
                if (!empty($leg['sector']) && strlen($leg['sector']) > 50) {
                    $errorMsg = "Sector in Leg " . ($index + 1) . " is too long (max 50 characters).";
                    header('Location: new_request.php?error=' . urlencode($errorMsg));
                    exit;
                }
                $legs[] = $leg;
            }
        }
    }

    $requestId = BookingRequest::create($header, $legs);

    if ($requestId) {
        header('Location: dashboard.php?success=request_created');
    } else {
        header('Location: new_request.php?error=' . urlencode('Database error: Failed to save booking request.'));
    }
}
