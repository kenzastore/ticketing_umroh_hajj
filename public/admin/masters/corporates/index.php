<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Corporate.php';

check_auth('admin');

$title = "Manage Corporates";
require_once __DIR__ . '/../../../shared/header.php';

$corporates = Corporate::readAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Corporates</h1>
    <a href="create.php" class="btn btn-primary">Add New Corporate</a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($corporates)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No corporates found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($corporates as $corp): ?>
                            <tr>
                                <td><?php echo $corp['id']; ?></td>
                                <td><?php echo htmlspecialchars($corp['name']); ?></td>
                                <td><?php echo htmlspecialchars($corp['address']); ?></td>
                                <td><?php echo $corp['created_at']; ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $corp['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                                    <a href="delete.php?id=<?php echo $corp['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this corporate?');">Delete</a>
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
