<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Invoice.php';

check_auth(['admin', 'finance']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        header('Location: dashboard.php');
        exit;
    }

    $invoice = Invoice::readById($id);
    if (!$invoice) {
        header('Location: dashboard.php?error=not_found');
        exit;
    }

    // Check if there are any payments
    require_once __DIR__ . '/../../app/models/Payment.php';
    $payments = Payment::readAllByInvoice($id);
    if (!empty($payments)) {
        header('Location: invoice_detail.php?id=' . $id . '&error=' . urlencode('Cannot void an invoice that has payments. Delete payments first.'));
        exit;
    }

    if (Invoice::updateStatus($id, 'VOIDED', $_SESSION['user_id'])) {
        header('Location: invoice_detail.php?id=' . $id . '&success=' . urlencode('Invoice voided successfully.'));
    } else {
        header('Location: invoice_detail.php?id=' . $id . '&error=' . urlencode('Failed to void invoice.'));
    }
} else {
    header('Location: dashboard.php');
}
exit;
