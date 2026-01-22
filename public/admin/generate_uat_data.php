<?php
require_once __DIR__ . '/../../includes/auth.php';

// RBAC Protection: Only Admin can reset UAT data
check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seederPath = __DIR__ . '/../../database/seed_uat_system.php';
    
    // Command execution
    $output = [];
    $returnVar = 0;
    exec("php " . escapeshellarg($seederPath) . " 2>&1", $output, $returnVar);

    if ($returnVar === 0) {
        $_SESSION['success_msg'] = "UAT Data successfully regenerated (100 records).";
    } else {
        $_SESSION['error_msg'] = "Failed to regenerate data: " . implode("\n", $output);
    }
}

header("Location: /shared/manual.php?msg=uat_reset");
exit;

