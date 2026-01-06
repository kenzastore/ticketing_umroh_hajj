<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class Invoice {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new invoice with its flight and fare lines.
     * @param array $header
     * @param array $flightLines
     * @param array $fareLines
     * @param int|null $userId
     * @return int|false
     */
    public static function create(array $header, array $flightLines, array $fareLines, $userId = null) {
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $sqlHeader = "INSERT INTO invoices (
                invoice_no, invoice_date, corporate_id, corporate_name, attention_to, 
                address, ref_text, pnr, tour_code, total_pax, fare_per_pax, amount_idr
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtHeader = $db->prepare($sqlHeader);
            $stmtHeader->execute([
                $header['invoice_no'] ?? null,
                $header['invoice_date'] ?? date('Y-m-d'),
                $header['corporate_id'] ?? null,
                $header['corporate_name'] ?? null,
                $header['attention_to'] ?? null,
                $header['address'] ?? null,
                $header['ref_text'] ?? null,
                $header['pnr'] ?? null,
                $header['tour_code'] ?? null,
                $header['total_pax'] ?? 0,
                $header['fare_per_pax'] ?? 0,
                $header['amount_idr'] ?? 0
            ]);
            
            $invoiceId = $db->lastInsertId();

            // Insert Flight Lines
            $sqlFlight = "INSERT INTO invoice_flight_lines (invoice_id, line_no, flight_date, flight_no, sector, time_range) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtFlight = $db->prepare($sqlFlight);
            foreach ($flightLines as $index => $line) {
                $stmtFlight->execute([
                    $invoiceId,
                    $line['line_no'] ?? ($index + 1),
                    $line['flight_date'] ?? null,
                    $line['flight_no'] ?? null,
                    $line['sector'] ?? null,
                    $line['time_range'] ?? null
                ]);
            }

            // Insert Fare Lines
            $sqlFare = "INSERT INTO invoice_fare_lines (invoice_id, line_no, dot_date, description, total_pax, fare_amount, amount_idr) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtFare = $db->prepare($sqlFare);
            foreach ($fareLines as $index => $line) {
                $stmtFare->execute([
                    $invoiceId,
                    $line['line_no'] ?? ($index + 1),
                    $line['dot_date'] ?? null,
                    $line['description'] ?? null,
                    $line['total_pax'] ?? 0,
                    $line['fare_amount'] ?? 0,
                    $line['amount_idr'] ?? 0
                ]);
            }

            // Audit Log
            $newInvoice = self::readById($invoiceId);
            AuditLog::log($userId, 'CREATE', 'invoice', $invoiceId, null, json_encode($newInvoice));

            if (!$inTransaction) $db->commit();
            return $invoiceId;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error creating invoice: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates invoice status with audit logging.
     * @param int|string $id
     * @param string $status
     * @param int|null $userId
     * @return bool
     */
    public static function updateStatus($id, string $status, $userId = null) {
        $oldInvoice = self::readById($id);
        if (!$oldInvoice) return false;

        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare("UPDATE invoices SET status = ? WHERE id = ?");
            $result = $stmt->execute([$status, $id]);

            // Audit Log
            $newInvoice = self::readById($id);
            AuditLog::log($userId, 'STATUS_CHANGE', 'invoice', $id, json_encode($oldInvoice), json_encode($newInvoice));

            if (!$inTransaction) $db->commit();
            return $result;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error updating invoice status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all invoices.
     */
    public static function readAll() {
        $sql = "SELECT * FROM invoices ORDER BY invoice_date DESC, id DESC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading invoices: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reads a single invoice with all lines.
     */
    public static function readById($id) {
        try {
            $stmt = self::$pdo->prepare("SELECT * FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$invoice) return false;

            $stmtF = self::$pdo->prepare("SELECT * FROM invoice_flight_lines WHERE invoice_id = ? ORDER BY line_no ASC");
            $stmtF->execute([$id]);
            $invoice['flight_lines'] = $stmtF->fetchAll(PDO::FETCH_ASSOC);

            $stmtFare = self::$pdo->prepare("SELECT * FROM invoice_fare_lines WHERE invoice_id = ? ORDER BY line_no ASC");
            $stmtFare->execute([$id]);
            $invoice['fare_lines'] = $stmtFare->fetchAll(PDO::FETCH_ASSOC);

            return $invoice;
        } catch (PDOException $e) {
            error_log("Error reading invoice by ID: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance
global $pdo;
Invoice::init($pdo);