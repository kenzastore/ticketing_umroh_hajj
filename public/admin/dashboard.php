<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
