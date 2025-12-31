<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth('finance');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Finance Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Finance Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
