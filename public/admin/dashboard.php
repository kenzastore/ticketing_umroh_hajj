<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';

check_auth('admin');

$title = "Dashboard";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    </div>
</div>

<!-- Time Limit Widget -->
<div class="row mb-5" id="time-limit-section">
    <div class="col-12">
        <div class="card border-danger shadow h-100 py-2 border-start border-5">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Time Limit (Reminders)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                             <span class="text-muted small">No urgent deadlines (Placeholder)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Grid -->
<div class="row g-4">
    <!-- Booking Request -->
    <div class="col-md-4 col-sm-6">
        <a href="booking_requests.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-primary border-top border-4">
                <div class="card-body">
                    <h3 class="card-title text-primary">Booking Request</h3>
                    <p class="card-text text-muted">Manage new and existing requests</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Movement -->
    <div class="col-md-4 col-sm-6">
        <a href="movement_fullview.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-success border-top border-4">
                <div class="card-body">
                    <h3 class="card-title text-success">Movement</h3>
                    <p class="card-text text-muted">Monitor flight movements</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Payment Report -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/dashboard.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-info border-top border-4">
                <div class="card-body">
                    <h3 class="card-title text-info">Payment Report</h3>
                    <p class="card-text text-muted">View financial reports</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Invoice -->
    <div class="col-md-4 col-sm-6">
        <a href="../finance/create_invoice.php" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-warning border-top border-4">
                <div class="card-body">
                    <h3 class="card-title text-warning">Invoice</h3>
                    <p class="card-text text-muted">Generate and manage invoices</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Rangkuman -->
    <div class="col-md-4 col-sm-6">
        <a href="#" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-secondary border-top border-4">
                <div class="card-body">
                    <h3 class="card-title text-secondary">Rangkuman</h3>
                    <p class="card-text text-muted">Summary & Statistics</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Payment Advise -->
    <div class="col-md-4 col-sm-6">
        <a href="#" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4 hover-scale border-dark border-top border-4">
                <div class="card-body">
                    <h3 class="card-title text-dark">Payment Advise</h3>
                    <p class="card-text text-muted">Manage payment advises</p>
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
</style>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>