<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth('monitor');

$title = "Monitor Dashboard"; // Set title for the header

require_once __DIR__ . '/../shared/header.php';
?>
    <div class="container">
        <h1>Monitor Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        <!-- Monitor specific content goes here -->
    </div>
<?php
require_once __DIR__ . '/../shared/footer.php';
?>
