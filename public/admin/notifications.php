<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Notification.php';

check_auth(['admin', 'finance']);

if (isset($_GET['read'])) {
    Notification::markAsRead($_GET['read']);
    header('Location: notifications.php');
    exit;
}

$title = "Notifications & Alerts";
require_once __DIR__ . '/../shared/header.php';

$notifs = Notification::getUnread();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Notifications & Alerts</h1>
    <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">Refresh</button>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($notifs)): ?>
            <div class="p-5 text-center text-muted">
                <h5>All caught up!</h5>
                <p>No new notifications at the moment.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifs as $n): ?>
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-<?php 
                                    echo $n['alert_type'] == 'DEADLINE' ? 'danger' : ($n['alert_type'] == 'PAYMENT' ? 'warning text-dark' : 'info'); 
                                ?> mb-2">
                                    <?= $n['alert_type'] ?>
                                </span>
                                <p class="mb-1 fw-bold"><?= htmlspecialchars($n['message']) ?></p>
                                <small class="text-muted"><?= $n['created_at'] ?></small>
                            </div>
                            <a href="?read=<?= $n['id'] ?>" class="btn btn-sm btn-link text-decoration-none">Mark as Read</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
