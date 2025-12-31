<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
check_auth('admin');

// Fetch requests with current booking status
$query = "
    SELECT r.*, b.status, b.pnr_code, a.name as agent_name 
    FROM requests r 
    JOIN bookings b ON r.id = b.request_id 
    LEFT JOIN agents a ON r.agent_id = a.id 
    ORDER BY r.created_at DESC
";
$stmt = $pdo->query($query);
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ticketing Umroh Haji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">Ticketing System</span>
            <div class="d-flex">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>All Requests</h1>
            <a href="new_request.php" class="btn btn-primary">Create New Request</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Agent / FID</th>
                        <th>Pax</th>
                        <th>Travel Dates</th>
                        <th>Status</th>
                        <th>PNR</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?></td>
                                <td><?php echo $r['pax_count']; ?></td>
                                <td>
                                    <?php echo $r['travel_date_start']; ?> to <?php echo $r['travel_date_end']; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo ($r['status'] === 'PNR_ISSUED' ? 'success' : 
                                             ($r['status'] === 'NEW' ? 'primary' : 'warning')); 
                                    ?>">
                                        <?php echo $r['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($r['pnr_code'] ?? '-'); ?></td>
                                <td>
                                    <a href="request_detail.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info text-white">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>