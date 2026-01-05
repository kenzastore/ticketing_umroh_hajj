<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

// Fetch the booking request using the new model
$r = BookingRequest::readById($id);

if (!$r) {
    header('Location: dashboard.php?error=not_found');
    exit;
}

$title = "Booking Request Detail #" . $r['id'];
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Booking Request #<?php echo $r['id']; ?></h1>
        <h5 class="text-muted">No: <?php echo htmlspecialchars($r['request_no'] ?? '-'); ?></h5>
    </div>
    <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
</div>

<div class="row">
    <!-- Main Details -->
    <div class="col-md-8">
        <div class="card mb-4 shadow">
            <div class="card-header bg-primary text-white">Request Details</div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">Corporate</div>
                    <div class="col-sm-8 fw-bold"><?php echo htmlspecialchars($r['corporate_name'] ?? '-'); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">Agent / Requester</div>
                    <div class="col-sm-8">
                        <?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?>
                        <?php if(!empty($r['skyagent_id'])): ?>
                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($r['skyagent_id']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">Group Size / Pax</div>
                    <div class="col-sm-8 fw-bold fs-5"><?php echo $r['group_size']; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">Duration</div>
                    <div class="col-sm-8">
                        <?php echo $r['duration_days']; ?> Days 
                        <?php if($r['add1_days']): ?> (+1 Day: <?php echo $r['add1_days']; ?>) <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">TTL Days</div>
                    <div class="col-sm-8"><?php echo $r['ttl_days'] ?? '-'; ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">Pricing (Total Selling)</div>
                    <div class="col-sm-8 text-success fw-bold">
                        <?php echo $r['selling_fare'] ? number_format($r['selling_fare'], 2) : '-'; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4 text-muted">Created At</div>
                    <div class="col-sm-8"><?php echo date('d M Y H:i', strtotime($r['created_at'])); ?></div>
                </div>
                
                <div class="mt-4">
                    <h6 class="text-muted border-bottom pb-2">Flight Segments</h6>
                    <?php if (!empty($r['legs'])): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Leg</th>
                                        <th>Date</th>
                                        <th>Flight No</th>
                                        <th>Sector</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($r['legs'] as $leg): ?>
                                        <tr>
                                            <td><?php echo $leg['leg_no']; ?></td>
                                            <td><?php echo $leg['flight_date'] ? date('d M Y', strtotime($leg['flight_date'])) : '-'; ?></td>
                                            <td><?php echo htmlspecialchars($leg['flight_no'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($leg['sector'] ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">No flight segments recorded.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <h6 class="text-muted">Notes</h6>
                    <div class="p-3 bg-light border rounded">
                        <?php echo nl2br(htmlspecialchars($r['notes'] ?? 'No notes.')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions / Pricing Info -->
    <div class="col-md-4">
        <div class="card mb-4 shadow">
            <div class="card-header bg-secondary text-white">Internal Pricing</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">TCP</label>
                    <div class="fw-bold"><?php echo $r['tcp'] ? number_format($r['tcp'], 2) : '-'; ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">GP Approved Fare</label>
                    <div class="fw-bold"><?php echo $r['gp_approved_fare'] ? number_format($r['gp_approved_fare'], 2) : '-'; ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Nett Fare</label>
                    <div class="fw-bold"><?php echo $r['nett_fare'] ? number_format($r['nett_fare'], 2) : '-'; ?></div>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-body">
                <h6 class="card-title">Next Actions</h6>
                <p class="small text-muted">
                    This request is currently a draft/quote. To proceed with operations, it should be converted to a Movement.
                </p>
                <button class="btn btn-success w-100 mb-2" disabled>Convert to Movement (Coming Soon)</button>
                <form action="delete_request_process.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                    <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger w-100">Delete Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
