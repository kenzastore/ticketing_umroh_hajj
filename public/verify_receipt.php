<?php
require_once __DIR__ . '/../includes/db_connect.php';

$token = $_GET['token'] ?? '';
$isValid = false;
$payment = null;
$invoice = null;

if (!empty($token)) {
    $stmt = $pdo->prepare("
        SELECT p.*, i.invoice_no, i.corporate_name, i.pnr, i.tour_code 
        FROM payments p
        JOIN invoices i ON p.invoice_id = i.id
        WHERE p.receipt_hash = ?
    ");
    $stmt->execute([$token]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($payment) {
        $isValid = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Verification | EEMW Ticketing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .verify-card { max-width: 500px; width: 100%; margin: auto; border-radius: 15px; overflow: hidden; }
        .status-header { padding: 30px; text-align: center; }
        .valid { background-color: #28a745; color: white; }
        .invalid { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="verify-card shadow bg-white">
        <?php if ($isValid): ?>
            <div class="status-header valid">
                <h2 class="mb-0">✅ VERIFIED</h2>
                <p class="small opacity-75">This is a valid EEMW digital receipt</p>
            </div>
            <div class="p-4">
                <table class="table table-borderless small mb-0">
                    <tr><th width="40%">Amount Paid</th><td class="fw-bold text-success h4">Rp <?= number_format($payment['amount_paid'], 2) ?></td></tr>
                    <tr><th>Date</th><td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td></tr>
                    <tr><th>PNR</th><td><strong><?= htmlspecialchars($payment['pnr']) ?></strong></td></tr>
                    <tr><th>Invoice No</th><td><?= htmlspecialchars($payment['invoice_no']) ?></td></tr>
                    <tr><th>Received From</th><td><?= htmlspecialchars($payment['corporate_name'] ?: 'N/A') ?></td></tr>
                    <tr><th>Token</th><td class="text-muted text-break"><?= $token ?></td></tr>
                </table>
            </div>
        <?php else: ?>
            <div class="status-header invalid">
                <h2 class="mb-0">❌ INVALID RECEIPT</h2>
                <p class="small opacity-75">Tampered or non-existent token</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-muted">The verification token provided does not match any record in our system.</p>
                <div class="alert alert-warning small">
                    If you believe this is an error, please contact EEMW Support.
                </div>
            </div>
        <?php endif; ?>
        <div class="p-3 bg-light text-center">
            <span class="text-muted small">© <?= date('Y') ?> EEMW Ticketing Management</span>
        </div>
    </div>
</body>
</html>
