<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Invoice.php';

check_auth(['finance', 'admin']);

$title = "Finance Dashboard";
require_once __DIR__ . '/../shared/header.php';

// Fetch movements ready for invoicing (where ticketing is done but no invoice created for that PNR/TourCode)
$stmt_ready = $pdo->prepare("
    SELECT m.id as movement_id, m.pnr, m.tour_code, m.agent_name, m.passenger_count, m.created_date
    FROM movements m
    WHERE m.ticketing_done = 1 
    AND (m.pnr IS NOT NULL AND m.pnr != '')
    AND m.pnr NOT IN (SELECT pnr FROM invoices WHERE pnr IS NOT NULL)
    ORDER BY m.created_date DESC
");
$stmt_ready->execute();
$ready_movements = $stmt_ready->fetchAll(PDO::FETCH_ASSOC);

// Fetch all invoices
$invoices = Invoice::readAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Finance Dashboard</h1>
    <a href="../admin/dashboard.php" class="btn btn-secondary btn-sm">&larr; Admin Dashboard</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<!-- Movements Ready for Invoicing -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white py-2">
        <h5 class="mb-0">Movements Ready for Invoicing</h5>
    </div>
    <div class="card-body">
        <?php if (empty($ready_movements)): ?>
            <p class="text-muted">No movements currently ready for invoicing.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>PNR</th>
                            <th>Tour Code</th>
                            <th>Agent</th>
                            <th>Pax</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ready_movements as $m): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($m['pnr']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($m['tour_code']); ?></code></td>
                                <td><?php echo htmlspecialchars($m['agent_name']); ?></td>
                                <td><?php echo $m['passenger_count']; ?></td>
                                <td>
                                    <a href="create_invoice.php?movement_id=<?php echo $m['movement_id']; ?>" class="btn btn-sm btn-success">Create Invoice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- All Invoices -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white py-2">
        <h5 class="mb-0">Invoice History</h5>
    </div>
    <div class="card-body">
        <?php if (empty($invoices)): ?>
            <p class="text-muted">No invoices found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Inv No.</th>
                            <th>Date</th>
                            <th>PNR / Tour Code</th>
                            <th>Corporate / Agent</th>
                            <th>Amount (IDR)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inv['invoice_no']); ?></td>
                                <td><?php echo $inv['invoice_date']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($inv['pnr']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($inv['tour_code']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($inv['corporate_name'] ?: 'N/A'); ?><br>
                                    <small><?php echo htmlspecialchars($inv['attention_to'] ?: ''); ?></small>
                                </td>
                                <td class="text-end">Rp <?php echo number_format($inv['amount_idr'], 2); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="invoice_detail.php?id=<?php echo $inv['id']; ?>" class="btn btn-info text-white">View Inv</a>
                                        <?php if ($inv['pnr']): ?>
                                            <!-- We need movement_id to link to payment_report.php -->
                                            <?php 
                                                $stmt_m = $pdo->prepare("SELECT id FROM movements WHERE pnr = ? LIMIT 1");
                                                $stmt_m->execute([$inv['pnr']]);
                                                $m_id = $stmt_m->fetchColumn();
                                                if ($m_id):
                                            ?>
                                                <a href="payment_report.php?movement_id=<?php echo $m_id; ?>" class="btn btn-primary">Report</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
