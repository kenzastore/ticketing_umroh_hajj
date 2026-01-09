<?php
$title = "Login"; // Set title for the header
require_once __DIR__ . '/shared/header.php';
?>
<!-- Custom Login Styling -->
<link rel="stylesheet" href="/assets/css/login.css">

<div class="login-wrapper">
    <!-- Left Side: Information Panel -->
    <div class="info-panel">
        <h1>Ticketing Umroh & Haji</h1>
        <p class="lead">Platform digital terintegrasi untuk pengelolaan siklus lengkap penagihan dan keberangkatan jamaah.</p>

        <div class="workflow-stepper">
            <div class="workflow-item">
                <div class="workflow-icon"><i class="fas fa-file-signature"></i></div>
                <div class="workflow-content">
                    <h6>1. Demand Intake</h6>
                    <p>Input permintaan kuota kursi dari Agen/Corporate.</p>
                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">Staff Operasional</span>
                </div>
            </div>
            
            <div class="workflow-item">
                <div class="workflow-icon"><i class="fas fa-plane-departure"></i></div>
                <div class="workflow-content">
                    <h6>2. Operational Execution</h6>
                    <p>Konversi menjadi Movement (PNR & Tour Code).</p>
                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">Staff Operasional / Admin</span>
                </div>
            </div>

            <div class="workflow-item">
                <div class="workflow-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="workflow-content">
                    <h6>3. Financial Settlement</h6>
                    <p>Pembuatan Invoice & Pencatatan Pembayaran.</p>
                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">Finance Officer</span>
                </div>
            </div>

            <div class="workflow-item">
                <div class="workflow-icon"><i class="fas fa-history"></i></div>
                <div class="workflow-content">
                    <h6>4. Management & Audit</h6>
                    <p>Monitoring deadline dan peninjauan log audit.</p>
                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">Administrator</span>
                </div>
            </div>
        </div>

        <div class="demo-creds shadow-sm">
            <h6 class="mb-3"><i class="fas fa-key me-2"></i>Demo Credentials</h6>
            <table>
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Administrator</td>
                        <td><code>admin_demo</code></td>
                        <td rowspan="4" class="align-middle"><code>password123</code></td>
                    </tr>
                    <tr>
                        <td>Operational</td>
                        <td><code>op_demo</code></td>
                    </tr>
                    <tr>
                        <td>Finance</td>
                        <td><code>finance_demo</code></td>
                    </tr>
                    <tr>
                        <td>Monitor</td>
                        <td><code>monitor_demo</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Side: Access Panel -->
    <div class="access-panel">
        <div class="login-card">
            <div class="card shadow-lg p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                        <h3 class="fw-bold">System Login</h3>
                        <p class="text-muted small">Silakan masuk ke akun Anda</p>
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <?php
                                if ($_GET['error'] === 'empty_fields') {
                                    echo '<i class="fas fa-exclamation-circle me-2"></i>Harap isi semua kolom.';
                                } elseif ($_GET['error'] === 'invalid_credentials') {
                                    echo '<i class="fas fa-times-circle me-2"></i>Username atau password salah.';
                                }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label small fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required placeholder="admin_demo">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Masuk ke Dashboard</button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted extra-small">&copy; 2026 PT Elang Emas Mandiri Indonesia</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hide default header for clean login experience */
    nav.navbar { display: none !important; }
    main.container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    .extra-small { font-size: 0.7rem; }
</style>

<?php
require_once __DIR__ . '/shared/footer.php';
?>