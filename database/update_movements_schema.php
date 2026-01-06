<?php
require_once __DIR__ . '/../includes/db_connect.php';

$queries = [
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS category ENUM('UMRAH', 'HAJJI') DEFAULT 'UMRAH' AFTER movement_no",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS approved_fare_currency VARCHAR(10) DEFAULT 'IDR' AFTER pattern_code",
    "ALTER TABLE movements MODIFY COLUMN approved_fare DECIMAL(18,2) NULL",
    "ALTER TABLE movements MODIFY COLUMN selling_fare DECIMAL(18,2) NULL",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS nett_selling_total DECIMAL(18,2) NULL AFTER nett_selling",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS total_selling_all DECIMAL(18,2) NULL AFTER total_selling",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS first_deposit_airlines_date DATE NULL AFTER deposit1_airlines_amount",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS first_deposit_eemw_date DATE NULL AFTER first_deposit_airlines_date",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS second_deposit_airlines_date DATE NULL AFTER deposit2_airlines_amount",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS second_deposit_eemw_date DATE NULL AFTER second_deposit_airlines_date",
    "ALTER TABLE movements ADD COLUMN IF NOT EXISTS time_limit_manifest_ticketing DATE NULL AFTER fullpay_eemw_date"
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "Executed: $sql\n";
    } catch (PDOException $e) {
        // Ignore "Duplicate column name" error if IF NOT EXISTS fails or isn't fully supported
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "Skipped (Column already exists): $sql\n";
        } else {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

try {
    $pdo->exec("CREATE INDEX idx_mv_category ON movements(category)");
    echo "Index created: idx_mv_category\n";
} catch (PDOException $e) {
    echo "Index skipped (maybe already exists): " . $e->getMessage() . "\n";
}

