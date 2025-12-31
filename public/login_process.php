<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header('Location: login.php?error=empty_fields');
        exit;
    }

    $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['full_name'] = $user['full_name'];

        // Redirect based on role
        switch ($user['role_name']) {
            case 'admin':
                header('Location: admin/dashboard.php');
                break;
            case 'finance':
                header('Location: finance/dashboard.php');
                break;
            case 'monitor':
                header('Location: monitor/dashboard.php');
                break;
            default:
                header('Location: index.php');
        }
        exit;
    } else {
        header('Location: login.php?error=invalid_credentials');
        exit;
    }
}
