<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';

use Dompdf\Dompdf;
use Dompdf\Options;

check_auth(['finance', 'admin']);

$paymentId = $_GET['id'] ?? null;
if (!$paymentId) die("ID required.");

// Fetch Data
$stmt = $pdo->prepare("
    SELECT p.*, i.invoice_no, i.corporate_name, i.pnr, i.tour_code 
    FROM payments p
    JOIN invoices i ON p.invoice_id = i.id
    WHERE p.id = ?
");
$stmt->execute([$paymentId]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) die("Payment not found.");

$invoice = [
    'invoice_no' => $payment['invoice_no'],
    'corporate_name' => $payment['corporate_name'],
    'pnr' => $payment['pnr']
];

// Generate QR Code URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443 ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$verifyUrl = "$protocol://$host/verify_receipt.php?token=" . $payment['receipt_hash'];
$qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verifyUrl);

// Fetch QR code and encode as Base64 to avoid Dompdf remote fetch issues
$qrCodeData = false;
if (function_exists('curl_version')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qrApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Avoid SSL issues in some environments
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $qrCodeData = curl_exec($ch);
    curl_close($ch);
}

if (!$qrCodeData) {
    $qrCodeData = @file_get_contents($qrApiUrl);
}

$qrUrl = '';
if ($qrCodeData) {
    $qrUrl = 'data:image/png;base64,' . base64_encode($qrCodeData);
}

// Setup Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Load Template
ob_start();
include __DIR__ . '/../../app/templates/receipt_template.php';
$html = ob_get_clean();

// Replace placeholder logic in template if needed, or update template to use these variables.
// The existing template already uses $payment, $invoice, and $payment['receipt_hash'].
// I'll update it to use $qrUrl instead of placeholder.

$dompdf->loadHtml($html);
$dompdf->setPaper('A5', 'portrait');
$dompdf->render();

$filename = "Receipt_" . $payment['pnr'] . "_" . date('Ymd') . ".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
