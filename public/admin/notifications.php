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

if (isset($_GET['read_all'])) {
    Notification::markAllAsRead();
    header('Location: notifications.php');
    exit;
}

$title = "Notifications & Alerts";
require_once __DIR__ . '/../shared/header.php';

$notifs = Notification::getUnread();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Notifications & Alerts</h1>
    <div>
        <?php if (!empty($notifs)): ?>
            <a href="?read_all=1" class="btn btn-outline-primary btn-sm me-2" onclick="return confirm('Mark all as read?');">Mark All as Read</a>
        <?php endif; ?>
        <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">Refresh</button>
    </div>
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
                            <div class="flex-grow-1">
                                <span class="badge bg-<?php 
                                    echo $n['alert_type'] == 'DEADLINE' ? 'danger' : ($n['alert_type'] == 'PAYMENT' ? 'warning text-dark' : 'info'); 
                                ?> mb-2">
                                    <?= $n['alert_type'] ?>
                                </span>
                                <p class="mb-1 fw-bold"><?= htmlspecialchars($n['message']) ?></p>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-3"><i class="far fa-clock me-1"></i><?= $n['created_at'] ?></small>
                                    <?php if ($n['entity_type'] == 'movement'): ?>
                                        <a href="edit_movement.php?id=<?= $n['entity_id'] ?>" class="btn btn-xs btn-outline-dark py-0" style="font-size: 0.7rem;">View Movement</a>
                                    <?php endif; ?>
                                </div>
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
