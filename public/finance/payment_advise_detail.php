<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/PaymentAdvise.php';

check_auth(['finance', 'admin']);

$id = $_GET['id'] ?? null;
if (!$id) die("ID required.");

$advise = PaymentAdvise::readById($id);
if (!$advise) die("Advise not found.");

$title = "Payment Advise - " . $advise['pnr'];
require_once __DIR__ . '/../shared/header.php';
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h1>Payment Advise</h1>
        <div>
            <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
            <a href="payment_advise_list.php" class="btn btn-outline-primary btn-sm">List</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success d-print-none">Payment advise created successfully.</div>
    <?php endif; ?>

    <!-- PDF Layout Implementation -->
    <div class="payment-advise-doc border p-5 bg-white shadow-sm mx-auto" style="max-width: 1000px; font-family: sans-serif;">
        <div class="row mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless small">
                    <tr><th width="150">AGENT NAME</th><td>: <?= htmlspecialchars($advise['agent_name']) ?></td></tr>
                    <tr><th>TOUR CODE</th><td>: <?= htmlspecialchars($advise['tour_code']) ?></td></tr>
                    <tr><th>DATE CREATED</th><td>: <?= date('l, d F Y', strtotime($advise['date_created'])) ?></td></tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless small">
                    <tr><th width="150">DATE E-MAIL TO AIRLINE</th><td>: <?= $advise['date_email_to_airline'] ? date('l, d F Y', strtotime($advise['date_email_to_airline'])) : '-' ?></td></tr>
                    <tr><th>EMAIL CONFIRMATION</th><td>: <?= htmlspecialchars($advise['email_confirmation_from_airline'] ?: '-') ?></td></tr>
                </table>
            </div>
        </div>

        <p class="mt-4 fw-bold">WE HAVE JUST MAKE A BALANCE PAYMENT VIA BANK TRANSFER AS BELOW :</p>

        <div class="row">
            <div class="col-7">
                <table class="table table-bordered table-sm small align-middle">
                    <tr><th width="40" class="text-center">1</th><th width="200">PNR</th><td class="text-primary fw-bold"><?= htmlspecialchars($advise['pnr']) ?></td></tr>
                    <tr><th class="text-center">2</th><th>GRP DEPART DATE</th><td><?= date('d-M-y', strtotime($advise['grp_depart_date'])) ?></td></tr>
                    <tr><th class="text-center">3</th><th>TOTAL SEATS CONFIRMED</th><td><?= $advise['total_seats_confirmed'] ?></td></tr>
                    <tr><th class="text-center"></th><th>TOTAL SEATS USED (%)</th><td><?= $advise['total_seats_used_percent'] ?>%</td></tr>
                    <tr><th class="text-center">4</th><th>APPROVED FARE</th><td>IDR <?= number_format($advise['approved_fare']) ?></td></tr>
                    <tr><th class="text-center">5</th><th>TOTAL AMOUNT</th><td>IDR <?= number_format($advise['total_amount']) ?></td></tr>
                    <tr><th class="text-center">6</th><th>DEPOSIT 1st & 2nd (20%)</th><td class="text-danger">(IDR <?= number_format($advise['deposit_amount']) ?>)</td></tr>
                    <tr><th class="text-center">7</th><th>BALANCE PAYMENT (80%)</th><td class="text-danger fw-bold">(IDR <?= number_format($advise['balance_payment_amount']) ?>)</td></tr>
                    <tr><th class="text-center">8</th><th>TOP UP AMOUNT</th><td class="text-danger fw-bold">(IDR <?= number_format($advise['top_up_amount']) ?>)</td></tr>
                    <tr><th class="text-center">9</th><th>TRANSFER AMOUNT</th><td class="text-danger fw-bold">(IDR <?= number_format($advise['transfer_amount']) ?>)</td></tr>
                    <tr><th class="text-center">10</th><th>REFERENCE NUMBER</th><td class="fw-bold"><?= htmlspecialchars($advise['reference_number'] ?: '-') ?></td></tr>
                </table>
                <p class="small italic text-primary mt-3">Bank Slip Transfer approval is attached</p>
            </div>
            <div class="col-5">
                <div class="mb-4 border-start ps-3 py-2 bg-light">
                    <h6 class="fw-bold text-uppercase small text-orange">Recipient Bank Details</h6>
                    <table class="table table-sm table-borderless text-xs mb-0">
                        <tr><th width="100">COMPANY</th><td>: <?= htmlspecialchars($advise['company_name']) ?></td></tr>
                        <tr><th>ACCOUNT NO</th><td>: <?= htmlspecialchars($advise['company_account_no']) ?></td></tr>
                        <tr><th>BANK NAME</th><td>: <?= htmlspecialchars($advise['company_bank_name']) ?></td></tr>
                        <tr><th>ADDRESS</th><td>: <?= htmlspecialchars($advise['company_address']) ?></td></tr>
                    </table>
                </div>
                
                <div class="border-start ps-3 py-2 bg-light">
                    <h6 class="fw-bold text-uppercase small text-success">Remitter Bank Details</h6>
                    <table class="table table-sm table-borderless text-xs mb-0">
                        <tr><th width="100">NAME</th><td>: <?= htmlspecialchars($advise['remitter_name']) ?></td></tr>
                        <tr><th>ACCOUNT</th><td>: <?= htmlspecialchars($advise['remitter_account_no']) ?></td></tr>
                        <tr><th>BANK NAME</th><td>: <?= htmlspecialchars($advise['remitter_bank_name']) ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-5 border-top pt-4">
            <div class="col-6">
                <table class="table table-sm table-borderless small">
                    <tr><th width="180">DATE OF TOP UP CREATED</th><td>: <?= $advise['date_top_up_created'] ? date('l, d F Y', strtotime($advise['date_top_up_created'])) : '-' ?></td></tr>
                    <tr><th>REMARKS ON TOP UP</th><td class="bg-warning-subtle"><?= htmlspecialchars($advise['remarks_top_up'] ?: '-') ?></td></tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless small">
                    <tr><th width="180">DATE BANK TRANSFERRED</th><td>: <?= $advise['date_bank_transferred'] ? date('l, d F Y', strtotime($advise['date_bank_transferred'])) : '-' ?></td></tr>
                    <tr><th>REMARKS ON BANK TRANSFER</th><td class="bg-warning-subtle"><?= htmlspecialchars($advise['remarks_bank_transfer'] ?: '-') ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-advise-doc th { background-color: #f8f9fa; }
    .text-orange { color: #fd7e14; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .text-xs { font-size: 0.75rem; }
    @media print {
        .navbar, .d-print-none { display: none !important; }
        .payment-advise-doc { border: none !important; box-shadow: none !important; padding: 0 !important; }
        body { background: white !important; }
    }
</style>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
