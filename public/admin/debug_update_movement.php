<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth(['admin']);

$id = $_GET['id'] ?? null;
$deadline = $_GET['deadline'] ?? null;
$done = $_GET['done'] ?? null;

if (!$id) {
    die("Usage: ?id=X&deadline=YYYY-MM-DD[&done=0|1]");
}

$updateData = [];
if ($deadline) $updateData['ticketing_deadline'] = $deadline;
if ($done !== null) $updateData['ticketing_done'] = (int)$done;

if (empty($updateData)) {
    die("Nothing to update. Provide 'deadline' or 'done' parameter.");
}

if (Movement::update($id, $updateData, $_SESSION['user_id'])) {
    echo "Successfully updated Movement ID $id.<br>";
    echo "New values: " . json_encode($updateData);
    echo "<br><br><a href='movement_fullview.php'>Back to Movement Dashboard</a>";
} else {
    echo "Failed to update Movement ID $id.";
}
