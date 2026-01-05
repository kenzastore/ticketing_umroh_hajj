<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth('admin');

$title = "Admin Dashboard - Booking Requests";

// Fetch all booking requests
$requests = BookingRequest::readAll();

require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Booking Requests</h1>
    <a href="new_request.php" class="btn btn-primary">Create New Request</a>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === 'request_created'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Booking request created successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Request No</th>
                        <th>Corporate</th>
                        <th>Agent</th>
                        <th>Pax</th>
                        <th>Selling Fare</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No booking requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><?php echo htmlspecialchars($r['request_no'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['corporate_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?></td>
                                <td><?php echo $r['group_size']; ?></td>
                                <td><?php echo number_format($r['selling_fare'], 2); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($r['created_at'])); ?></td>
                                <td>
                                    <a href="request_detail.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info text-white">View Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
