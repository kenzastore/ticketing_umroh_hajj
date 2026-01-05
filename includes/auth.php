<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    function check_auth($required_role = null) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        if ($required_role) {
            $user_role = $_SESSION['role_name'] ?? null;
            if (is_array($required_role)) {
                if (!in_array($user_role, $required_role)) {
                    die("Unauthorized access.");
                }
            } elseif ($user_role !== $required_role) {
                die("Unauthorized access.");
            }
        }
    }
