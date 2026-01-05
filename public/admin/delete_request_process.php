<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        if (BookingRequest::delete($id)) {
            header('Location: dashboard.php?message=deleted');
            exit;
        }
    }
    
    header('Location: dashboard.php?error=delete_failed');
    exit;
}

header('Location: dashboard.php');
exit;
