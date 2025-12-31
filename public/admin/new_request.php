<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

// Fetch agents for the dropdown
$stmt = $pdo->query("SELECT id, name FROM agents");
$agents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Request - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Create New Request</h1>
        <form action="new_request_process.php" method="POST">
            <div class="mb-3">
                <label for="agent_id" class="form-label">Agent / Requester</label>
                <select class="form-select" id="agent_id" name="agent_id">
                    <option value="">Individual / FID</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?php echo $agent['id']; ?>"><?php echo htmlspecialchars($agent['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="pax_count" class="form-label">Pax Count</label>
                <input type="number" class="form-control" id="pax_count" name="pax_count" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="travel_date_start" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="travel_date_start" name="travel_date_start">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="travel_date_end" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="travel_date_end" name="travel_date_end">
                </div>
            </div>
            <div class="mb-3">
                <label for="airline_preference" class="form-label">Airline Preference</label>
                <input type="text" class="form-control" id="airline_preference" name="airline_preference">
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Create Request</button>
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>
