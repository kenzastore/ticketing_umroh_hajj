<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Agent.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if ($id) {
    if (Agent::delete($id)) {
        header('Location: index.php?msg=deleted');
    } else {
        header('Location: index.php?error=failed_delete');
    }
} else {
    header('Location: index.php');
}
exit;
