<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';
require_once __DIR__ . '/../../app/models/Agent.php';
require_once __DIR__ . '/../../app/models/Corporate.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: booking_requests.php');
    exit;
}

$r = BookingRequest::readById($id);
if (!$r) {
    die("Request not found.");
}

$agents = Agent::readAll();
$corporates = Corporate::readAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header = [
        'corporate_id' => $_POST['corporate_id'] ?: null,
        'agent_id' => $_POST['agent_id'] ?: null,
        'group_size' => $_POST['group_size'],
        'request_no' => $_POST['request_no'] ?: null,
        'notes' => $_POST['notes'] ?? '',
        'selling_fare' => $_POST['selling_fare'] ?: 0,
        'nett_fare' => $_POST['nett_fare'] ?: 0,
        'duration_days' => $_POST['duration_days'] ?: 0,
        'add1_days' => $_POST['add1_days'] ?: 0,
        'ttl_days' => $_POST['ttl_days'] ?: 0,
        'tcp' => $_POST['tcp'] ?: 0
    ];

    // Handle names for denormalized columns
    if ($header['agent_id']) {
        foreach ($agents as $a) {
            if ($a['id'] == $header['agent_id']) {
                $header['agent_name'] = $a['name'];
                $header['skyagent_id'] = $a['skyagent_id'];
                break;
            }
        }
    }
    if ($header['corporate_id']) {
        foreach ($corporates as $c) {
            if ($c['id'] == $header['corporate_id']) {
                $header['corporate_name'] = $c['name'];
                break;
            }
        }
    }

    $legs = [];
    foreach ($_POST['legs'] as $leg) {
        if (!empty($leg['flight_date']) || !empty($leg['flight_no'])) {
            $legs[] = $leg;
        }
    }

    if (BookingRequest::update($id, $header, $legs, $_SESSION['user_id'])) {
        $success = 'Booking Request updated successfully.';
        header("refresh:1;url=request_detail.php?id=$id");
    } else {
        $error = 'Failed to update booking request.';
    }
}

$title = "Edit Booking Request #" . $r['id'];
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Booking Request #<?php echo $r['id']; ?></h1>
    <a href="request_detail.php?id=<?php echo $id; ?>" class="btn btn-secondary">&larr; Back to Detail</a>
</div>

<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

<form method="POST">
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
                                <option value="<?php echo $corp['id']; ?>" <?php echo $r['corporate_id'] == $corp['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($corp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="agent_id" class="form-label">Travel Agent</label>
                        <select class="form-select" id="agent_id" name="agent_id">
                            <option value="">-- Select Agent --</option>
                            <?php foreach ($agents as $agent): ?>
                                <option value="<?php echo $agent['id']; ?>" data-skyagent="<?php echo htmlspecialchars($agent['skyagent_id']); ?>" <?php echo $r['agent_id'] == $agent['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($agent['name']); ?> 
                                    (<?php echo htmlspecialchars($agent['skyagent_id']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="group_size" class="form-label">Group Size / Pax *</label>
                            <input type="number" class="form-control" id="group_size" name="group_size" required min="1" value="<?php echo $r['group_size']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="request_no" class="form-label">Worksheet No</label>
                            <input type="number" class="form-control" id="request_no" name="request_no" value="<?php echo $r['request_no']; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"><?php echo htmlspecialchars($r['notes']); ?></textarea>
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
                            <input type="number" step="0.01" class="form-control" id="selling_fare" name="selling_fare" value="<?php echo $r['selling_fare']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nett_fare" class="form-label">Nett Fare</label>
                            <input type="number" step="0.01" class="form-control" id="nett_fare" name="nett_fare" value="<?php echo $r['nett_fare']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="duration_days" class="form-label">Duration</label>
                            <input type="number" class="form-control" id="duration_days" name="duration_days" value="<?php echo $r['duration_days']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="add1_days" class="form-label">Add 1</label>
                            <input type="number" class="form-control" id="add1_days" name="add1_days" value="<?php echo $r['add1_days']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ttl_days" class="form-label">TTL Days</label>
                            <input type="number" class="form-control" id="ttl_days" name="ttl_days" value="<?php echo $r['ttl_days']; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tcp" class="form-label">TCP</label>
                        <input type="number" step="0.01" class="form-control" id="tcp" name="tcp" value="<?php echo $r['tcp']; ?>">
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
                <?php for ($i = 1; $i <= 4; $i++): 
                    $leg = null;
                    foreach($r['legs'] as $l) { if($l['leg_no'] == $i) { $leg = $l; break; } }
                ?>
                    <div class="col-md-3 mb-3 border-end">
                        <h6>Leg <?php echo $i; ?></h6>
                        <div class="mb-2">
                            <label class="form-label small">Date</label>
                            <input type="date" name="legs[<?php echo $i; ?>][flight_date]" class="form-control form-control-sm" value="<?php echo $leg ? $leg['flight_date'] : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Flight No</label>
                            <input type="text" name="legs[<?php echo $i; ?>][flight_no]" class="form-control form-control-sm" value="<?php echo $leg ? htmlspecialchars($leg['flight_no']) : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Sector</label>
                            <input type="text" name="legs[<?php echo $i; ?>][sector]" class="form-control form-control-sm" value="<?php echo $leg ? htmlspecialchars($leg['sector']) : ''; ?>">
                        </div>
                        <input type="hidden" name="legs[<?php echo $i; ?>][leg_no]" value="<?php echo $i; ?>">
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="text-end mb-5">
        <button type="submit" class="btn btn-lg btn-primary">Update Booking Request</button>
    </div>
</form>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
