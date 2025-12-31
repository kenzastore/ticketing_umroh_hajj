<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Agent.php';

check_auth('admin');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'contact_person' => $_POST['contact_person'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'email' => $_POST['email'] ?? '',
        'address' => $_POST['address'] ?? ''
    ];

    if (empty($data['name'])) {
        $error = 'Agent Name is required.';
    } else {
        if (Agent::create($data)) {
            $success = 'Agent created successfully.';
            // Redirect to index after short delay or show link
            header("refresh:1;url=index.php");
        } else {
            $error = 'Failed to create agent.';
        }
    }
}

$title = "Add New Agent";
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="mb-3">
    <a href="index.php" class="btn btn-secondary">&larr; Back to List</a>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Add New Agent</h4>
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
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Agent</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
