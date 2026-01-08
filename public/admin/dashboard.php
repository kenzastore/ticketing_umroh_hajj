<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';

check_auth('admin');

$title = "Dashboard";

// Fetch deadlines by category
$ticketingDeadlines = Movement::getDeadlinesByCategory('ticketing', 3);
$dp1Deadlines        = Movement::getDeadlinesByCategory('dp1', 3);
$dp2Deadlines        = Movement::getDeadlinesByCategory('dp2', 3);
$fpDeadlines         = Movement::getDeadlinesByCategory('fp', 3);

require_once __DIR__ . '/../shared/header.php';

/**
 * Helper to render deadline list
 */
function renderDeadlineList($deadlines, $dateField) {
    if (empty($deadlines)) {
        return '<div class="p-4 text-center text-muted small">No urgent deadlines.</div>';
    }
    
    $today = date('Y-m-d');
    $html = '<div class="list-group list-group-flush small">';
    foreach ($deadlines as $d) {
        $deadlineDate = $d[$dateField] ?? $d['ticketing_deadline'] ?? null;
        
        // Handle categories that have multiple date fields (e.g. DP1/DP2/FP)
        if (!$deadlineDate && isset($d['deposit1_airlines_date'])) {
            $deadlineDate = min(array_filter([$d['deposit1_airlines_date'], $d['deposit1_eemw_date']], function($v) { return !is_null($v); }));
        } elseif (!$deadlineDate && isset($d['deposit2_airlines_date'])) {
            $deadlineDate = min(array_filter([$d['deposit2_airlines_date'], $d['deposit2_eemw_date']], function($v) { return !is_null($v); }));
        } elseif (!$deadlineDate && isset($d['fullpay_airlines_date'])) {
            $deadlineDate = min(array_filter([$d['fullpay_airlines_date'], $d['fullpay_eemw_date']], function($v) { return !is_null($v); }));
        }

        $isPastDue = $deadlineDate < $today;
        $bgClass = $isPastDue ? 'bg-danger-subtle fw-bold' : '';
        $badgeClass = $isPastDue ? 'bg-danger' : 'bg-warning text-dark';
        
        $html .= '<a href="edit_movement.php?id=' . $d['id'] . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ' . $bgClass . '">';
        $html .= '<span>';
        if ($isPastDue) {
            $html .= '<i class="fas fa-exclamation-triangle text-danger me-2"></i>';
        }
        $html .= '<strong>' . htmlspecialchars($d['pnr'] ?? 'No PNR') . '</strong> - ' . htmlspecialchars($d['agent_name'] ?? '-');
        $html .= '</span>';
        $html .= '<span class="badge ' . $badgeClass . ' rounded-pill">';
        $html .= date('d M', strtotime($deadlineDate));
        $html .= '</span>';
        $html .= '</a>';
    }
    $html .= '</div>';
    return $html;
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    </div>
</div>

<!-- Urgent Deadlines Widget -->
<div class="row mb-5" id="time-limit-section">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-header bg-danger text-white py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-clock me-2"></i>Urgent Deadlines (H-3 & Past Due)</h6>
            </div>
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs card-header-tabs nav-fill m-0" id="deadlineTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-2 border-top-0 w-100" id="ticketing-tab" data-bs-toggle="tab" data-bs-target="#ticketing" type="button" role="tab" aria-controls="ticketing" aria-selected="true">
                            Ticketing <span class="badge bg-danger rounded-pill ms-1"><?= count($ticketingDeadlines) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 border-top-0 w-100" id="dp1-tab" data-bs-toggle="tab" data-bs-target="#dp1" type="button" role="tab" aria-controls="dp1" aria-selected="false">
                            DP1 <span class="badge bg-secondary rounded-pill ms-1"><?= count($dp1Deadlines) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 border-top-0 w-100" id="dp2-tab" data-bs-toggle="tab" data-bs-target="#dp2" type="button" role="tab" aria-controls="dp2" aria-selected="false">
                            DP2 <span class="badge bg-secondary rounded-pill ms-1"><?= count($dp2Deadlines) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 border-top-0 w-100" id="fp-tab" data-bs-toggle="tab" data-bs-target="#fp" type="button" role="tab" aria-controls="fp" aria-selected="false">
                            FP <span class="badge bg-secondary rounded-pill ms-1"><?= count($fpDeadlines) ?></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="deadlineTabsContent">
                    <div class="tab-pane fade show active" id="ticketing" role="tabpanel" aria-labelledby="ticketing-tab">
                        <?= renderDeadlineList($ticketingDeadlines, 'ticketing_deadline') ?>
                    </div>
                    <div class="tab-pane fade" id="dp1" role="tabpanel" aria-labelledby="dp1-tab">
                        <?= renderDeadlineList($dp1Deadlines, 'deposit1_airlines_date') ?>
                    </div>
                    <div class="tab-pane fade" id="dp2" role="tabpanel" aria-labelledby="dp2-tab">
                        <?= renderDeadlineList($dp2Deadlines, 'deposit2_airlines_date') ?>
                    </div>
                    <div class="tab-pane fade" id="fp" role="tabpanel" aria-labelledby="fp-tab">
                        <?= renderDeadlineList($fpDeadlines, 'fullpay_airlines_date') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Grid -->
<div class="row g-4">
    <!-- 1. Booking Request -->
    <div class="col-md-4 col-sm-6">
        <a href="booking_requests.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-primary border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-file-contract fa-3x text-primary"></i>
                    </div>
                    <h3 class="card-title text-primary">1. Booking Request</h3>
                    <p class="card-text text-muted">Manage new and existing requests</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 2. Movement -->
    <div class="col-md-4 col-sm-6">
        <a href="movement_fullview.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-success border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-plane fa-3x text-success"></i>
                    </div>
                    <h3 class="card-title text-success">2. Movement</h3>
                    <p class="card-text text-muted">Monitor flight movements</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 3. Invoice -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/create_invoice.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-warning border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice fa-3x text-warning"></i>
                    </div>
                    <h3 class="card-title text-warning">3. Invoice</h3>
                    <p class="card-text text-muted">Generate and manage invoices</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 4. Payment Report -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/dashboard.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-info border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-info"></i>
                    </div>
                    <h3 class="card-title text-info">4. Payment Report</h3>
                    <p class="card-text text-muted">View financial reports</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 5. Payment Advise -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/payment_advise_list.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-dark border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-money-check-alt fa-3x text-dark"></i>
                    </div>
                    <h3 class="card-title text-dark">5. Payment Advise</h3>
                    <p class="card-text text-muted">Manage payment advises</p>
                </div>
            </div>
        </a>
    </div>

    <!-- 6. Rangkuman -->
    <div class="col-md-4 col-sm-6">
        <a href="agent_summary.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-secondary border-top border-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-list fa-3x text-secondary"></i>
                    </div>
                    <h3 class="card-title text-secondary">6. Summary</h3>
                    <p class="card-text text-muted">Summary & Statistics</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.05);
    }
    .nav-tabs .nav-link {
        font-size: 0.85rem;
        color: #6c757d;
        border-radius: 0;
        border-bottom: 1px solid #dee2e6;
        background: none;
        border-left: none;
        border-right: none;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        color: #dc3545;
        border-bottom: 2px solid #dc3545 !important;
    }
    .bg-danger-subtle {
        background-color: #f8d7da;
    }
    .btn-xs {
        padding: 0.1rem 0.3rem;
        font-size: 0.7rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Manually initialize tabs if Bootstrap auto-init fails
    var triggerTabList = [].slice.call(document.querySelectorAll('#deadlineTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
