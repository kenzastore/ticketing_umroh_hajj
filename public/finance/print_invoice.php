<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Invoice.php';
require_once __DIR__ . '/../../app/models/Payment.php';

check_auth(['finance', 'admin']);

$invoice_id = $_GET['id'] ?? null;
if (!$invoice_id) {
    die('Invoice ID is required.');
}

$invoice = Invoice::readById($invoice_id);
if (!$invoice) {
    die('Invoice not found.');
}

$payments = Payment::readAllByInvoice($invoice_id);
$total_paid = array_sum(array_column($payments, 'amount_paid'));
$balance_due = $invoice['amount_total'] - $total_paid;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $invoice['invoice_number']; ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #555;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            line-height: 24px;
        }

        .invoice-box {
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            padding: 30px;
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
            .invoice-box {
                box-shadow: none;
                border: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer;">Close</button>
    </div>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <!-- Placeholder Logo -->
                                <div style="background: #333; color: #fff; width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 24px;">LOGO</div>
                            </td>
                            
                            <td>
                                <strong>Invoice #: <?php echo htmlspecialchars($invoice['invoice_number']); ?></strong><br>
                                Created: <?php echo date('d M Y', strtotime($invoice['created_at'])); ?><br>
                                Due Date: <?php echo date('d M Y', strtotime($invoice['due_date'])); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Billed To:</strong><br>
                                <?php echo htmlspecialchars($invoice['agent_name'] ?? 'Individual / FID'); ?><br>
                                <?php if (!empty($invoice['pnr_code'])): ?>
                                    PNR: <?php echo htmlspecialchars($invoice['pnr_code']); ?><br>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <strong>Provider:</strong><br>
                                Umroh & Hajj Ticketing Co.<br>
                                Jakarta, Indonesia<br>
                                support@example.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Description</td>
                <td>Price</td>
            </tr>
            
            <tr class="item">
                <td>Flight Booking (<?php echo $invoice['pax_count']; ?> Pax)</td>
                <td>Rp <?php echo number_format($invoice['amount_total'], 2, ',', '.'); ?></td>
            </tr>
            
            <tr class="total">
                <td></td>
                <td>
                   Total: Rp <?php echo number_format($invoice['amount_total'], 2, ',', '.'); ?>
                </td>
            </tr>

            <!-- Payments Section -->
             <?php if (!empty($payments)): ?>
                <tr class="heading">
                    <td>Payment History</td>
                    <td>Amount Paid</td>
                </tr>
                <?php foreach ($payments as $payment): ?>
                <tr class="details">
                    <td>
                        <?php echo date('d M Y', strtotime($payment['payment_date'])); ?> - <?php echo htmlspecialchars($payment['payment_method']); ?>
                        <br>
                        <small style="color: #999;">Ref: <?php echo htmlspecialchars($payment['reference_number'] ?? '-'); ?></small>
                        <?php if (!empty($payment['receipt_hash'])): ?>
                            <br><small style="font-family: monospace; font-size: 10px; color: #555;">Token: <?php echo substr($payment['receipt_hash'], 0, 16); ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td>(Rp <?php echo number_format($payment['amount_paid'], 2, ',', '.'); ?>)</td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="total">
                    <td></td>
                    <td>
                       Balance Due: Rp <?php echo number_format($balance_due, 2, ',', '.'); ?>
                    </td>
                </tr>
             <?php endif; ?>

        </table>

        <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #aaa;">
            <p>Thank you for your business.</p>
            <p>This is a computer-generated document. No signature is required.</p>
        </div>
    </div>
</body>
</html>
