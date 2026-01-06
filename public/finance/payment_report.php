<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/PaymentReport.php';

check_auth(['finance', 'admin']);

$movementId = $_GET['movement_id'] ?? null;
if (!$movementId) {
    die("Movement ID required.");
}

$report = PaymentReport::getReportByMovementId($movementId);
if (!$report) {
    die("Report not found.");
}

$title = "Payment Report - " . ($report['pnr'] ?? 'N/A');
require_once __DIR__ . '/../shared/header.php';
?>

<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h1 class="h3">Payment Report</h1>
        <div>
            <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print Report</button>
            <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Back to Dashboard</a>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4 border p-3 bg-light rounded">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr><th width="150">UPDATED</th><td>: <?= date('l, d F Y') ?></td></tr>
                <tr><th>NAMA AGENT</th><td>: <span class="fw-bold text-primary"><?= htmlspecialchars($report['agent_name'] ?? '-') ?></span></td></tr>
                <tr><th>DATE OF REQUEST</th><td>: <?= date('l, d F Y', strtotime($report['created_at'])) ?></td></tr>
                <tr><th>DATE OF CONFIMED</th><td>: <?= $report['created_date'] ? date('l, d F Y', strtotime($report['created_date'])) : '-' ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr><th width="150">PROGRAM</th><td>: <?= htmlspecialchars($report['pattern_code'] ?? '18DAYS') ?> <span class="ms-3">REF ID : <span class="text-primary"><?= $report['pnr'] ?></span></span></td></tr>
                <tr><th>PNR</th><td>: <span class="fw-bold"><?= htmlspecialchars($report['pnr'] ?? '-') ?></span></td></tr>
                <tr><th>TOUR CODE</th><td>: <span class="fw-bold"><?= htmlspecialchars($report['tour_code'] ?? '-') ?></span></td></tr>
                <tr><th>AUTHORIZED</th><td>: <?= $_SESSION['username'] ?></td></tr>
            </table>
        </div>
    </div>

    <!-- Flight & Accounting Section -->
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle text-center" style="font-size: 0.85rem;">
            <thead class="table-dark">
                <tr>
                    <th>NO</th>
                    <th>DATE DEP/ARR</th>
                    <th>FLIGHT NO</th>
                    <th>CITY</th>
                    <th>TIME</th>
                    <th>REFERENCE ID</th>
                    <th>DATE OF PAYMENT</th>
                    <th>TTL PAX</th>
                    <th>REMARKS</th>
                    <th>FARE PER-PAX</th>
                    <th>DEBET</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $legs = $report['legs'] ?? [];
                $lines = $report['report_lines'] ?? [];
                $rowCount = max(count($legs), count($lines), 5); // Minimum rows
                
                $totalDebet = 0;
                
                for ($i = 0; $i < $rowCount; $i++): 
                    $leg = $legs[$i] ?? null;
                    $line = $lines[$i] ?? null;
                    if ($line) $totalDebet += $line['debit_amount'];
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $leg ? date('d-M-y', strtotime($leg['scheduled_departure'])) : '-' ?></td>
                    <td><?= $leg['flight_no'] ?? '-' ?></td>
                    <td><?= $leg['sector'] ?? '-' ?></td>
                    <td><?= $leg['time_range'] ?? '-' ?></td>
                    
                    <td class="text-primary fw-bold"><?= $line['reference_id'] ?? '-' ?></td>
                    <td><?= ($line && $line['payment_date']) ? date('d-M-y', strtotime($line['payment_date'])) : '-' ?></td>
                    <td><?= $line['total_pax'] ?? '-' ?></td>
                    <td class="text-start"><?= $line['remarks'] ?? '-' ?></td>
                    <td class="text-end"><?= ($line && $line['fare_per_pax']) ? 'Rp ' . number_format($line['fare_per_pax']) : '-' ?></td>
                    <td class="text-end fw-bold"><?= ($line && $line['debit_amount']) ? 'Rp ' . number_format($line['debit_amount']) : '-' ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="10" class="text-end fw-bold text-uppercase">Balance</td>
                    <td class="text-end fw-bold text-danger">Rp <?= number_format($totalDebet) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Bank Information & Summaries (Simplified for POC) -->
    <div class="row mt-4">
        <div class="col-md-7">
            <h6 class="fw-bold">BANK INFORMATION</h6>
            <table class="table table-bordered table-sm text-xs">
                <thead class="bg-light">
                    <tr>
                        <th>FROM</th>
                        <th>BANK ACCOUNT NAME</th>
                        <th>TO</th>
                        <th>BANK ACCOUNT NAME</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lines as $line): if(!$line['bank_from']) continue; ?>
                    <tr>
                        <td><?= htmlspecialchars($line['bank_from']) ?></td>
                        <td><?= htmlspecialchars($line['bank_from_name']) ?></td>
                        <td><?= htmlspecialchars($line['bank_to']) ?></td>
                        <td><?= htmlspecialchars($line['bank_to_name']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($lines)): ?>
                        <tr><td colspan="4" class="text-center text-muted">- No Bank Info Recorded -</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-5">
            <div class="card bg-yellow-light p-3">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-primary">INCENTIVE</th><td class="text-end fw-bold">Rp 3,500,000</td></tr>
                    <tr><th class="text-muted">DISCOUNT</th><td class="text-end text-muted">Rp 0</td></tr>
                    <tr class="border-top"><th class="h5">FINAL BALANCE</th><td class="text-end h5 fw-bold text-success">Rp <?= number_format($totalDebet) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-yellow-light { background-color: #fffde7; border: 1px solid #fff59d; }
    .text-xs { font-size: 0.75rem; }
    @media print {
        .navbar, .d-print-none { display: none !important; }
        .container-fluid { width: 100%; padding: 0; }
        .table { font-size: 0.7rem; }
    }
</style>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
