<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/PaymentAdvise.php';

check_auth(['finance', 'admin']);

$advises = PaymentAdvise::readAll();

$title = "Payment Advises";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Payment Advises</h1>
    <a href="create_payment_advise.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create New Advise</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date Created</th>
                        <th>Agent</th>
                        <th>PNR</th>
                        <th>Tour Code</th>
                        <th>Top Up Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($advises)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No payment advises found.</td></tr>
                    <?php else: ?>
                        <?php foreach($advises as $a): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($a['date_created'])) ?></td>
                                <td><?= htmlspecialchars($a['agent_name']) ?></td>
                                <td><strong class="text-primary"><?= htmlspecialchars($a['pnr']) ?></strong></td>
                                <td><code><?= htmlspecialchars($a['tour_code']) ?></code></td>
                                <td class="fw-bold">IDR <?= number_format($a['top_up_amount']) ?></td>
                                <td>
                                    <?php if($a['date_bank_transferred']): ?>
                                        <span class="badge bg-success">TRANSFERRED</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">PENDING</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="payment_advise_detail.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
