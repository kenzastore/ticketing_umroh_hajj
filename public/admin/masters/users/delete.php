<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/User.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if ($id) {
    // Prevent deleting self (basic check, though frontend hides it too)
    if ($id == $_SESSION['user_id']) {
        header('Location: index.php?error=cannot_delete_self');
        exit;
    }

    if (User::delete($id)) {
        header('Location: index.php?msg=deleted');
    } else {
        header('Location: index.php?error=failed_delete');
    }
} else {
    header('Location: index.php');
}
exit;
