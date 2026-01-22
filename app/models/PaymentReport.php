<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class PaymentReport {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Get consolidated report data by movement ID.
     * @param int|string $movementId
     * @return array|false
     */
    public static function getReportByMovementId($movementId) {
        try {
            // 1. Get Movement/Header Data
            $stmt = self::$pdo->prepare("
                SELECT m.*, a.name as agent_full_name 
                FROM movements m
                LEFT JOIN agents a ON m.agent_id = a.id
                WHERE m.id = ?
            ");
            $stmt->execute([$movementId]);
            $movement = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movement) return false;

            // 2. Get Flight Legs
            $stmtLegs = self::$pdo->prepare("
                SELECT * FROM flight_legs 
                WHERE movement_id = ? 
                ORDER BY direction ASC, leg_no ASC
            ");
            $stmtLegs->execute([$movementId]);
            $movement['legs'] = $stmtLegs->fetchAll(PDO::FETCH_ASSOC);

            // 3. Get Payment/Accounting Lines
            // Fetch all lines for this PNR and separate them by type
            $stmtLines = self::$pdo->prepare("
                SELECT * FROM payment_report_lines 
                WHERE reference_id = ? 
                ORDER BY payment_date ASC, id ASC
            ");
            $stmtLines->execute([$movement['pnr']]);
            $allLines = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

            $movement['sales_lines'] = [];
            $movement['cost_lines'] = [];

            foreach ($allLines as $line) {
                if ($line['table_type'] === 'COST') {
                    $movement['cost_lines'][] = $line;
                } else {
                    $movement['sales_lines'][] = $line;
                }
            }

            return $movement;
        } catch (PDOException $e) {
            error_log("Error fetching payment report: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new payment report line.
     */
    public static function createLine(array $data, $userId = null) {
        $fields = array_keys($data);
        $cols = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO payment_report_lines ($cols) VALUES ($placeholders)";

        try {
            $db = self::$pdo;
            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($data));
            $id = $db->lastInsertId();

            AuditLog::log($userId, 'CREATE', 'payment_report_line', $id, null, json_encode($data));
            return $id;
        } catch (PDOException $e) {
            error_log("Error creating payment report line: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing payment report line.
     */
    public static function updateLine($id, array $data, $userId = null) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE payment_report_lines SET " . implode(', ', $fields) . " WHERE id = ?";

        try {
            $db = self::$pdo;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            AuditLog::log($userId, 'UPDATE', 'payment_report_line', $id, null, json_encode($data));
            return true;
        } catch (PDOException $e) {
            error_log("Error updating payment report line: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a payment report line.
     */
    public static function deleteLine($id, $userId = null) {
        $sql = "DELETE FROM payment_report_lines WHERE id = ?";
        try {
            $db = self::$pdo;
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);

            AuditLog::log($userId, 'DELETE', 'payment_report_line', $id, null, null);
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting payment report line: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize
global $pdo;
PaymentReport::init($pdo);
