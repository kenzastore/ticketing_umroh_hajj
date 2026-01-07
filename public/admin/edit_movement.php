<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth(['admin']);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: movement_fullview.php');
    exit;
}

$m = Movement::readById($id);
if (!$m) {
    header('Location: movement_fullview.php?error=not_found');
    exit;
}

$title = "Edit Movement PNR: " . $m['pnr'];
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Movement</h1>
    <a href="movement_fullview.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
</div>

<form action="edit_movement_process.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">

    <div class="row">
        <!-- Section 1: Core Info -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white">General Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PNR *</label>
                            <input type="text" name="pnr" class="form-control fw-bold" value="<?php echo htmlspecialchars($m['pnr']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="UMRAH" <?php echo $m['category'] == 'UMRAH' ? 'selected' : ''; ?>>UMRAH</option>
                                <option value="HAJJI" <?php echo $m['category'] == 'HAJJI' ? 'selected' : ''; ?>>HAJJI</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tour Code</label>
                        <input type="text" name="tour_code" class="form-control" value="<?php echo htmlspecialchars($m['tour_code']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Travel Agent</label>
                        <input type="text" name="agent_name" class="form-control" value="<?php echo htmlspecialchars($m['agent_name']); ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Carrier</label>
                            <input type="text" name="carrier" class="form-control" value="<?php echo htmlspecialchars($m['carrier']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passenger Count</label>
                            <input type="number" name="passenger_count" class="form-control" value="<?php echo $m['passenger_count']; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Belonging To</label>
                        <input type="text" name="belonging_to" class="form-control" value="<?php echo htmlspecialchars($m['belonging_to']); ?>" placeholder="e.g. BI-CGK">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Payment Status -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">Payment Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">DP 1 Status</label>
                        <input type="text" name="dp1_status" class="form-control" value="<?php echo htmlspecialchars($m['dp1_status']); ?>" placeholder="e.g. PAID or NIL">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">DP 2 Status</label>
                        <input type="text" name="dp2_status" class="form-control" value="<?php echo htmlspecialchars($m['dp2_status']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Payment Status</label>
                        <input type="text" name="fp_status" class="form-control" value="<?php echo htmlspecialchars($m['fp_status']); ?>">
                    </div>
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="ticketing_done" id="ticketing_done" value="1" <?php echo $m['ticketing_done'] ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold" for="ticketing_done">Ticketing Process Finished (DONE)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Section 3: Schedule -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">Flight Schedule Summaries</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Flight No (Out)</label>
                            <input type="text" name="flight_no_out" class="form-control" value="<?php echo htmlspecialchars($m['flight_no_out']); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sector (Out)</label>
                            <input type="text" name="sector_out" class="form-control" value="<?php echo htmlspecialchars($m['sector_out']); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Departure Date</label>
                            <input type="date" name="dep_seg1_date" class="form-control" value="<?php echo $m['dep_seg1_date']; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Ticketing Deadline *</label>
                            <input type="date" name="ticketing_deadline" class="form-control bg-light fw-bold" value="<?php echo $m['ticketing_deadline']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Duration</label>
                            <input type="number" name="duration_days" class="form-control" value="<?php echo $m['duration_days']; ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Add-1</label>
                            <input type="number" name="add1_days" class="form-control" value="<?php echo $m['add1_days']; ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">TTL Days</label>
                            <input type="number" name="ttl_days" class="form-control" value="<?php echo $m['ttl_days']; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Bal. Nett Amount</label>
                            <input type="number" step="0.01" name="nett_balance_amount" class="form-control" value="<?php echo $m['nett_balance_amount']; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Bal. Sell Amount</label>
                            <input type="number" step="0.01" name="sell_balance_amount" class="form-control" value="<?php echo $m['sell_balance_amount']; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Section 4: Airlines Deadlines -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">Airlines Payment Deadlines</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 border-end">
                            <h6>1st Deposit</h6>
                            <label class="form-label small">Amount</label>
                            <input type="number" step="0.01" name="deposit1_airlines_amount" class="form-control mb-2" value="<?php echo $m['deposit1_airlines_amount']; ?>">
                            <label class="form-label small">Deadline (Airline)</label>
                            <input type="date" name="deposit1_airlines_date" class="form-control mb-2" value="<?php echo $m['deposit1_airlines_date']; ?>">
                            <label class="form-label small">Deadline (EEMW)</label>
                            <input type="date" name="deposit1_eemw_date" class="form-control" value="<?php echo $m['deposit1_eemw_date']; ?>">
                        </div>
                        <div class="col-md-4 mb-3 border-end">
                            <h6>2nd Deposit</h6>
                            <label class="form-label small">Amount</label>
                            <input type="number" step="0.01" name="deposit2_airlines_amount" class="form-control mb-2" value="<?php echo $m['deposit2_airlines_amount']; ?>">
                            <label class="form-label small">Deadline (Airline)</label>
                            <input type="date" name="deposit2_airlines_date" class="form-control mb-2" value="<?php echo $m['deposit2_airlines_date']; ?>">
                            <label class="form-label small">Deadline (EEMW)</label>
                            <input type="date" name="deposit2_eemw_date" class="form-control" value="<?php echo $m['deposit2_eemw_date']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6>Full Payment</h6>
                            <label class="form-label small">Deadline (Airline)</label>
                            <input type="date" name="fullpay_airlines_date" class="form-control mb-2" value="<?php echo $m['fullpay_airlines_date']; ?>">
                            <label class="form-label small">Deadline (EEMW)</label>
                            <input type="date" name="fullpay_eemw_date" class="form-control" value="<?php echo $m['fullpay_eemw_date']; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mb-5">
        <button type="submit" class="btn btn-primary btn-lg px-5">Update Movement</button>
    </div>
</form>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
