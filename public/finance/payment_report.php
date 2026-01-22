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

// Helper for rendering tables
function renderPaymentTable($title, $lines, $legs, $timeLimitLabel) {
    $totalDebet = 0;
    foreach($lines as $l) $totalDebet += $l['debit_amount'];
    
    // Determine row count (ensure enough rows for flight legs too)
    $rowCount = max(count($legs), count($lines), 1);
    ?>
    <div class="mb-4">
        <div class="bg-primary text-white p-2 mb-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold"><?= $title ?></h6>
            <?php if($title == 'ELANG EMAS WISATA'): ?>
                <span class="badge bg-light text-primary">SALES / INTERNAL</span>
            <?php else: ?>
                <span class="badge bg-light text-dark">COST / AIRLINE</span>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-center mb-0" style="font-size: 0.75rem;">
                <thead class="bg-light text-uppercase" style="font-size: 0.65rem;">
                    <tr>
                        <th rowspan="2" width="30">NO</th>
                        <th rowspan="2">DATE DEP/ARR</th>
                        <th rowspan="2">FLIGHT NO</th>
                        <th rowspan="2">CITY</th>
                        <th rowspan="2">TIME</th>
                        <th rowspan="2">REFERENCE ID</th>
                        <th rowspan="2">TIME LIMIT PAYMENT BY <?= $timeLimitLabel ?></th>
                        <th rowspan="2">DATE OF PAYMENT</th>
                        <th rowspan="2">TTL PAX</th>
                        <th rowspan="2">REMARKS</th>
                        <th rowspan="2">FARE PER-PAX</th>
                        <th rowspan="2">DEBET</th>
                        <th colspan="3" class="bg-info text-white">FROM</th>
                        <th colspan="3" class="bg-success text-white">TO</th>
                    </tr>
                    <tr style="font-size: 0.6rem;">
                        <th class="bg-info-subtle">BANK</th>
                        <th class="bg-info-subtle">ACC NAME</th>
                        <th class="bg-info-subtle">ACC NO</th>
                        <th class="bg-success-subtle">BANK</th>
                        <th class="bg-success-subtle">ACC NAME</th>
                        <th class="bg-success-subtle">ACC NO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $rowCount; $i++): 
                        $leg = $legs[$i] ?? null;
                        $line = $lines[$i] ?? null;
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $leg ? date('d-M-y', strtotime($leg['scheduled_departure'])) : '-' ?></td>
                        <td><?= $leg['flight_no'] ?? '-' ?></td>
                        <td><?= $leg['sector'] ?? '-' ?></td>
                        <td><?= $leg['time_range'] ?? '-' ?></td>
                        
                        <td class="text-primary fw-bold"><?= $line['reference_id'] ?? '-' ?></td>
                        <td class="text-danger fw-bold"><?= ($line && $line['time_limit_date']) ? date('d-M-y', strtotime($line['time_limit_date'])) : '-' ?></td>
                        <td><?= ($line && $line['payment_date']) ? date('d-M-y', strtotime($line['payment_date'])) : '-' ?></td>
                        <td><?= $line['total_pax'] ?? '-' ?></td>
                        <td class="text-start"><?= $line['remarks'] ?? '-' ?></td>
                        <td class="text-end"><?= ($line && $line['fare_per_pax']) ? 'Rp ' . number_format($line['fare_per_pax']) : '-' ?></td>
                        <td class="text-end fw-bold"><?= ($line && $line['debit_amount']) ? 'Rp ' . number_format($line['debit_amount']) : '-' ?></td>
                        
                        <!-- Bank FROM -->
                        <td class="text-xs"><?= $line['bank_from'] ?? '-' ?></td>
                        <td class="text-xs"><?= $line['bank_from_name'] ?? '-' ?></td>
                        <td class="text-xs"><?= $line['bank_from_number'] ?? '-' ?></td>
                        
                        <!-- Bank TO -->
                        <td class="text-xs"><?= $line['bank_to'] ?? '-' ?></td>
                        <td class="text-xs"><?= $line['bank_to_name'] ?? '-' ?></td>
                        <td class="text-xs"><?= $line['bank_to_number'] ?? '-' ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="11" class="text-end fw-bold text-uppercase">Balance</td>
                        <td class="text-end fw-bold text-danger">Rp <?= number_format($totalDebet) ?></td>
                        <td colspan="6" class="bg-white border-0"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php
    return $totalDebet;
}
?>

<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h1 class="h3">Payment Report</h1>
        <div>
            <a href="edit_payment_report.php?movement_id=<?= $movementId ?>" class="btn btn-primary btn-sm me-2"><i class="fas fa-edit"></i> Edit Report</a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print Report</button>
            <a href="/finance/dashboard.php" class="btn btn-outline-primary btn-sm">Back to Dashboard</a>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4 border p-3 bg-light rounded mx-0" style="font-size: 0.85rem;">
        <div class="col-md-4">
            <table class="table table-sm table-borderless mb-0">
                <tr><th width="150">NAMA AGENT</th><td>: <span class="fw-bold text-primary"><?= htmlspecialchars($report['agent_name'] ?? '-') ?></span></td></tr>
                <tr><th>DATE OF REQUEST</th><td>: <?= date('l, d F Y', strtotime($report['created_at'])) ?></td></tr>
                <tr><th>DATE OF CONFIMED</th><td>: <?= $report['created_date'] ? date('l, d F Y', strtotime($report['created_date'])) : '-' ?></td></tr>
            </table>
        </div>
        <div class="col-md-4">
            <table class="table table-sm table-borderless mb-0">
                <tr><th width="100">PROGRAM</th><td>: <?= htmlspecialchars($report['pattern_code'] ?? '-') ?></td></tr>
                <tr><th>PNR</th><td>: <span class="fw-bold"><?= htmlspecialchars($report['pnr'] ?? '-') ?></span></td></tr>
                <tr><th>TOUR CODE</th><td>: <span class="fw-bold"><?= htmlspecialchars($report['tour_code'] ?? '-') ?></span></td></tr>
            </table>
        </div>
        <div class="col-md-4 text-end">
            <div class="p-2 border bg-white rounded d-inline-block text-start">
                <small class="text-muted d-block">REF ID</small>
                <span class="h5 fw-bold text-primary mb-0"><?= $report['pnr'] ?></span>
            </div>
        </div>
    </div>

    <?php 
    $legs = $report['legs'] ?? [];
    $salesBalance = renderPaymentTable('ELANG EMAS WISATA', $report['sales_lines'] ?? [], $legs, 'ELANG EMAS WISATA'); 
    $costBalance = renderPaymentTable('COST / AIRLINE', $report['cost_lines'] ?? [], $legs, 'SUPPLIER'); 
    
    $incentive = $salesBalance - $costBalance; // Based on logic A
    $discount = $report['discount_amount'] ?? 0;
    $finalBalance = $incentive - $discount;
    ?>

    <!-- Summary Section -->
    <div class="row mt-4">
        <div class="col-md-6 offset-md-6">
            <div class="card shadow-sm border-primary">
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr class="bg-light">
                            <td class="ps-3 py-2 fw-bold text-primary">INCENTIVE (Profit)</td>
                            <td class="pe-3 py-2 text-end fw-bold">Rp <?= number_format($incentive) ?></td>
                        </tr>
                        <tr>
                            <td class="ps-3 py-2 text-muted">DISCOUNT</td>
                            <td class="pe-3 py-2 text-end text-muted">Rp <?= number_format($discount) ?></td>
                        </tr>
                        <tr class="border-top table-primary">
                            <td class="ps-3 py-2 h5 fw-bold">FINAL BALANCE</td>
                            <td class="pe-3 py-2 text-end h5 fw-bold">Rp <?= number_format($finalBalance) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-xs { font-size: 0.65rem; }
    .bg-info-subtle { background-color: #e0f2f1 !important; }
    .bg-success-subtle { background-color: #f1f8e9 !important; }
    @media print {
        .navbar, .d-print-none, .btn { display: none !important; }
        .container-fluid { width: 100%; padding: 0; margin: 0; }
        .table { font-size: 0.6rem !important; }
        .card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>