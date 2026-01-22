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

$title = "Edit Payment Report - " . ($report['pnr'] ?? 'N/A');
require_once __DIR__ . '/../shared/header.php';

// Prepare deadlines for JS
$deadlines = [
    'dp1' => $report['deposit1_airlines_date'] ?? $report['deposit1_eemw_date'],
    'dp2' => $report['deposit2_airlines_date'] ?? $report['deposit2_eemw_date'],
    'fp'  => $report['fullpay_airlines_date'] ?? $report['fullpay_eemw_date']
];
?>

<script>
    const DEADLINES = <?= json_encode($deadlines) ?>;
</script>

<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Payment Report</h1>
        <a href="payment_report.php?movement_id=<?= $movementId ?>" class="btn btn-secondary btn-sm">&larr; Back to Report View</a>
    </div>

    <form action="edit_payment_report_process.php" method="POST">
        <input type="hidden" name="movement_id" value="<?= $movementId ?>">
        <input type="hidden" name="pnr" value="<?= $report['pnr'] ?>">

        <!-- Summary Fields -->
        <div class="card mb-4 shadow-sm border-primary">
            <div class="card-header bg-primary text-white fw-bold">Summary & Adjustments</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Incentive (Override Profit)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="incentive_amount" class="form-control" value="<?= (int)$report['incentive_amount'] ?>">
                        </div>
                        <small class="text-muted">If 0, it will be auto-calculated as (Sales Balance - Cost Balance).</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Discount</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="discount_amount" class="form-control" value="<?= (int)$report['discount_amount'] ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Lines Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>Payment Report Lines (SALES & COST)</span>
                <button type="button" class="btn btn-success btn-sm" onclick="addLine()">+ Add New Line</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle text-center mb-0" style="font-size: 0.8rem;" id="lines-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Type</th>
                                <th>Payment Date</th>
                                <th>Ref ID</th>
                                <th>TTL Pax</th>
                                <th>Remarks</th>
                                <th>Fare/Pax</th>
                                <th>Debet</th>
                                <th>Bank From (Name/No)</th>
                                <th>Bank To (Name/No)</th>
                                <th>Time Limit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $allLines = array_merge($report['sales_lines'], $report['cost_lines']);
                            foreach ($allLines as $index => $line): ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="lines[<?= $index ?>][id]" value="<?= $line['id'] ?>">
                                    <select name="lines[<?= $index ?>][table_type]" class="form-select form-select-sm">
                                        <option value="SALES" <?= $line['table_type'] == 'SALES' ? 'selected' : '' ?>>SALES</option>
                                        <option value="COST" <?= $line['table_type'] == 'COST' ? 'selected' : '' ?>>COST</option>
                                    </select>
                                </td>
                                <td><input type="date" name="lines[<?= $index ?>][payment_date]" class="form-control form-control-sm" value="<?= $line['payment_date'] ?>"></td>
                                <td><input type="text" name="lines[<?= $index ?>][reference_id]" class="form-control form-control-sm" value="<?= htmlspecialchars($line['reference_id']) ?>"></td>
                                <td><input type="number" name="lines[<?= $index ?>][total_pax]" class="form-control form-control-sm" value="<?= $line['total_pax'] ?>"></td>
                                <td><input type="text" name="lines[<?= $index ?>][remarks]" class="form-control form-control-sm" value="<?= htmlspecialchars($line['remarks']) ?>" oninput="syncDeadline(this)"></td>
                                <td><input type="number" name="lines[<?= $index ?>][fare_per_pax]" class="form-control form-control-sm" value="<?= (int)$line['fare_per_pax'] ?>"></td>
                                <td><input type="number" name="lines[<?= $index ?>][debit_amount]" class="form-control form-control-sm" value="<?= (int)$line['debit_amount'] ?>"></td>
                                <td>
                                    <input type="text" name="lines[<?= $index ?>][bank_from]" class="form-control form-control-sm mb-1" value="<?= htmlspecialchars($line['bank_from']) ?>" placeholder="Bank">
                                    <input type="text" name="lines[<?= $index ?>][bank_from_name]" class="form-control form-control-sm mb-1" value="<?= htmlspecialchars($line['bank_from_name']) ?>" placeholder="Name">
                                    <input type="text" name="lines[<?= $index ?>][bank_from_number]" class="form-control form-control-sm" value="<?= htmlspecialchars($line['bank_from_number']) ?>" placeholder="No">
                                </td>
                                <td>
                                    <input type="text" name="lines[<?= $index ?>][bank_to]" class="form-control form-control-sm mb-1" value="<?= htmlspecialchars($line['bank_to']) ?>" placeholder="Bank">
                                    <input type="text" name="lines[<?= $index ?>][bank_to_name]" class="form-control form-control-sm mb-1" value="<?= htmlspecialchars($line['bank_to_name']) ?>" placeholder="Name">
                                    <input type="text" name="lines[<?= $index ?>][bank_to_number]" class="form-control form-control-sm" value="<?= htmlspecialchars($line['bank_to_number']) ?>" placeholder="No">
                                </td>
                                <td><input type="date" name="lines[<?= $index ?>][time_limit_date]" class="form-control form-control-sm" value="<?= $line['time_limit_date'] ?>"></td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeLine(this, <?= $line['id'] ?>)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">Save Changes</button>
        </div>
    </form>
</div>

<script>
    let lineIndex = <?= count($allLines) ?>;

    function addLine() {
        const tbody = document.querySelector('#lines-table tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="lines[${lineIndex}][table_type]" class="form-select form-select-sm">
                    <option value="SALES">SALES</option>
                    <option value="COST">COST</option>
                </select>
            </td>
            <td><input type="date" name="lines[${lineIndex}][payment_date]" class="form-control form-control-sm"></td>
            <td><input type="text" name="lines[${lineIndex}][reference_id]" class="form-control form-control-sm" value="<?= $report['pnr'] ?>"></td>
            <td><input type="number" name="lines[${lineIndex}][total_pax]" class="form-control form-control-sm" value="<?= $report['passenger_count'] ?>"></td>
            <td><input type="text" name="lines[${lineIndex}][remarks]" class="form-control form-control-sm" oninput="syncDeadline(this)"></td>
            <td><input type="number" name="lines[${lineIndex}][fare_per_pax]" class="form-control form-control-sm"></td>
            <td><input type="number" name="lines[${lineIndex}][debit_amount]" class="form-control form-control-sm"></td>
            <td>
                <input type="text" name="lines[${lineIndex}][bank_from]" class="form-control form-control-sm mb-1" placeholder="Bank">
                <input type="text" name="lines[${lineIndex}][bank_from_name]" class="form-control form-control-sm mb-1" placeholder="Name">
                <input type="text" name="lines[${lineIndex}][bank_from_number]" class="form-control form-control-sm" placeholder="No">
            </td>
            <td>
                <input type="text" name="lines[${lineIndex}][bank_to]" class="form-control form-control-sm mb-1" placeholder="Bank">
                <input type="text" name="lines[${lineIndex}][bank_to_name]" class="form-control form-control-sm mb-1" placeholder="Name">
                <input type="text" name="lines[${lineIndex}][bank_to_number]" class="form-control form-control-sm" placeholder="No">
            </td>
            <td><input type="date" name="lines[${lineIndex}][time_limit_date]" class="form-control form-control-sm"></td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeLine(this)"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        lineIndex++;
    }

    function removeLine(btn, id = null) {
        if (confirm('Are you sure you want to remove this line?')) {
            if (id) {
                // If it's an existing line, we might want to handle it differently, 
                // but for simplicity here we just don't send its data anymore.
                // In a real app, you might need a hidden 'deleted_lines[]' input.
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_lines[]';
                input.value = id;
                btn.closest('form').appendChild(input);
            }
            btn.closest('tr').remove();
        }
    }

    function syncDeadline(input) {
        const row = input.closest('tr');
        const remark = input.value.toUpperCase();
        const deadlineInput = row.querySelector('input[name*="[time_limit_date]"]');
        
        if (!deadlineInput) return;

        if (remark.includes('DEPOSIT 1') || remark.includes('DEPOSIT-1')) {
            if (DEADLINES.dp1) deadlineInput.value = DEADLINES.dp1;
        } else if (remark.includes('DEPOSIT 2') || remark.includes('DEPOSIT-2')) {
            if (DEADLINES.dp2) deadlineInput.value = DEADLINES.dp2;
        } else if (remark.includes('FULLPAY') || remark.includes('FP')) {
            if (DEADLINES.fp) deadlineInput.value = DEADLINES.fp;
        }
    }
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
