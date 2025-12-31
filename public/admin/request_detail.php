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

$statuses = ['NEW', 'QUOTED', 'BLOCKED', 'PNR_ISSUED', 'CANCELED'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Detail - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Request #<?php echo $r['id']; ?></h1>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Status updated successfully!</div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Details</div>
                    <div class="card-body">
                        <p><strong>Agent / FID:</strong> <?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?></p>
                        <p><strong>Pax Count:</strong> <?php echo $r['pax_count']; ?></p>
                        <p><strong>Travel Period:</strong> <?php echo $r['travel_date_start']; ?> to <?php echo $r['travel_date_end']; ?></p>
                        <p><strong>Airline Preference:</strong> <?php echo htmlspecialchars($r['airline_preference']); ?></p>
                        <p><strong>Notes:</strong><br><?php echo nl2br(htmlspecialchars($r['notes'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">Status & PNR</div>
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
                                <div class="form-text">Updating PNR will automatically set status to PNR_ISSUED.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Update Status/PNR</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>