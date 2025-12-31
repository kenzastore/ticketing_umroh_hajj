<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/User.php';
require_once __DIR__ . '/../../../../app/models/Role.php';

check_auth('admin');

$roles = Role::readAll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => $_POST['username'] ?? '',
        'password' => $_POST['password'] ?? '',
        'full_name' => $_POST['full_name'] ?? '',
        'role_id' => $_POST['role_id'] ?? ''
    ];

    if (empty($data['username']) || empty($data['password']) || empty($data['role_id'])) {
        $error = 'Username, Password, and Role are required.';
    } else {
        if (User::create($data)) {
            $success = 'User created successfully.';
            header("refresh:1;url=index.php");
        } else {
            $error = 'Failed to create user. Username might already exist.';
        }
    }
}

$title = "Add New User";
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="mb-3">
    <a href="index.php" class="btn btn-secondary">&larr; Back to List</a>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Add New User</h4>
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
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role *</label>
                <select name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save User</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
