<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Notification.php';

check_auth(['admin', 'finance']);

$action = $_REQUEST['action'] ?? '';
$redirect = $_SERVER['HTTP_REFERER'] ?? 'notifications.php';

switch ($action) {
    case 'mark_read':
        $id = $_REQUEST['id'] ?? 0;
        if ($id) {
            Notification::markAsRead($id);
        }
        break;

    case 'mark_all_read':
        Notification::markAllAsRead();
        break;

    default:
        // No action
        break;
}

header("Location: $redirect");
exit;
