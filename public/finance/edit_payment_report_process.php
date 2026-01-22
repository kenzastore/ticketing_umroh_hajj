<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Movement.php';
require_once __DIR__ . '/../../app/models/PaymentReport.php';

check_auth(['finance', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movementId = $_POST['movement_id'];
    $userId = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // 1. Update Movement Summary Fields
        Movement::update($movementId, [
            'incentive_amount' => $_POST['incentive_amount'],
            'discount_amount' => $_POST['discount_amount']
        ], $userId);

        // 2. Handle Deleted Lines
        if (!empty($_POST['deleted_lines'])) {
            foreach ($_POST['deleted_lines'] as $lineId) {
                PaymentReport::deleteLine($lineId, $userId);
            }
        }

        // 3. Handle Create/Update Lines
        if (!empty($_POST['lines'])) {
            foreach ($_POST['lines'] as $lineData) {
                $lineId = $lineData['id'] ?? null;
                unset($lineData['id']); // Remove ID from data to be saved/updated
                
                if ($lineId) {
                    PaymentReport::updateLine($lineId, $lineData, $userId);
                } else {
                    PaymentReport::createLine($lineData, $userId);
                }
            }
        }

        $pdo->commit();
        header("Location: payment_report.php?movement_id=$movementId&success=updated");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error saving changes: " . $e->getMessage());
    }
}
