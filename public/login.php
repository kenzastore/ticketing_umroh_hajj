<?php
$title = "Login"; // Set title for the header
require_once __DIR__ . '/shared/header.php';
?>
<div class="container mt-5">
    <div class="row align-items-center">
        <!-- Left Column: Business Process Flow -->
        <div class="col-md-7 mb-5 mb-md-0">
            <h1 class="display-4 mb-3 text-primary">Ticketing Umroh & Haji</h1>
            <p class="lead mb-4">A centralized platform for managing the complete lifecycle of pilgrimage ticketing.</p>
            
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title border-bottom pb-2 mb-3">Workflow Overview</h5>
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-pill p-2 fs-6">1</span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Request Entry</h6>
                            <p class="small text-muted mb-0">Agents submit Group or FID requests detailing pax count and travel dates.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-info rounded-pill p-2 fs-6">2</span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Booking & PNR</h6>
                            <p class="small text-muted mb-0">Admins secure seats and enter PNR codes. Status moves to <span class="badge bg-success" style="font-size: 0.7em;">PNR_ISSUED</span>.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-warning text-dark rounded-pill p-2 fs-6">3</span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Invoicing</h6>
                            <p class="small text-muted mb-0">Finance generates invoices for confirmed bookings. PDF invoices are sent to agents.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-success rounded-pill p-2 fs-6">4</span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Payment & Verification</h6>
                            <p class="small text-muted mb-0">Payments are recorded and hashed for security. Booking becomes <span class="badge bg-success" style="font-size: 0.7em;">PAID_FULL</span>.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="me-3">
                            <span class="badge bg-secondary rounded-pill p-2 fs-6">5</span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Flight Monitoring</h6>
                            <p class="small text-muted mb-0">Real-time tracking of departure, arrival, and delays for all active movements.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">System Login</h3>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php
                                if ($_GET['error'] === 'empty_fields') {
                                    echo 'Please fill in all fields.';
                                } elseif ($_GET['error'] === 'invalid_credentials') {
                                    echo 'Invalid username or password.';
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username" required placeholder="e.g. admin">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                    </form>
                    
                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted d-block mb-2">Demo Credentials:</small>
                        <span class="badge bg-light text-dark border me-1">admin / admin</span>
                        <span class="badge bg-light text-dark border me-1">finance / password123</span>
                        <span class="badge bg-light text-dark border">monitor / password123</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/shared/footer.php';
?>
