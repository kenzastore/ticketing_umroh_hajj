<?php
session_start();

function check_auth($required_role = null) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /public/login.php');
        exit;
    }

    if ($required_role && $_SESSION['role'] !== $required_role) {
        die("Unauthorized access.");
    }
}
