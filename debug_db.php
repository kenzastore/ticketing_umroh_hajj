<?php
require_once __DIR__ . '/includes/db_connect.php';

echo "--- INVOICES ---\\n";
$stmt = $pdo->query("SELECT id, invoice_no, pnr FROM invoices");
$invs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($invs)) {
    echo "No invoices found.\\n";
} else {
    foreach ($invs as $i) {
        echo "ID: {$i['id']}, No: {$i['invoice_no']}, PNR: " . ($i['pnr'] ?: 'NULL') . "\\n";
    }
}

echo "\\n--- MOVEMENTS ---\\n";
$stmt = $pdo->query("SELECT id, pnr FROM movements");
$mvs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($mvs)) {
    echo "No movements found.\\n";
} else {
    foreach ($mvs as $m) {
        echo "ID: {$m['id']}, PNR: " . ($m['pnr'] ?: 'NULL') . "\\n";
    }
}

