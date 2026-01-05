<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth('monitor');

$title = "Monitor Dashboard"; // Set title for the header

require_once __DIR__ . '/../shared/header.php';
?>
    <div class="container">
        <h1>Monitor Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
        
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Flight Operations Monitoring</h4>
            </div>
            <div class="card-body text-center">
                <p class="card-text">Access real-time flight status for all active movements.</p>
                <a href="/admin/movement_fullview.php" class="btn btn-lg btn-success">Go to Flight Monitor</a>
            </div>
        </div>
        
        <!-- More monitor-specific content can go here later -->

    </div>
<?php
require_once __DIR__ . '/../shared/footer.php';
?>
