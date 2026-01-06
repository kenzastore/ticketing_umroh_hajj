<?php
// Split address into lines if it's stored as a single text block
$address_lines = explode("\n", $invoice['address'] ?? '');
$addr1 = $address_lines[0] ?? '';
$addr2 = $address_lines[1] ?? '';

// Helper for currency formatting
if (!function_exists('format_rp')) {
    function format_rp($val) {
        if ($val === null || $val === '') return '-';
        $is_neg = $val < 0;
        $formatted = number_format(abs($val), 0, ",", ".");
        return 'Rp ' . ($is_neg ? '(' : '') . $formatted . ($is_neg ? ')' : '');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice <?= htmlspecialchars($invoice['invoice_no']) ?></title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #000; line-height: 1.1; font-size: 10px; margin: 0; padding: 0; }
        .container { padding: 30px; }
        
        .top-date { text-align: right; margin-bottom: 5px; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo { width: 160px; }
        .recipient-block { text-align: right; line-height: 1.3; }
        .recipient-block .attn { margin-bottom: 2px; }
        .recipient-block .company { font-weight: bold; font-size: 11px; }
        
        .title-ref { margin-top: 15px; margin-bottom: 15px; text-align: center; }
        .title-ref .proforma { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 3px; }
        .title-ref .ref-id { font-size: 11px; font-weight: bold; font-style: italic; }
        
        .main-table { width: 100%; border: 1.5px solid #000; }
        .main-table th { border: 1px solid #000; background: #fff; padding: 8px 4px; text-align: center; font-weight: bold; font-size: 10px; }
        .main-table td { border-left: 1px solid #000; border-right: 1px solid #000; padding: 2px 6px; vertical-align: top; }
        
        .flight-info-label { font-weight: bold; font-style: italic; text-decoration: underline; margin-top: 8px; margin-bottom: 4px; display: block; }
        
        .currency-cell { white-space: nowrap; }
        .currency-cell .sym { float: left; }
        .currency-cell .val { float: right; }
        
        .totals-table { width: 100%; border-top: 1.5px solid #000; }
        .totals-table td { padding: 4px 6px; }
        .totals-table .label-col { width: 80%; text-align: right; font-weight: bold; padding-right: 15px; border: none; }
        .totals-table .rp-col { width: 3%; border-left: 1px solid #000; border-bottom: 1px solid #000; text-align: left; }
        .totals-table .val-col { width: 17%; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: right; }
        
        .remarks-section { margin-top: 15px; font-size: 9px; }
        .remarks-section .title { font-weight: bold; margin-bottom: 4px; }
        
        .time-limit-section { margin-top: 10px; }
        .time-limit-section .title { font-weight: bold; margin-bottom: 4px; }
        .time-limit-table td { padding: 1px 5px 1px 0; font-size: 9px; }
        
        .payment-info { margin-top: 15px; line-height: 1.4; font-size: 10px; }
        .payment-info .bank-name { font-weight: bold; margin-top: 5px; text-transform: uppercase; }
        
        .footer-contact { margin-top: 25px; line-height: 1.4; font-size: 10px; }
        .footer-contact .company-name { font-weight: bold; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-date"><?= date('D, d M Y', strtotime($invoice['invoice_date'] ?? 'now')) ?></div>

        <table class="header-table">
            <tr>
                <td>
                    <?php if (isset($project_root)): ?>
                    <img src="<?= PDFEngine::imageToBase64($project_root . '/public/assets/images/elang_emas.png') ?>" class="logo">
                    <?php endif; ?>
                </td>
                <td class="recipient-block">
                    <div class="attn">Attention to</div>
                    <div style="margin-bottom: 5px;"><?= htmlspecialchars($invoice['attention_to'] ?: '........') ?></div>
                    <div class="company"><?= htmlspecialchars($invoice['corporate_name'] ?: '-') ?></div>
                    <div><?= htmlspecialchars($addr1) ?></div>
                    <div><?= htmlspecialchars($addr2) ?></div>
                </td>
            </tr>
        </table>

        <div class="title-ref">
            <div class="proforma">PROFORMA INVOICE</div>
            <div class="ref-id">REF : <?= htmlspecialchars($invoice['ref_text'] ?: $invoice['invoice_no']) ?></div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="15%">DOT</th>
                    <th width="40%">DESCRIPTION</th>
                    <th width="10%">TOTAL PAX</th>
                    <th width="15%">FARES</th>
                    <th width="20%">AMOUNT IN IDR</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1: DOT + PNR + Summary Pax -->
                <tr>
                    <td align="center" style="padding-top: 10px;">
                        <?= date('d-M-y', strtotime($invoice['dot_date'] ?? $invoice['invoice_date'])) ?>
                    </td>
                    <td style="padding-top: 10px; font-weight: bold;">
                        PNR : <?= htmlspecialchars($invoice['pnr'] ?? '........') ?>
                    </td>
                    <td align="center" style="padding-top: 10px;">
                        <?= htmlspecialchars($invoice['total_pax']) ?>
                    </td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Row 2: Flight Information Header -->
                <tr>
                    <td></td>
                    <td><span class="flight-info-label">Flight Information</span></td>
                    <td></td><td></td><td></td>
                </tr>

                <!-- Flight Legs -->
                <?php foreach (($invoice['flight_lines'] ?? []) as $f): ?>
                <tr>
                    <td></td>
                    <td style="font-size: 9px; padding-left: 10px;">
                        <span style="display:inline-block; width: 70px;"><?= date('d-M-y', strtotime($f['flight_date'])) ?></span>
                        <span style="display:inline-block; width: 50px;"><?= htmlspecialchars($f['flight_no']) ?></span>
                        <span style="display:inline-block; width: 60px;"><?= htmlspecialchars($f['sector']) ?></span>
                        <span><?= htmlspecialchars($f['time_range']) ?></span>
                    </td>
                    <td></td><td></td><td></td>
                </tr>
                <?php endforeach; ?>

                <!-- Spacer -->
                <tr><td colspan="5" style="height: 10px;"></td></tr>

                <!-- Fare Lines -->
                <?php 
                foreach (($invoice['fare_lines'] ?? []) as $fare):
                    $desc = $fare['description'];
                    $is_base = (stripos($desc, 'Fares') !== false && stripos($desc, 'Deposit') === false);
                    $is_staging = (stripos($desc, 'Deposit') !== false || stripos($desc, 'Fullpayment') !== false);
                    
                    $pct = '';
                    if ($is_staging) {
                        if (stripos($desc, 'Deposit-1st') !== false) $pct = '20%';
                        elseif (stripos($desc, 'Deposit-2nd') !== false) $pct = '30%';
                        elseif (stripos($desc, 'Fullpayment') !== false) $pct = '50%';
                        
                        // If percentage already in desc, use it instead
                        if (preg_match('/(\d+)%/', $desc, $matches)) {
                            $pct = $matches[1] . '%';
                        }
                    }
                ?>
                <tr>
                    <td></td>
                    <td style="vertical-align: middle;">
                        <div style="<?= $is_base ? 'font-weight: bold;' : '' ?> height: 14px;">
                            <span style="float: left;"><?= htmlspecialchars($desc) ?></span>
                            <?php if ($is_staging): ?>
                                <span style="float: right; padding-right: 20px;"><?= $pct ?></span>
                            <?php endif; ?>
                            <div style="clear: both;"></div>
                        </div>
                    </td>
                    <td align="center" style="<?= $is_base ? 'font-weight: bold;' : '' ?> vertical-align: middle;">
                        <?= $fare['total_pax'] ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="currency-cell" style="<?= $is_base ? 'font-weight: bold;' : '' ?>">
                            <span class="sym">Rp</span>
                            <span class="val">
                                <?= $fare['fare_amount'] < 0 ? '(' : '' ?><?= number_format(abs($fare['fare_amount']), 0, ",", ".") ?><?= $fare['fare_amount'] < 0 ? ')' : '' ?>
                            </span>
                            <div style="clear: both;"></div>
                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="currency-cell" style="<?= $is_base ? 'font-weight: bold;' : '' ?>">
                            <span class="sym">Rp</span>
                            <span class="val">
                                <?= $fare['amount_idr'] < 0 ? '(' : '' ?><?= number_format(abs($fare['amount_idr']), 0, ",", ".") ?><?= $fare['amount_idr'] < 0 ? ')' : '' ?>
                            </span>
                            <div style="clear: both;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- Spacer row with border bottom -->
                <tr>
                    <td style="height: 40px; border-bottom: 1.5px solid #000;"></td>
                    <td style="border-bottom: 1.5px solid #000;"></td>
                    <td style="border-bottom: 1.5px solid #000;"></td>
                    <td style="border-bottom: 1.5px solid #000;"></td>
                    <td style="border-bottom: 1.5px solid #000;"></td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td class="label-col">Total Payment</td>
                <td class="rp-col">Rp</td>
                <td class="val-col"><?= number_format($invoice['amount_idr'], 0, ",", ".") ?></td>
            </tr>
            <tr>
                <td class="label-col">Balance</td>
                <td class="rp-col">Rp</td>
                <td class="val-col"><?= number_format($invoice['balance_idr'] ?? 0, 0, ",", ".") ?></td>
            </tr>
        </table>

        <div class="remarks-section">
            <div class="title">REMARKS</div>
            <div>1. HARGA SEWAKTU WAKTU DAPAT BERUBAH TERGANTUNG PERUBAHAN TAX DAN NILAI KURS</div>
            <div>2. DEPOSIT/ FULLPAYMENT TIDAK DAPAT DIREFUND DAN TIDAK DAPAT DIALIHKAN</div>
            <div>3. MATERIALIZATION ADALAH 80% DARI SEATS YANG TELAH DIDEPOSITKAN</div>
            <div>4. KONFIRMATION MATERIALIZATION 35 HARI SEBELUM KEBERANGAKTAN</div>
            <div>5. SCHEDULES FLIGHT DETAILS AS ABOVE & SUBJECT TO CHANGES</div>
        </div>

        <div class="time-limit-section">
            <div class="title">6. TIME LIMIT</div>
            <table class="time-limit-table">
                <tr><td>- DEPOSIT-1</td><td>: <?= htmlspecialchars($invoice['deposit_1_deadline'] ?? 'Saturday, 03 January 2026') ?></td></tr>
                <tr><td>- DEPOSIT-2</td><td>: <?= htmlspecialchars($invoice['deposit_2_deadline'] ?? 'Friday, 23 January 2026') ?></td></tr>
                <tr><td>- FULLPAYMENT</td><td>: <?= htmlspecialchars($invoice['fullpayment_deadline'] ?? 'Friday, 03 April 2026') ?></td></tr>
                <tr><td>- TICKETING</td><td>: <?= htmlspecialchars($invoice['ticketing_deadline'] ?? 'Friday, 24 April 2026') ?></td></tr>
            </table>
        </div>

        <div class="payment-info">
            Payment via bank transfer to Bank account :<br>
            <div class="bank-name">MANDIRI CAPEM SIDOARJO PAHLAWAN</div>
            <div>PT ELANG EMAS MANDIRI INDONESIA</div>
            <div>Account No : 141-00-0102110-4</div>
        </div>

        <div class="footer-contact">
            <span class="company-name">PT ELANG EMAS MANDIRI INDONESIA</span>
            <div>ABDUL HARIS NIRA</div>
            <div>081-338-007-128</div>
        </div>
    </div>
</body>
</html>