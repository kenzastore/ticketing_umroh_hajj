<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth('admin');

$title = "Admin Dashboard - Booking Requests";

// Fetch all booking requests
$requests = BookingRequest::readAll();

require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Booking Requests</h1>
    <div>
        <a href="export_requests.php" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> Export to Excel</a>
        <a href="new_request.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create New Request</a>
    </div>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] === 'request_created'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Booking request created successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap align-middle" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2" class="align-middle">NO</th>
                        <th rowspan="2" class="align-middle">CORPORATE NAME</th>
                        <th rowspan="2" class="align-middle">Agent Name</th>
                        <th rowspan="2" class="align-middle">Skyagent ID</th>
                        
                        <th colspan="3" class="text-center bg-warning text-dark">FLIGHT LEG 1</th>
                        <th colspan="3" class="text-center bg-warning text-dark">FLIGHT LEG 2</th>
                        <th colspan="3" class="text-center bg-warning text-dark">FLIGHT LEG 3</th>
                        <th colspan="3" class="text-center bg-warning text-dark">FLIGHT LEG 4</th>
                        
                        <th rowspan="2" class="align-middle">Group Size</th>
                        <th rowspan="2" class="align-middle">TCP</th>
                        <th rowspan="2" class="align-middle">DURATION</th>
                        <th rowspan="2" class="align-middle">ADD 1</th>
                        <th rowspan="2" class="align-middle">TTL DAYS</th>
                        <th rowspan="2" class="align-middle">Actions</th>
                    </tr>
                    <tr>
                        <!-- Leg 1 -->
                        <th class="bg-warning text-dark">DATE</th>
                        <th class="bg-warning text-dark">FLIGHT</th>
                        <th class="bg-warning text-dark">SECTOR</th>
                        <!-- Leg 2 -->
                        <th class="bg-warning text-dark">DATE</th>
                        <th class="bg-warning text-dark">FLIGHT</th>
                        <th class="bg-warning text-dark">SECTOR</th>
                        <!-- Leg 3 -->
                        <th class="bg-warning text-dark">DATE</th>
                        <th class="bg-warning text-dark">FLIGHT</th>
                        <th class="bg-warning text-dark">SECTOR</th>
                        <!-- Leg 4 -->
                        <th class="bg-warning text-dark">DATE</th>
                        <th class="bg-warning text-dark">FLIGHT</th>
                        <th class="bg-warning text-dark">SECTOR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="21" class="text-center text-muted py-4">No booking requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $index => $r): ?>
                            <tr>
                                <td><?php echo $index + 1; // Or $r['request_no'] ?></td>
                                <td><?php echo htmlspecialchars($r['corporate_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?></td>
                                <td><?php echo htmlspecialchars($r['skyagent_id'] ?? '-'); ?></td>

                                <?php 
                                // Render up to 4 legs
                                $legs = $r['legs'] ?? [];
                                for ($i = 0; $i < 4; $i++): 
                                    $leg = $legs[$i] ?? null;
                                ?>
                                    <td><?php echo $leg ? date('d/m/Y', strtotime($leg['flight_date'])) : ''; ?></td>
                                    <td><?php echo $leg ? htmlspecialchars($leg['flight_no']) : ''; ?></td>
                                    <td><?php echo $leg ? htmlspecialchars($leg['sector']) : ''; ?></td>
                                <?php endfor; ?>

                                <td><?php echo $r['group_size']; ?></td>
                                <td><?php echo number_format($r['tcp'] ?? 0, 2); ?></td>
                                <td><?php echo $r['duration_days']; ?></td>
                                <td><?php echo $r['add1_days']; ?></td>
                                <td><?php echo $r['ttl_days']; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="request_detail.php?id=<?php echo $r['id']; ?>" class="btn btn-info text-white" title="View"><i class="fas fa-eye"></i></a>
                                        <!-- Email Button Placeholder -->
                                        <a href="mailto:?subject=Booking Request <?php echo $r['request_no']; ?>" class="btn btn-warning" title="Email"><i class="fas fa-envelope"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
