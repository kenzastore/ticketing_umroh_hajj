<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Invoice.php';
require_once __DIR__ . '/../../app/models/Payment.php';
require_once __DIR__ . '/../../includes/pdf_engine.php';

check_auth(['finance', 'admin']);

$invoice_id = $_GET['id'] ?? null;
if (!$invoice_id) {
    die('Invoice ID is required.');
}

$invoice = Invoice::readById($invoice_id);
if (!$invoice) {
    die('Invoice not found.');
}

// Fetch related payments for the footer/summary
$payments = Payment::readAllByInvoice($invoice_id);

// Data for template
$data = [
    'invoice' => $invoice,
    'payments' => $payments
];

// Generate PDF
$filename = "Invoice-" . str_replace('/', '-', $invoice['invoice_no']) . ".pdf";

try {
    // Check if vendor/autoload.php exists before attempting to render
    if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        throw new Exception("PDF Engine (dompdf) not installed. Please run 'composer install'.");
    }
    
    PDFEngine::render('app/templates/invoice_template.php', $data, $filename, 'stream');
} catch (Exception $e) {
    die("Error generating PDF: " . $e->getMessage());
}