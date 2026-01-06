<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Basic authentication check
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'login_process.php'];

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Notification.php';

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $unreadNotifs = Notification::getUnread();
    $unreadCount = count($unreadNotifs);
}

// Placeholder for dynamic title or metadata
$pageTitle = "Ticketing Umroh Hajj";
if (isset($title)) {
    $pageTitle = $title . " | " . $pageTitle;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css"> 
</head>
<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="/admin/dashboard.php">EEMW Ticketing</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role_name'] == 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/dashboard.php">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/booking_requests.php">Requests</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/movement_fullview.php">Movements</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/finance/dashboard.php">Finance</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="masterDropdown" data-bs-toggle="dropdown">
                                        Masters
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="/admin/masters/agents/index.php">Agents</a></li>
                                        <li><a class="dropdown-item" href="/admin/masters/corporates/index.php">Corporates</a></li>
                                        <li><a class="dropdown-item" href="/admin/masters/users/index.php">Users</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/new_request.php">New Request</a>
                                </li>
                            <?php elseif ($_SESSION['role_name'] == 'finance'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/finance/dashboard.php">Finance Dashboard</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-center">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Notifications -->
                            <li class="nav-item me-3">
                                <a href="/admin/notifications.php" class="nav-link position-relative">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($unreadCount > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                            <?= $unreadCount ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link text-light small me-2">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-outline-light btn-sm px-3" href="/logout.php">Logout</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container mt-4">