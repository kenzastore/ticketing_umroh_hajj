<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Notification.php';

check_auth(['admin', 'finance']);

$title = "Notifications & Alerts";
require_once __DIR__ . '/../shared/header.php';

// Filter Params
$status = $_GET['status'] ?? 'unread'; // default unread
$type = $_GET['type'] ?? 'all';
$page = $_GET['page'] ?? 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Fetch Notifications
$filters = [
    'status' => $status,
    'type' => $type,
    'limit' => $limit,
    'offset' => $offset
];

$notifs = Notification::getAll($filters);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Notifications & Alerts</h1>
    <div>
        <a href="notification_process.php?action=mark_all_read" class="btn btn-outline-primary btn-sm me-2" onclick="return confirm('Mark all as read?');">Mark All as Read</a>
        <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">Refresh</button>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label fw-bold small">Status:</label>
            </div>
            <div class="col-auto">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="?status=unread&type=<?= $type ?>" class="btn btn-outline-secondary <?= $status == 'unread' ? 'active' : '' ?>">Unread</a>
                    <a href="?status=all&type=<?= $type ?>" class="btn btn-outline-secondary <?= $status == 'all' ? 'active' : '' ?>">All</a>
                </div>
                <input type="hidden" name="status" value="<?= $status ?>">
            </div>

            <div class="col-auto ms-3">
                <label class="col-form-label fw-bold small">Type:</label>
            </div>
            <div class="col-auto">
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?= $type == 'all' ? 'selected' : '' ?>>All Types</option>
                    <option value="DEADLINE" <?= $type == 'DEADLINE' ? 'selected' : '' ?>>Deadline</option>
                    <option value="PAYMENT" <?= $type == 'PAYMENT' ? 'selected' : '' ?>>Payment</option>
                    <option value="SYSTEM" <?= $type == 'SYSTEM' ? 'selected' : '' ?>>System</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($notifs)): ?>
            <div class="p-5 text-center text-muted">
                <?php if ($status == 'unread'): ?>
                    <h5>All caught up!</h5>
                    <p>No new notifications at the moment.</p>
                <?php else: ?>
                    <p>No notifications found.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifs as $n): ?>
                    <div class="list-group-item p-3 <?= $n['is_read'] ? 'bg-light' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="badge bg-<?php 
                                    echo $n['alert_type'] == 'DEADLINE' ? 'danger' : ($n['alert_type'] == 'PAYMENT' ? 'warning text-dark' : 'info'); 
                                ?> mb-2">
                                    <?= $n['alert_type'] ?>
                                </span>
                                <?php if (!$n['is_read']): ?>
                                    <span class="badge bg-primary rounded-pill ms-1" style="font-size: 0.6em;">NEW</span>
                                <?php endif; ?>
                                
                                <p class="mb-1 fw-bold"><?= htmlspecialchars($n['message']) ?></p>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-3"><i class="far fa-clock me-1"></i><?= $n['created_at'] ?></small>
                                    <?php if ($n['entity_type'] == 'movement'): ?>
                                        <a href="edit_movement.php?id=<?= $n['entity_id'] ?>" class="btn btn-xs btn-outline-dark py-0" style="font-size: 0.7rem;">View Movement</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!$n['is_read']): ?>
                                <a href="notification_process.php?action=mark_read&id=<?= $n['id'] ?>" class="btn btn-sm btn-link text-decoration-none">Mark as Read</a>
                            <?php else: ?>
                                <span class="text-muted small"><i class="fas fa-check"></i> Read</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>