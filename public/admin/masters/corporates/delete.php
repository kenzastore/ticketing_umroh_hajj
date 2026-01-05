<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Corporate.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if ($id) {
    Corporate::delete($id);
}

header('Location: index.php');
exit;
