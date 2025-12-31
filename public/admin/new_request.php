<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

// Fetch agents for the dropdown
$stmt = $pdo->query("SELECT id, name FROM agents ORDER BY name ASC");
$agents = $stmt->fetchAll();

$title = "New Request";
require_once __DIR__ . '/../shared/header.php';
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Request</h1>
        <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
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
                    <label for="pax_count" class="form-label">Pax Count *</label>
                    <input type="number" class="form-control" id="pax_count" name="pax_count" required min="1">
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
                    <input type="text" class="form-control" id="airline_preference" name="airline_preference" placeholder="e.g., Garuda, Saudia">
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Create Request</button>
            </form>
        </div>
    </div>

<?php
require_once __DIR__ . '/../shared/footer.php';
?>
