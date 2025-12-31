<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/User.php';
require_once __DIR__ . '/../../../../app/models/Role.php';

check_auth('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$user = User::readById($id);
if (!$user) {
    die("User not found.");
}

$roles = Role::readAll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => $_POST['username'] ?? '',
        'full_name' => $_POST['full_name'] ?? '',
        'role_id' => $_POST['role_id'] ?? ''
    ];

    // Only update password if provided
    if (!empty($_POST['password'])) {
        $data['password'] = $_POST['password'];
    }

    if (empty($data['username']) || empty($data['role_id'])) {
        $error = 'Username and Role are required.';
    } else {
        if (User::update($id, $data)) {
            $success = 'User updated successfully.';
            $user = User::readById($id); // Refresh data
            header("refresh:1;url=index.php");
        } else {
            $error = 'Failed to update user or no changes made.';
        }
    }
}

$title = "Edit User";
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="mb-3">
    <a href="index.php" class="btn btn-secondary">&larr; Back to List</a>
</div>

<div class="card shadow">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Edit User</h4>
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
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Role *</label>
                <select name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>" <?php echo ($role['id'] == $user['role_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
