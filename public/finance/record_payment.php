<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Payment.php';

check_auth('finance');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = $_POST['invoice_id'] ?? null;
    $amount_paid = $_POST['amount_paid'] ?? 0;
    $payment_date = $_POST['payment_date'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $payment_stage = $_POST['payment_stage'] ?? null;
    $reference_number = $_POST['reference_number'] ?? null;
    $notes = $_POST['notes'] ?? null;

    if (!$invoice_id || empty($amount_paid) || $amount_paid <= 0 || empty($payment_date) || empty($payment_method)) {
        header('Location: invoice_detail.php?id=' . $invoice_id . '&payment_error=' . urlencode('All required fields must be filled.'));
        exit;
    }

    $paymentData = [
        'invoice_id' => $invoice_id,
        'amount_paid' => $amount_paid,
        'payment_date' => $payment_date,
        'payment_method' => $payment_method,
        'payment_stage' => $payment_stage,
        'reference_number' => $reference_number,
        'notes' => $notes
    ];

    if (Payment::create($paymentData)) {
        header('Location: invoice_detail.php?id=' . $invoice_id . '&payment_success=' . urlencode('Payment recorded successfully.'));
    } else {
        header('Location: invoice_detail.php?id=' . $invoice_id . '&payment_error=' . urlencode('Failed to record payment.'));
    }
    exit;
} else {
    header('Location: dashboard.php');
    exit;
}
