<?php
/**
 * UAT Data Seeding Script
 * Generates 100 Indonesian dummy records for testing.
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../app/models/Agent.php';
require_once __DIR__ . '/../app/models/Corporate.php';
require_once __DIR__ . '/../app/models/BookingRequest.php';
require_once __DIR__ . '/../app/models/Movement.php';
require_once __DIR__ . '/../app/models/Invoice.php';
require_once __DIR__ . '/../app/models/Payment.php';

// Data Constants
define('INDONESIAN_NAMES', [
    'Budi Santoso', 'Siti Aminah', 'Agus Prayitno', 'Dewi Lestari', 'Eko Saputro',
    'Rina Wijaya', 'Hendra Kusuma', 'Sri Wahyuni', 'Andi Pratama', 'Maya Sari',
    'Joko Susilo', 'Ani Rahayu', 'Dedi Kurniawan', 'Lilik Setiawan', 'Siska Amelia',
    'Rully Hidayat', 'Yuni Kartika', 'Fajar Ramadhan', 'Indah Permata', 'Zulkifli Mansyur'
]);

define('INDONESIAN_AGENTS', [
    'Mutiara Tour & Travel',
    'Amanah Wisata Umroh',
    'Barokah Haji Services',
    'Cahaya Iman Jakarta',
    'Duta Mulia Surabaya'
]);

define('INDONESIAN_CORPORATES', [
    'PT Maju Jaya Abadi',
    'CV Sumber Makmur',
    'Yayasan Amal Sholeh',
    'Koperasi Karyawan Sejahtera',
    'Bank Syariah Indonesia (Cabang)'
]);

define('INDONESIAN_AIRPORTS', ['SUB', 'CGK', 'JOG', 'DPS', 'KNO']);
define('DESTINATION_AIRPORTS', ['JED', 'MED', 'SIN']);
define('AIRLINE_CARRIERS', ['Garuda Indonesia', 'Saudia Airlines', 'Lion Air', 'Citilink', 'Scoot']);

// Ensure global $pdo is available for models
if (isset($pdo)) {
    Agent::init($pdo);
    Corporate::init($pdo);
    BookingRequest::init($pdo);
    Movement::init($pdo);
    Invoice::init($pdo);
    Payment::init($pdo);
}

echo "UAT Seeding Script Initialized.\n";

