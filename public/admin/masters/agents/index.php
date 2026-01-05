<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../../app/models/Agent.php';

check_auth('admin');

$title = "Manage Agents";
require_once __DIR__ . '/../../../shared/header.php';

$agents = Agent::readAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Agents</h1>
    <a href="create.php" class="btn btn-primary">Add New Agent</a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Skyagent ID</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($agents)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No agents found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agents as $agent): ?>
                            <tr>
                                <td><?php echo $agent['id']; ?></td>
                                <td><?php echo htmlspecialchars($agent['name']); ?></td>
                                <td><?php echo htmlspecialchars($agent['skyagent_id']); ?></td>
                                <td><?php echo htmlspecialchars($agent['phone']); ?></td>
                                <td><?php echo htmlspecialchars($agent['email']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $agent['id']; ?>" class="btn btn-sm btn-warning text-white">Edit</a>
                                    <a href="delete.php?id=<?php echo $agent['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this agent?');">Delete</a>
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
