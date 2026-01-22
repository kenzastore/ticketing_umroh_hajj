<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/AuditLog.php';
require_once __DIR__ . '/../../app/models/User.php';

check_auth('admin');

$title = "Audit Logs";

// Filters
$filters = [
    'user_id' => $_GET['user_id'] ?? '',
    'entity_type' => $_GET['entity_type'] ?? '',
    'action' => $_GET['action'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

// Pagination
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$logs = AuditLog::readAll($limit, $offset, $filters);
$totalLogs = AuditLog::countAll($filters);
$totalPages = ceil($totalLogs / $limit);

$users = User::readAll();

require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">System Audit Logs</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filters['user_id'] == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Entity Type</label>
                <input type="text" name="entity_type" class="form-control form-control-sm" placeholder="e.g. movement" value="<?= htmlspecialchars($filters['entity_type']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Action</label>
                <input type="text" name="action" class="form-control form-control-sm" placeholder="e.g. UPDATE" value="<?= htmlspecialchars($filters['action']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_from']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_to']) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                <a href="audit_logs.php" class="btn btn-secondary btn-sm ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm small" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Entity ID</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="7" class="text-center">No logs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $log['id'] ?></td>
                                <td><?= $log['created_at'] ?></td>
                                <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($log['action']) ?></span></td>
                                <td><?= htmlspecialchars($log['entity_type']) ?></td>
                                <td><?= $log['entity_id'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#logModal<?= $log['id'] ?>">
                                        View
                                    </button>
                                    
                                    <!-- Modal -->
                                    <div class="modal fade" id="logModal<?= $log['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content text-dark">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Log Details #<?= $log['id'] ?></h5>
                                                    <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Old Value</h6>
                                                            <pre class="bg-light p-2 border small" style="max-height: 400px; overflow: auto;"><?php 
                                                                $old = json_decode($log['old_value'], true);
                                                                echo htmlspecialchars(json_encode($old, JSON_PRETTY_PRINT) ?: $log['old_value']); 
                                                            ?></pre>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>New Value</h6>
                                                            <pre class="bg-light p-2 border small" style="max-height: 400px; overflow: auto;"><?php 
                                                                $new = json_decode($log['new_value'], true);
                                                                echo htmlspecialchars(json_encode($new, JSON_PRETTY_PRINT) ?: $log['new_value']); 
                                                            ?></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm justify-content-center mt-3">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<style>
    .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.75rem;
    }
    pre {
        white-space: pre-wrap;
        word-break: break-all;
    }
</style>

<?php require_once __DIR__ . '/../shared/header.php'; ?>
<?php require_once __DIR__ . '/../shared/footer.php'; ?>
