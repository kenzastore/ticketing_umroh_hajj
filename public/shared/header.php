<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Basic authentication check
// Redirect to login page if user is not logged in, except for login pages themselves
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'login_process.php']; // Pages accessible without login

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header('Location: login.php');
    exit();
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
    <!-- Bootstrap CSS (or your preferred framework) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css"> 
    
    <!-- You can add more meta tags, stylesheets, or scripts here -->
</head>
<body>
    <div class="wrapper">
        <!-- Navigation Bar (example) -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Umroh Hajj Ticketing</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role_name'] == 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/dashboard.php">Admin Dashboard</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="masterDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Master Data
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="masterDropdown">
                                        <li><a class="dropdown-item" href="/admin/masters/agents/index.php">Manage Agents</a></li>
                                        <li><a class="dropdown-item" href="/admin/masters/users/index.php">Manage Users</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/movement_fullview.php" target="_blank">Flight Monitor</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/new_request.php">New Request</a>
                                </li>
                                <!-- Add other admin links -->
                            <?php elseif ($_SESSION['role_name'] == 'finance'): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="financeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Finance Operations
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="financeDropdown">
                                        <li><a class="dropdown-item" href="/finance/dashboard.php">Finance Dashboard</a></li>
                                        <li><a class="dropdown-item" href="/finance/dashboard.php#ready-for-invoice">Create Invoice</a></li>
                                        <li><a class="dropdown-item" href="/finance/dashboard.php#all-invoices">Manage Invoices</a></li>
                                    </ul>
                                </li>
                                <!-- Add other finance links -->
                            <?php elseif ($_SESSION['role_name'] == 'monitor'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/monitor/dashboard.php">Monitor Dashboard</a>
                                </li>
                                <!-- Add other monitor links -->
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['username'])): ?>
                            <li class="nav-item">
                                <span class="nav-link text-light">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['role_name']); ?>)</span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-danger text-white" href="/logout.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary text-white" href="/login.php">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="container mt-4">
