<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Corporate.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$corporate = Corporate::readById($id);
if (!$corporate) {
    die("Corporate not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'address' => $_POST['address'] ?? ''
    ];

    if (empty($data['name'])) {
        $error = 'Corporate Name is required.';
    } else {
        if (Corporate::update($id, $data)) {
            $success = 'Corporate updated successfully.';
            $corporate = Corporate::readById($id); // Refresh data
            header("refresh:1;url=index.php");
        } else {
            $error = 'Failed to update corporate or no changes made.';
        }
    }
}

$title = "Edit Corporate";
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="mb-3">
    <a href="index.php" class="btn btn-secondary">&larr; Back to List</a>
</div>

<div class="card shadow">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Edit Corporate</h4>
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
                <label class="form-label">Corporate Name *</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($corporate['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($corporate['address']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Corporate</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
