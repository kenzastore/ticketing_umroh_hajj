<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/AuditLog.php';

if (isset($_SESSION['user_id'])) {
    AuditLog::log($_SESSION['user_id'], 'LOGOUT', 'user', $_SESSION['user_id'], null, null);
}

session_destroy();
header('Location: login.php');
exit;
