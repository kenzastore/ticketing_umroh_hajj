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

$r = BookingRequest::readById($id);
if (!$r) {
    header('Location: dashboard.php?error=not_found');
    exit;
}

$title = "Convert Request to Movement";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Convert to Movement</h1>
    <a href="request_detail.php?id=<?php echo $id; ?>" class="btn btn-secondary">&larr; Back to Detail</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">Request Summary</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted">Agent</td><td><?php echo htmlspecialchars($r['agent_name'] ?? '-'); ?></td></tr>
                    <tr><td class="text-muted">Pax</td><td><?php echo $r['group_size']; ?></td></tr>
                    <tr><td class="text-muted">Fare</td><td><?php echo number_format($r['selling_fare'], 2); ?></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Movement Details</div>
            <div class="card-body">
                <form action="convert_request_process.php" method="POST">
                    <input type="hidden" name="booking_request_id" value="<?php echo $r['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="UMRAH">UMRAH</option>
                                <option value="HAJJI">HAJJI</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pnr" class="form-label">PNR *</label>
                            <input type="text" class="form-control fw-bold" id="pnr" name="pnr" required placeholder="e.g. TRV12JT" maxlength="20">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="tour_code" class="form-label">Tour Code *</label>
                            <input type="text" class="form-control" id="tour_code" name="tour_code" required placeholder="e.g. 5FEB-40-13D-TRID">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="carrier" class="form-label">Carrier *</label>
                            <input type="text" class="form-control" id="carrier" name="carrier" required placeholder="e.g. TRID" maxlength="20">
                        </div>
                    </div>

                    <hr>
                    <p class="text-muted small">The following data will be copied from the booking request:</p>
                    <ul class="small text-muted">
                        <li>Agent: <?php echo htmlspecialchars($r['agent_name']); ?></li>
                        <li>Passenger Count: <?php echo $r['group_size']; ?></li>
                        <li>Approved Fare: <?php echo number_format($r['gp_approved_fare'], 2); ?></li>
                        <li>Selling Fare: <?php echo number_format($r['selling_fare'], 2); ?></li>
                        <li>Flight segments (if any)</li>
                    </ul>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-lg btn-success px-5">Finalize Conversion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
