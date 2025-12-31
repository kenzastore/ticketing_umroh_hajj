<?php
$title = "Login"; // Set title for the header
require_once __DIR__ . '/shared/header.php';
?>
    <div class="container login-container">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Login</h3>
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
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
<?php
require_once __DIR__ . '/shared/footer.php';
?>
