<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Agent.php';

check_auth(['admin', 'finance']);

$summary = Agent::getAgentSummary();

// Calculate Global KPIs
$totalRevenue = 0;
$totalPaid = 0;
$totalPax = 0;
foreach ($summary as $row) {
    $totalRevenue += $row['total_revenue'];
    $totalPaid += $row['total_paid'];
    $totalPax += $row['total_pax'];
}
$totalOutstanding = $totalRevenue - $totalPaid;

$title = "Agent Summary (Rangkuman)";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">SUMMARY OF EACH AGENTS (Rangkuman)</h1>
        <div>
            <a href="export_agent_summary.php" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Dashboard</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body py-2">
                    <div class="text-xs text-muted text-uppercase fw-bold">Total Revenue</div>
                    <div class="h4 mb-0 fw-bold text-primary">Rp <?= number_format($totalRevenue) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body py-2">
                    <div class="text-xs text-muted text-uppercase fw-bold">Total Received</div>
                    <div class="h4 mb-0 fw-bold text-success">Rp <?= number_format($totalPaid) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body py-2">
                    <div class="text-xs text-muted text-uppercase fw-bold">Total Outstanding</div>
                    <div class="h4 mb-0 fw-bold text-danger">Rp <?= number_format($totalOutstanding) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-info border-4">
                <div class="card-body py-2">
                    <div class="text-xs text-muted text-uppercase fw-bold">Total Pax</div>
                    <div class="h4 mb-0 fw-bold text-info"><?= number_format($totalPax) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>AGENT NAME</th>
                            <th class="text-center">TOTAL PNRs</th>
                            <th class="text-center">TOTAL PAX</th>
                            <th class="text-end">TOTAL REVENUE (SELLING)</th>
                            <th class="text-end">TOTAL PAID</th>
                            <th class="text-end">BALANCE</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($summary)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">No agent data found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($summary as $row): 
                                $balance = $row['total_revenue'] - $row['total_paid'];
                            ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($row['agent_name']) ?></td>
                                    <td class="text-center"><?= $row['total_pnrs'] ?></td>
                                    <td class="text-center"><?= $row['total_pax'] ?></td>
                                    <td class="text-end">Rp <?= number_format($row['total_revenue']) ?></td>
                                    <td class="text-end text-success">Rp <?= number_format($row['total_paid']) ?></td>
                                    <td class="text-end fw-bold <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                                        Rp <?= number_format($balance) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="movement_fullview.php?q=<?= urlencode($row['agent_name']) ?>" class="btn btn-sm btn-outline-primary" title="View Movements">
                                            <i class="fas fa-search"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .text-xs { font-size: 0.7rem; }
    .table th { font-size: 0.8rem; text-transform: uppercase; }
</style>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
