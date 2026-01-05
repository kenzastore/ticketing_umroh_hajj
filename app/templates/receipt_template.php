<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Official Receipt</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; text-align: center; color: #333; }
        .receipt-box { border: 1px solid #ddd; padding: 30px; max-width: 500px; margin: auto; }
        .logo { font-size: 24px; font-weight: bold; color: #0056b3; margin-bottom: 5px; }
        .official { font-size: 18px; font-weight: bold; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .amount { font-size: 32px; font-weight: bold; margin: 20px 0; color: #28a745; }
        .details { text-align: left; margin-bottom: 30px; }
        .details table { width: 100%; }
        .details td { padding: 5px 0; }
        .qr-placeholder { background: #eee; width: 100px; height: 100px; margin: 20px auto; display: block; border: 1px solid #ccc; line-height: 100px; font-size: 10px; }
        .token { font-family: monospace; font-size: 10px; color: #999; word-break: break-all; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="logo">ELANG EMAS WISATA</div>
        <div class="official">OFFICIAL RECEIPT</div>
        
        <div class="amount">Rp <?= number_format($payment['amount_paid'], 2) ?></div>
        
        <div class="details">
            <table>
                <tr><td width="40%">Date:</td><td><?= $payment['payment_date'] ?></td></tr>
                <tr><td>Invoice No:</td><td><?= htmlspecialchars($invoice['invoice_no']) ?></td></tr>
                <tr><td>Received From:</td><td><?= htmlspecialchars($invoice['corporate_name'] ?: 'N/A') ?></td></tr>
                <tr><td>Payment Method:</td><td><?= htmlspecialchars($payment['payment_method']) ?></td></tr>
                <tr><td>Reference:</td><td><?= htmlspecialchars($payment['reference_number'] ?: '-') ?></td></tr>
            </table>
        </div>

        <div class="qr-placeholder">VERIFICATION QR</div>
        <div class="token">TOKEN: <?= $payment['receipt_hash'] ?></div>
        
        <div style="margin-top: 30px; font-size: 12px; color: #777;">
            This is a valid digital proof of payment.
        </div>
    </div>
</body>
</html>
