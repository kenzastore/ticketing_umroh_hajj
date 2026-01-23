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

<style>
    .dashboard-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .card-icon-container {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .card-description {
        font-size: 0.85rem;
        line-height: 1.4;
        color: #6c757d;
        height: 3rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
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
</style>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Dashboard Hub</h1>
        <p class="text-muted">Selamat datang di pusat kendali operasional EEMW Ticketing.</p>
    </div>
</div>

<!-- Urgent Deadlines Widget -->
<div class="row mb-5" id="time-limit-section">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
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
<div class="row g-4 mb-5">
    <!-- 1. Booking Request -->
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm dashboard-card border-start border-primary border-4">
            <div class="card-body p-4">
                <div class="card-icon-container bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-file-contract fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Booking Request</h5>
                <p class="card-description">Kelola permintaan pemesanan baru dan jamaah masuk. Input data awal secara cepat dan akurat.</p>
                <div class="text-end">
                    <a href="booking_requests.php" class="btn btn-primary btn-sm px-3 shadow-sm" aria-label="Buka Manajemen Booking Request">
                        Buka Request <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Movement -->
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm dashboard-card border-start border-success border-4">
            <div class="card-body p-4">
                <div class="card-icon-container bg-success bg-opacity-10 text-success">
                    <i class="fas fa-plane-departure fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Movement Monitoring</h5>
                <p class="card-description">Pantau status pergerakan grup, jadwal penerbangan, dan deadline ticketing secara real-time.</p>
                <div class="text-end">
                    <a href="movement_fullview.php" class="btn btn-success btn-sm px-3 shadow-sm" aria-label="Buka Monitoring Movement">
                        Buka Movement <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Invoice -->
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm dashboard-card border-start border-warning border-4">
            <div class="card-body p-4">
                <div class="card-icon-container bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Invoice Generator</h5>
                <p class="card-description">Terbitkan invoice proforma untuk agen. Pantau status penagihan dan histori pembayaran.</p>
                <div class="text-end">
                    <a href="../finance/dashboard.php" class="btn btn-warning btn-sm px-3 shadow-sm text-dark fw-bold" aria-label="Buka Manajemen Invoice">
                        Buka Invoice <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Payment Report -->
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm dashboard-card border-start border-info border-4">
            <div class="card-body p-4">
                <div class="card-icon-container bg-info bg-opacity-10 text-info">
                    <i class="fas fa-chart-pie fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Payment Report</h5>
                <p class="card-description">Laporan keuangan komprehensif yang membandingkan Sales (Internal) dan Cost (Airline).</p>
                <div class="text-end">
                    <a href="../finance/dashboard.php" class="btn btn-info btn-sm px-3 shadow-sm text-white" aria-label="Buka Laporan Pembayaran">
                        Buka Laporan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Payment Advise -->
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm dashboard-card border-start border-dark border-4">
            <div class="card-body p-4">
                <div class="card-icon-container bg-dark bg-opacity-10 text-dark">
                    <i class="fas fa-money-check-alt fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Payment Advise</h5>
                <p class="card-description">Proses konfirmasi pembayaran ke maskapai. Kelola top-up deposit dan saldo airline.</p>
                <div class="text-end">
                    <a href="../finance/payment_advise_list.php" class="btn btn-dark btn-sm px-3 shadow-sm" aria-label="Buka Payment Advise">
                        Buka Advise <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Rangkuman -->
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 shadow-sm dashboard-card border-start border-secondary border-4">
            <div class="card-body p-4">
                <div class="card-icon-container bg-secondary bg-opacity-10 text-secondary">
                    <i class="fas fa-layer-group fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Rangkuman / Summary</h5>
                <p class="card-description">Ringkasan performa agen dan statistik grup. Data konsolidasi untuk pengambilan keputusan.</p>
                <div class="text-end">
                    <a href="agent_summary.php" class="btn btn-secondary btn-sm px-3 shadow-sm" aria-label="Buka Rangkuman Data">
                        Buka Summary <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

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