<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/User.php';

check_auth('admin');

$title = "Manage Users";
require_once __DIR__ . '/../../../shared/header.php';

$users = User::readAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Users</h1>
    <a href="create.php" class="btn btn-primary">Add New User</a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($user['role_name']); ?></span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                                    <?php if ($user['username'] !== 'admin'): // Prevent deleting main admin ?>
                                        <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>
