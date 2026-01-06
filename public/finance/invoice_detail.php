<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Invoice.php';
require_once __DIR__ . '/../../app/models/Payment.php';

check_auth(['finance', 'admin']);

$invoice_id = $_GET['id'] ?? null;
if (!$invoice_id) {
    header('Location: dashboard.php?error=Invoice ID is required.');
    exit;
}

$invoice = Invoice::readById($invoice_id);
if (!$invoice) {
    header('Location: dashboard.php?error=Invoice not found.');
    exit;
}

$payments = Payment::readAllByInvoice($invoice_id);
$total_paid = array_sum(array_column($payments, 'amount_paid'));
$balance_due = ($invoice['amount_idr'] ?? 0) - $total_paid;

$title = "Invoice #" . $invoice['invoice_no'];
require_once __DIR__ . '/../shared/header.php';

?>

<div class="mb-3">
    <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Finance Dashboard</a>
    <a href="print_invoice.php?id=<?php echo $invoice['id']; ?>" class="btn btn-success ms-2" target="_blank">Print Invoice</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Invoice #<?php echo htmlspecialchars($invoice['invoice_no']); ?></h4>
        <span class="badge bg-<?php
            if (($invoice['status'] ?? '') === 'PAID') echo 'success';
            elseif (($invoice['status'] ?? '') === 'PARTIALLY_PAID') echo 'warning';
            else echo 'danger';
        ?> fs-6"><?php echo $invoice['status'] ?? 'UNPAID'; ?></span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Booking PNR:</strong> <?php echo htmlspecialchars($invoice['pnr']); ?></p>
                <p><strong>Corporate / Agent:</strong> <?php echo htmlspecialchars($invoice['corporate_name'] ?? 'Individual/FID'); ?></p>
                <p><strong>Pax Count:</strong> <?php echo $invoice['total_pax']; ?></p>
            </div>
            <div class="col-md-6 text-end">
                <p><strong>Total Amount:</strong> Rp <?php echo number_format($invoice['amount_idr'], 2, ',', '.'); ?></p>
                <p><strong>Paid:</strong> Rp <?php echo number_format($total_paid, 2, ',', '.'); ?></p>
                <p><strong>Balance Due:</strong> Rp <?php echo number_format($balance_due, 2, ',', '.'); ?></p>
                <p><strong>Due Date:</strong> <?php echo $invoice['invoice_date']; ?></p>
            </div>
        </div>
        <hr>
        <h5>Payments History</h5>
        <?php if (empty($payments)): ?>
            <p>No payments recorded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo $payment['payment_date']; ?></td>
                                <td>Rp <?php echo number_format($payment['amount_paid'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($payment['reference_number']); ?></td>
                                <td><?php echo htmlspecialchars($payment['notes']); ?></td>
                                <td>
                                    <a href="print_receipt.php?id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-outline-success" target="_blank">
                                        <i class="fas fa-file-invoice-dollar"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <hr>
        <h5>Record New Payment</h5>
        <?php if (isset($_GET['payment_error'])): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($_GET['payment_error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['payment_success'])): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($_GET['payment_success']); ?></div>
        <?php endif; ?>

        <?php if ($balance_due > 0): ?>
            <form action="record_payment.php" method="POST">
                <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="amount_paid" class="form-label">Amount Paid</label>
                        <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required min="0.01" max="<?php echo $balance_due; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="payment_method" class="form-label">Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select Method</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="payment_stage" class="form-label">Payment Stage</label>
                        <select class="form-select" id="payment_stage" name="payment_stage">
                            <option value="">-- Optional --</option>
                            <option value="DP1">DP1 (First Deposit)</option>
                            <option value="DP2">DP2 (Second Deposit)</option>
                            <option value="Full Payment">Full Payment / Pelunasan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number">
                    </div>
                    <div class="col-md-6">
                        <label for="notes" class="form-label">Notes</label>
                        <input type="text" class="form-control" id="notes" name="notes">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Record Payment</button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">This invoice is fully paid.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
