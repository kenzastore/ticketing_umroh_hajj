<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Agent.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$agent = Agent::readById($id);
if (!$agent) {
    die("Agent not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'skyagent_id' => $_POST['skyagent_id'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'email' => $_POST['email'] ?? ''
    ];

    if (empty($data['name'])) {
        $error = 'Agent Name is required.';
    } else {
        if (Agent::update($id, $data)) {
            $success = 'Agent updated successfully.';
            $agent = Agent::readById($id); // Refresh data
            header("refresh:1;url=index.php");
        } else {
            $error = 'Failed to update agent or no changes made.';
        }
    }
}

$title = "Edit Agent";
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="mb-3">
    <a href="index.php" class="btn btn-secondary">&larr; Back to List</a>
</div>

<div class="card shadow">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Edit Agent</h4>
    </div>
    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Agent Name *</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($agent['name']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Skyagent ID</label>
                    <input type="text" name="skyagent_id" class="form-control" value="<?php echo htmlspecialchars($agent['skyagent_id']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($agent['phone']); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($agent['email']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Agent</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>