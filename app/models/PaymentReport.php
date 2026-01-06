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
            // This is complex. We need both "Selling" and "Nett" lines.
            // For now, let's fetch from payment_report_lines if they exist, 
            // or aggregate from invoices/payments.
            // Based on PDF, it looks like custom entries.
            $stmtLines = self::$pdo->prepare("
                SELECT * FROM payment_report_lines 
                WHERE reference_id = ? OR flight_no IN (SELECT flight_no FROM flight_legs WHERE movement_id = ?)
                ORDER BY payment_date ASC
            ");
            // This query is a placeholder. Real implementation depends on how lines are linked.
            // Using PNR or Tour Code as reference might be better.
            $stmtLines = self::$pdo->prepare("
                SELECT * FROM payment_report_lines 
                WHERE reference_id = ? 
                ORDER BY id ASC
            ");
            $stmtLines->execute([$movement['pnr']]);
            $movement['report_lines'] = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

            return $movement;
        } catch (PDOException $e) {
            error_log("Error fetching payment report: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize
global $pdo;
PaymentReport::init($pdo);
