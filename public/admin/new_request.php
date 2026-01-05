<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Agent.php';
require_once __DIR__ . '/../../app/models/Corporate.php';

check_auth('admin');

$agents = Agent::readAll();
$corporates = Corporate::readAll();

$title = "New Booking Request";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Create New Booking Request</h1>
    <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<form action="new_request_process.php" method="POST">
    <div class="row">
        <!-- Basic Info -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">Header Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="corporate_id" class="form-label">Corporate</label>
                        <select class="form-select" id="corporate_id" name="corporate_id">
                            <option value="">-- Select Corporate --</option>
                            <?php foreach ($corporates as $corp): ?>
                                <option value="<?php echo $corp['id']; ?>"><?php echo htmlspecialchars($corp['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="agent_id" class="form-label">Travel Agent</label>
                        <select class="form-select" id="agent_id" name="agent_id">
                            <option value="">-- Select Agent --</option>
                            <?php foreach ($agents as $agent): ?>
                                <option value="<?php echo $agent['id']; ?>" data-skyagent="<?php echo htmlspecialchars($agent['skyagent_id']); ?>">
                                    <?php echo htmlspecialchars($agent['name']); ?> 
                                    (<?php echo htmlspecialchars($agent['skyagent_id']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_size" class="form-label">Group Size / Pax *</label>
                            <input type="number" class="form-control" id="group_size" name="group_size" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="request_no" class="form-label">Worksheet No (Optional)</label>
                            <input type="number" class="form-control" id="request_no" name="request_no">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Duration -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white">Pricing & Duration</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="selling_fare" class="form-label">Selling Fare (Total)</label>
                            <input type="number" step="0.01" class="form-control" id="selling_fare" name="selling_fare" placeholder="e.g. 15000000">
                            <div class="form-text small">Total price for the whole group</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nett_fare" class="form-label">Nett Fare</label>
                            <input type="number" step="0.01" class="form-control" id="nett_fare" name="nett_fare" placeholder="Base cost">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="duration_days" class="form-label">Duration</label>
                            <input type="number" class="form-control" id="duration_days" name="duration_days" placeholder="Days">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="add1_days" class="form-label">Add 1</label>
                            <input type="number" class="form-control" id="add1_days" name="add1_days">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ttl_days" class="form-label">TTL Days</label>
                            <input type="number" class="form-control" id="ttl_days" name="ttl_days" placeholder="Total">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tcp" class="form-label">TCP</label>
                        <input type="number" step="0.01" class="form-control" id="tcp" name="tcp">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flight Segments -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Flight Segments (Max 4 Legs)</div>
        <div class="card-body">
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="col-md-3 mb-3 border-end">
                        <h6>Leg <?php echo $i; ?></h6>
                        <div class="mb-2">
                            <label class="form-label small">Date</label>
                            <input type="date" name="legs[<?php echo $i; ?>][flight_date]" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Flight No</label>
                            <input type="text" name="legs[<?php echo $i; ?>][flight_no]" class="form-control form-control-sm" placeholder="e.g. TR596">
                            <div class="form-text mt-0 small-xs" style="font-size: 0.7rem;">Airline code + Number</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Sector</label>
                            <input type="text" name="legs[<?php echo $i; ?>][sector]" class="form-control form-control-sm" placeholder="e.g. SUB-SIN" maxlength="50">
                            <div class="form-text mt-0 small-xs" style="font-size: 0.7rem;">Format: DEP-ARR (max 50 chars)</div>
                        </div>
                        <input type="hidden" name="legs[<?php echo $i; ?>][leg_no]" value="<?php echo $i; ?>">
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="text-end mb-5">
        <button type="submit" class="btn btn-lg btn-success">Save Booking Request</button>
    </div>
</form>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>