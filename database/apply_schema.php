<?php
require_once __DIR__ . '/../includes/db_connect.php';

echo "Applying schema from database/schema.sql...\n";

try {
    // 1. Disable FK checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // 2. Drop existing tables to ensure a clean slate (optional but recommended for schema change)
    // We'll query all table names and drop them.
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
        echo "Dropped table: $table\n";
    }
    
    // 3. Apply Schema
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove comments
    $statements = explode(';', $sql);

    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                echo "Error executing statement: \n" . substr($stmt, 0, 100) . "...\n";
                echo "Message: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 4. Re-enable FK checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Schema application finished successfully.\n";

} catch (PDOException $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}

