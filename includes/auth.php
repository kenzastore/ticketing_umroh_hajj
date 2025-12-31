<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function check_auth($required_role = null) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }

    if ($required_role && $_SESSION['role_name'] !== $required_role) {
        die("Unauthorized access.");
    }
}
