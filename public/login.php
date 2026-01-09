<?php
$title = "Login"; // Set title for the header
require_once __DIR__ . '/shared/header.php';
?>
<!-- Custom Minimal Login Styling -->
<link rel="stylesheet" href="/assets/css/minimal-login.css">

<div class="login-container">
    <div class="minimal-card card shadow-lg">
        <div class="card-body">
            <div class="text-center mb-5">
                <div class="app-logo">
                    <i class="fas fa-plane-departure"></i>
                </div>
                <h3 class="fw-bold">Ticketing Umroh</h3>
                <p class="text-muted small">Silakan masuk untuk mengelola ticketing</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger py-3 small border-0 shadow-sm mb-4" role="alert">
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
                <!-- Username -->
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="admin_demo">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                        <span class="input-group-text password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">Masuk ke Dashboard</button>
            </form>

            <!-- Workflow Accordion -->
            <div class="accordion workflow-accordion mt-5" id="workflowAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="fas fa-info-circle me-2"></i> How the system works
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#workflowAccordion">
                        <div class="accordion-body">
                            <div class="small">
                                <div class="d-flex mb-3">
                                    <div class="text-primary me-3"><i class="fas fa-file-signature fa-fw"></i></div>
                                    <div>
                                        <strong>1. Demand Intake</strong><br>
                                        <span class="text-muted">Input requests from Agents (Role: Operational).</span>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="text-primary me-3"><i class="fas fa-tasks fa-fw"></i></div>
                                    <div>
                                        <strong>2. Operational Execution</strong><br>
                                        <span class="text-muted">Manage PNR & Movements (Role: Operational/Admin).</span>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="text-primary me-3"><i class="fas fa-file-invoice-dollar fa-fw"></i></div>
                                    <div>
                                        <strong>3. Financial Settlement</strong><br>
                                        <span class="text-muted">Invoices & Payments (Role: Finance).</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text-primary me-3"><i class="fas fa-check-double fa-fw"></i></div>
                                    <div>
                                        <strong>4. Management & Audit</strong><br>
                                        <span class="text-muted">Deadlines & Audit Logs (Role: Admin).</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Credentials Footer -->
<footer class="demo-footer">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="demo-title text-center">Demo Accounts (Password: <code>password123</code>)</span>
                <div class="demo-grid text-center">
                    <div class="demo-item">
                        <code class="d-block">admin_demo</code>
                        <span class="demo-role">Administrator</span>
                    </div>
                    <div class="demo-item">
                        <code class="d-block">op_demo</code>
                        <span class="demo-role">Operational</span>
                    </div>
                    <div class="demo-item">
                        <code class="d-block">finance_demo</code>
                        <span class="demo-role">Finance</span>
                    </div>
                    <div class="demo-item">
                        <code class="d-block">monitor_demo</code>
                        <span class="demo-role">Monitor</span>
                    </div>
                </div>
                <p class="text-muted extra-small text-center mt-4 mb-0">&copy; 2026 PT Elang Emas Mandiri Indonesia</p>
            </div>
        </div>
    </div>
</footer>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<style>
    /* Clean overrides for the login experience */
    nav.navbar { display: none !important; }
    main.container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    .extra-small { font-size: 0.7rem; }
</style>

<?php
require_once __DIR__ . '/shared/footer.php';
?>