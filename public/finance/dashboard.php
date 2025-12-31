<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth('finance');

$title = "Finance Dashboard"; // Set title for the header

require_once __DIR__ . '/../shared/header.php';
?>
    <div class="container">
        <h1>Finance Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <!-- Finance specific content goes here -->
    </div>
<?php
require_once __DIR__ . '/../shared/footer.php';
?>
