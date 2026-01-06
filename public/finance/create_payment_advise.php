<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';
require_once __DIR__ . '/../../app/models/PaymentAdvise.php';

check_auth(['finance', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    unset($data['submit']);
    
    $id = PaymentAdvise::create($data, $_SESSION['user_id']);
    if ($id) {
        header("Location: payment_advise_detail.php?id=$id&success=1");
        exit;
    } else {
        $error = "Failed to create payment advise.";
    }
}

// Fetch movements for selection
$movements = Movement::readAll();

$title = "Create Payment Advise";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>New Payment Advise</h1>
        <a href="payment_advise_list.php" class="btn btn-outline-secondary btn-sm">Cancel</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card shadow-sm p-4">
        <div class="row g-3">
            <h5 class="border-bottom pb-2">Movement Selection & Header</h5>
            <div class="col-md-4">
                <label class="form-label">Select Movement (PNR)</label>
                <select name="movement_id" id="movement_id" class="form-select" onchange="fillMovementData()">
                    <option value="">-- Choose Movement --</option>
                    <?php foreach($movements as $m): ?>
                        <option value="<?= $m['id'] ?>" 
                                data-agent="<?= htmlspecialchars($m['agent_name']) ?>" 
                                data-tour="<?= htmlspecialchars($m['tour_code']) ?>"
                                data-pnr="<?= htmlspecialchars($m['pnr']) ?>"
                                data-pax="<?= $m['passenger_count'] ?>"
                                data-fare="<?= $m['selling_fare'] ?>"
                                data-date="<?= $m['dep_seg1_date'] ?>">
                            <?= htmlspecialchars($m['pnr']) ?> - <?= htmlspecialchars($m['agent_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Agent Name</label>
                <input type="text" name="agent_name" id="agent_name" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tour Code</label>
                <input type="text" name="tour_code" id="tour_code" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">PNR</label>
                <input type="text" name="pnr" id="pnr" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date Created</label>
                <input type="date" name="date_created" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date E-mail to Airline</label>
                <input type="date" name="date_email_to_airline" class="form-control">
            </div>

            <h5 class="border-bottom pb-2 mt-4">Seat & Fare Information</h5>
            <div class="col-md-3">
                <label class="form-label">Group Depart Date</label>
                <input type="date" name="grp_depart_date" id="grp_depart_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Total Seats Confirmed</label>
                <input type="number" name="total_seats_confirmed" id="total_seats_confirmed" class="form-control" oninput="calculateTotals()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Approved Fare (IDR)</label>
                <input type="number" name="approved_fare" id="approved_fare" class="form-control" oninput="calculateTotals()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Total Amount</label>
                <input type="number" name="total_amount" id="total_amount" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Deposit (20%)</label>
                <input type="number" name="deposit_amount" id="deposit_amount" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Balance Payment (80%)</label>
                <input type="number" name="balance_payment_amount" id="balance_payment_amount" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Top Up Amount</label>
                <input type="number" name="top_up_amount" id="top_up_amount" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Transfer Amount</label>
                <input type="number" name="transfer_amount" id="transfer_amount" class="form-control">
            </div>

            <h5 class="border-bottom pb-2 mt-4">Recipient Bank (Airline)</h5>
            <div class="col-md-6">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="SCOOT PTE. LTD.">
            </div>
            <div class="col-md-6">
                <label class="form-label">Account No</label>
                <input type="text" name="company_account_no" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Bank Name</label>
                <input type="text" name="company_bank_name" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Address</label>
                <input type="text" name="company_address" class="form-control">
            </div>

            <h5 class="border-bottom pb-2 mt-4">Remitter Bank (EEMW)</h5>
            <div class="col-md-4">
                <label class="form-label">Remitter Name</label>
                <input type="text" name="remitter_name" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Account No</label>
                <input type="text" name="remitter_account_no" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bank Name</label>
                <input type="text" name="remitter_bank_name" class="form-control">
            </div>

            <div class="col-12 mt-4">
                <button type="submit" name="submit" class="btn btn-primary btn-lg w-100">Generate Payment Advise</button>
            </div>
        </div>
    </form>
</div>

<script>
function fillMovementData() {
    const select = document.getElementById('movement_id');
    const opt = select.options[select.selectedIndex];
    if (!opt.value) return;

    document.getElementById('agent_name').value = opt.dataset.agent;
    document.getElementById('tour_code').value = opt.dataset.tour;
    document.getElementById('pnr').value = opt.dataset.pnr;
    document.getElementById('total_seats_confirmed').value = opt.dataset.pax;
    document.getElementById('approved_fare').value = opt.dataset.fare;
    document.getElementById('grp_depart_date').value = opt.dataset.date;
    
    calculateTotals();
}

function calculateTotals() {
    const seats = parseFloat(document.getElementById('total_seats_confirmed').value) || 0;
    const fare = parseFloat(document.getElementById('approved_fare').value) || 0;
    
    const total = seats * fare;
    const deposit = total * 0.2;
    const balance = total * 0.8;
    
    document.getElementById('total_amount').value = total;
    document.getElementById('deposit_amount').value = deposit;
    document.getElementById('balance_payment_amount').value = balance;
    document.getElementById('top_up_amount').value = balance;
    document.getElementById('transfer_amount').value = balance;
}
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
