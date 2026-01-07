<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth('admin');

$title = "Dashboard";
$deadlines = Movement::getUpcomingDeadlines(3);

require_once __DIR__ . '/../shared/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    </div>
</div>

<!-- Time Limit Widget -->
<div class="row mb-5" id="time-limit-section">
    <div class="col-12">
        <div class="card border-danger shadow h-100 py-2 border-start border-5">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Time Limit (Ticketing Deadline - 3 Days)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                             <?php if(empty($deadlines)): ?>
                                 <span class="text-muted small">No urgent deadlines.</span>
                             <?php else: ?>
                                <ul class="list-group list-group-flush small">
                                    <?php foreach($deadlines as $d): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong><?= htmlspecialchars($d['pnr'] ?? 'No PNR') ?></strong> 
                                                - <?= htmlspecialchars($d['agent_name'] ?? '-') ?>
                                            </span>
                                            <span class="badge bg-danger rounded-pill">
                                                <?= date('d M', strtotime($d['ticketing_deadline'])) ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                             <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Grid -->
<div class="row g-4">
    <!-- 1. Booking Request -->
    <div class="col-md-4 col-sm-6">
        <a href="booking_requests.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-primary border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-file-contract fa-3x text-primary"></i>
                    </div>
                    <h3 class="card-title text-primary">1. Booking Request</h3>
                    <p class="card-text text-muted">Manage new and existing requests</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 2. Movement -->
    <div class="col-md-4 col-sm-6">
        <a href="movement_fullview.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-success border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-plane fa-3x text-success"></i>
                    </div>
                    <h3 class="card-title text-success">2. Movement</h3>
                    <p class="card-text text-muted">Monitor flight movements</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 3. Invoice -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/create_invoice.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-warning border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice fa-3x text-warning"></i>
                    </div>
                    <h3 class="card-title text-warning">3. Invoice</h3>
                    <p class="card-text text-muted">Generate and manage invoices</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 4. Payment Report -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/dashboard.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-info border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-info"></i>
                    </div>
                    <h3 class="card-title text-info">4. Payment Report</h3>
                    <p class="card-text text-muted">View financial reports</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 5. Payment Advise -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/payment_advise_list.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-dark border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-money-check-alt fa-3x text-dark"></i>
                    </div>
                    <h3 class="card-title text-dark">5. Payment Advise</h3>
                    <p class="card-text text-muted">Manage payment advises</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 6. Rangkuman -->
    <div class="col-md-4 col-sm-6">
        <a href="agent_summary.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-secondary border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-list fa-3x text-secondary"></i>
                    </div>
                    <h3 class="card-title text-secondary">6. Summary</h3>
                    <p class="card-text text-muted">Summary & Statistics</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.05);
    }
</style>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>