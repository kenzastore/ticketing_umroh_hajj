<?php
// Split address into lines if it's stored as a single text block
$address_lines = explode("\n", $invoice['address'] ?? '');
$addr1 = $address_lines[0] ?? '';
$addr2 = $address_lines[1] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?></title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #000; line-height: 1.2; font-size: 11px; }
        .container { padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .header-table td { vertical-align: top; }
        .logo { width: 180px; }
        .recipient-block { text-align: right; }
        
        .title-ref { margin-top: 20px; margin-bottom: 20px; }
        .title-ref .proforma { font-size: 16px; font-weight: bold; text-decoration: underline; }
        
        .main-table th { border: 1px solid #000; background: #f2f2f2; padding: 5px; text-align: center; }
        .main-table td { border: 1px solid #000; padding: 4px; }
        
        .summary-row td { font-weight: bold; background: #fafafa; }
        .flight-info-label { font-weight: bold; text-decoration: underline; padding: 5px 0; }
        
        .totals-table td { padding: 4px; border: none; }
        .totals-table .label { text-align: right; padding-right: 10px; }
        .totals-table .value { border: 1px solid #000; width: 120px; text-align: right; }
        
        .section-header { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
        .bank-info { margin-top: 15px; }
        .footer-contact { margin-top: 30px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- 1) Header (Right-side Recipient + Invoice Date) -->
        <table class="header-table">
            <tr>
                <td>
                    <?php if (isset($project_root)): ?>
                    <img src="<?= PDFEngine::imageToBase64($project_root . '/public/assets/images/elang_emas.png') ?>" class="logo">
                    <?php endif; ?>
                </td>
                <td class="recipient-block">
                    <table style="width: auto; margin-left: auto;">
                        <tr><td style="text-align: right; padding-bottom: 10px;">Date: <?= htmlspecialchars($invoice['invoice_date']) ?></td></tr>
                        <tr><td style="text-align: right; font-weight: bold;">Attention to</td></tr>
                        <tr><td style="text-align: right;"><?= htmlspecialchars($invoice['attention_to'] ?: '-') ?></td></tr>
                        <tr><td style="text-align: right;"><?= htmlspecialchars($invoice['corporate_name'] ?: '-') ?></td></tr>
                        <tr><td style="text-align: right;"><?= htmlspecialchars($addr1) ?></td></tr>
                        <tr><td style="text-align: right;"><?= htmlspecialchars($addr2) ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 2) Title + Reference -->
        <div class="title-ref">
            <div class="proforma">PROFORMA INVOICE</div>
            <div>REF : <?= htmlspecialchars($invoice['ref_text'] ?: $invoice['invoice_no']) ?></div>
        </div>

        <!-- 3) Main Table Header -->
        <table class="main-table">
            <thead>
                <tr>
                    <th width="80">DOT</th>
                    <th>DESCRIPTION</th>
                    <th width="70">TOTAL PAX</th>
                    <th width="90">FARES</th>
                    <th width="110">AMOUNT IN IDR</th>
                </tr>
            </thead>
            <tbody>
                <!-- 4) Summary Line (DOT + PNR + PAX) -->
                <tr class="summary-row">
                    <td align="center"><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                    <td>PNR : <?= htmlspecialchars($invoice['pnr']) ?></td>
                    <td align="center"><?= htmlspecialchars($invoice['total_pax']) ?></td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- 5) Flight Information Section -->
                <tr>
                    <td></td>
                    <td colspan="4">
                        <div class="flight-info-label">Flight Information</div>
                        <table style="border:none; margin:0;">
                            <?php foreach ($invoice['flight_lines'] as $f): ?>
                            <tr>
                                <td style="border:none; width: 80px;"><?= $f['flight_date'] ?></td>
                                <td style="border:none; width: 60px;"><?= htmlspecialchars($f['flight_no']) ?></td>
                                <td style="border:none; width: 80px;"><?= htmlspecialchars($f['sector']) ?></td>
                                <td style="border:none;"><?= htmlspecialchars($f['time_range']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr>

                <!-- 6) Fare Breakdown + Payment Staging -->
                <?php foreach ($invoice['fare_lines'] as $fare): ?>
                <tr>
                    <td align="center"><?= htmlspecialchars($fare['dot_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fare['description']) ?></td>
                    <td align="center"><?= $fare['total_pax'] ?></td>
                    <td align="right"><?= number_format($fare['fare_amount'], 0, ",", ".") ?></td>
                    <td align="right"><?= number_format($fare['amount_idr'], 0, ",", ".") ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 7) Totals -->
        <table class="totals-table">
            <tr>
                <td class="label">Total Payment</td>
                <td class="value">Rp <?= number_format($invoice['amount_idr'], 0, ",", ".") ?></td>
            </tr>
            <tr>
                <td class="label">Balance</td>
                <td class="value">Rp <?= number_format($invoice['balance_idr'] ?? 0, 0, ",", ".") ?></td>
            </tr>
        </table>

        <!-- 8) Remarks -->
        <div class="section-header">REMARKS</div>
        <div style="margin-left: 10px;">
            1. All payment should be made by bank transfer.<br>
            2. This proforma invoice is for processing purposes only.
        </div>

        <!-- 9) Time Limit -->
        <div class="section-header">6. TIME LIMIT</div>
        <table style="width: 300px; margin-left: 10px;">
            <tr><td>Deposit-1</td><td>: <?= $invoice['deposit_1_deadline'] ?? '-' ?></td></tr>
            <tr><td>Deposit-2</td><td>: <?= $invoice['deposit_2_deadline'] ?? '-' ?></td></tr>
            <tr><td>Fullpayment</td><td>: <?= $invoice['fullpayment_deadline'] ?? '-' ?></td></tr>
            <tr><td>Ticketing</td><td>: <?= $invoice['ticketing_deadline'] ?? '-' ?></td></tr>
        </table>

        <!-- 10) Bank Transfer Details -->
        <div class="bank-info">
            Payment via bank transfer to Bank account:<br>
            <strong>Bank Mandiri</strong><br>
            Account Name: PT ELANG EMAS WISATA<br>
            Account No: 123-00-999999-1
        </div>

        <!-- 11) Footer Contact -->
        <div class="footer-contact">
            PT ELANG EMAS WISATA<br>
            Ihsan<br>
            +62 812 3456 789
        </div>
    </div>
</body>
</html>
