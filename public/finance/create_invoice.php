<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Invoice.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth(['finance', 'admin']);

$movement_id = $_GET['movement_id'] ?? null;

if (!$movement_id) {
    header('Location: dashboard.php?error=Movement ID is required.');
    exit;
}

$movement = Movement::readById($movement_id);

if (!$movement) {
    header('Location: dashboard.php?error=Movement not found.');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header = [
        'invoice_no' => $_POST['invoice_no'] ?: 'INV/' . date('Ymd') . '/' . $movement['pnr'],
        'invoice_date' => $_POST['invoice_date'] ?: date('Y-m-d'),
        'corporate_id' => $_POST['corporate_id'] ?: null,
        'corporate_name' => $_POST['corporate_name'] ?: null,
        'attention_to' => $_POST['attention_to'] ?: null,
        'address' => $_POST['address'] ?: null,
        'ref_text' => $_POST['ref_text'] ?: null,
        'pnr' => $movement['pnr'],
        'tour_code' => $movement['tour_code'],
        'total_pax' => $_POST['total_pax'] ?: 0,
        'fare_per_pax' => $_POST['fare_per_pax'] ?: 0,
        'amount_idr' => $_POST['amount_idr'] ?: 0
    ];

    $flightLines = [];
    if (!empty($_POST['flights'])) {
        foreach ($_POST['flights'] as $f) {
            if (!empty($f['flight_no'])) $flightLines[] = $f;
        }
    }

    $fareLines = [];
    if (!empty($_POST['fares'])) {
        foreach ($_POST['fares'] as $f) {
            if (!empty($f['description'])) $fareLines[] = $f;
        }
    }

    if (Invoice::create($header, $flightLines, $fareLines)) {
        header('Location: dashboard.php?success=Invoice Created');
        exit;
    } else {
        $error = "Failed to create invoice.";
    }
}

$title = "Create Invoice";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="mb-3">
    <a href="dashboard.php" class="btn btn-secondary btn-sm">&larr; Back to Finance</a>
</div>

<form method="POST">
    <div class="row">
        <!-- Header -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">Invoice Header</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Invoice No</label>
                            <input type="text" name="invoice_no" class="form-control form-control-sm" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Invoice Date</label>
                            <input type="date" name="invoice_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Corporate Name</label>
                        <input type="text" name="corporate_name" class="form-control form-control-sm" value="<?= htmlspecialchars($movement['agent_name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Attention To</label>
                        <input type="text" name="attention_to" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Address</label>
                        <textarea name="address" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Reference Text (e.g. REF: 11MAY-45S)</label>
                        <input type="text" name="ref_text" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">Pricing Summary</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small">Total Pax</label>
                        <input type="number" name="total_pax" id="total_pax" class="form-control" value="<?= $movement['passenger_count'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Fare Per Pax</label>
                        <input type="number" step="0.01" name="fare_per_pax" id="fare_per_pax" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-primary fw-bold">Total Amount IDR</label>
                        <input type="number" step="0.01" name="amount_idr" id="amount_idr" class="form-control form-control-lg border-primary">
                    </div>
                    <div class="alert alert-info py-2 small">
                        <strong>PNR:</strong> <?= $movement['pnr'] ?><br>
                        <strong>Tour Code:</strong> <?= $movement['tour_code'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flight Lines -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white py-1">Flight Details (Lines)</div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Date</th>
                        <th>Flight No</th>
                        <th>Sector</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Pre-fill from movement legs
                    for ($i = 0; $i < 4; $i++): 
                        $leg = $movement['legs'][$i] ?? null;
                    ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><input type="date" name="flights[<?= $i ?>][flight_date]" class="form-control form-control-sm border-0" value="<?= $leg['scheduled_departure'] ?? '' ?>"></td>
                            <td><input type="text" name="flights[<?= $i ?>][flight_no]" class="form-control form-control-sm border-0" value="<?= $leg['flight_no'] ?? '' ?>"></td>
                            <td><input type="text" name="flights[<?= $i ?>][sector]" class="form-control form-control-sm border-0" value="<?= $leg['sector'] ?? '' ?>"></td>
                            <td><input type="text" name="flights[<?= $i ?>][time_range]" class="form-control form-control-sm border-0" value="<?= $leg['time_range'] ?? '' ?>"></td>
                            <input type="hidden" name="flights[<?= $i ?>][line_no]" value="<?= $i+1 ?>">
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fare Lines -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white py-1">Fare Breakdown (Lines)</div>
        <div class="card-body p-0">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Description</th>
                        <th>Pax</th>
                        <th>Fare Amount</th>
                        <th>Total IDR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><input type="text" name="fares[<?= $i ?>][description]" class="form-control form-control-sm border-0"></td>
                            <td><input type="number" name="fares[<?= $i ?>][total_pax]" class="form-control form-control-sm border-0"></td>
                            <td><input type="number" step="0.01" name="fares[<?= $i ?>][fare_amount]" class="form-control form-control-sm border-0"></td>
                            <td><input type="number" step="0.01" name="fares[<?= $i ?>][amount_idr]" class="form-control form-control-sm border-0"></td>
                            <input type="hidden" name="fares[<?= $i ?>][line_no]" value="<?= $i+1 ?>">
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

        <div class="text-end mb-5">

            <button type="submit" class="btn btn-lg btn-success">Save & Generate Invoice</button>

        </div>

    </form>

    

    <script>

    document.addEventListener('DOMContentLoaded', function() {

        const totalPaxInput = document.getElementById('total_pax');

        const farePerPaxInput = document.getElementById('fare_per_pax');

        const amountIdrInput = document.getElementById('amount_idr');

    

        function calculateHeaderTotal() {

            const pax = parseFloat(totalPaxInput.value) || 0;

            const fare = parseFloat(farePerPaxInput.value) || 0;

            amountIdrInput.value = (pax * fare);

        }

    

        if (totalPaxInput && farePerPaxInput && amountIdrInput) {

            totalPaxInput.addEventListener('input', calculateHeaderTotal);

            farePerPaxInput.addEventListener('input', calculateHeaderTotal);

        }

    });

    </script>

    

    <?php require_once __DIR__ . '/../shared/footer.php'; ?>

    