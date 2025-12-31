<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, b.id as booking_id, b.status, b.pnr_code, a.name as agent_name
    FROM requests r
    JOIN bookings b ON r.id = b.request_id
    LEFT JOIN agents a ON r.agent_id = a.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) {
    header('Location: dashboard.php?error=not_found');
    exit;
}

$statuses = [
    'NEW', 'QUOTED', 'BLOCKED', 'PNR_ISSUED', 'IN_MOVEMENT',
    'INVOICED', 'PAID_DEPOSIT', 'PAID_FULL', 'CONFIRMED_FP_MERAH',
    'REPORTED', 'RECEIPTED', 'CHANGED', 'CANCELED'
];

$title = "Request Detail #" . $r['id'];
require_once __DIR__ . '/../shared/header.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Request #<?php echo $r['id']; ?></h1>
        <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Status updated successfully!</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 shadow">
                <div class="card-header bg-primary text-white">Request Details</div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Agent / Requester</div>
                        <div class="col-sm-8 fw-bold"><?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Pax Count</div>
                        <div class="col-sm-8"><?php echo $r['pax_count']; ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Travel Period</div>
                        <div class="col-sm-8">
                            <?php
                            $start = $r['travel_date_start'] ? date('d M Y', strtotime($r['travel_date_start'])) : 'N/A';
                            $end = $r['travel_date_end'] ? date('d M Y', strtotime($r['travel_date_end'])) : 'N/A';
                            echo $start . ' - ' . $end;
                            ?>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Airline Preference</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($r['airline_preference'] ?? '-'); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Created At</div>
                        <div class="col-sm-8"><?php echo date('d M Y H:i', strtotime($r['created_at'])); ?></div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted">Notes</h6>
                            <div class="p-3 bg-light border rounded">
                                <?php echo nl2br(htmlspecialchars($r['notes'] ?? 'No notes.')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow">
                <div class="card-header bg-info text-white">Booking Status & PNR</div>
                <div class="card-body">
                    <form action="update_status.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $r['booking_id']; ?>">
                        <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $r['status'] === $status ? 'selected' : ''; ?>>
                                        <?php echo $status; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="pnr_code" class="form-label">PNR Code</label>
                            <input type="text" class="form-control" id="pnr_code" name="pnr_code" value="<?php echo htmlspecialchars($r['pnr_code'] ?? ''); ?>">
                            <div class="form-text text-muted small">Entering a PNR code usually implies the booking is at least 'PNR_ISSUED'.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
require_once __DIR__ . '/../shared/footer.php';
?>