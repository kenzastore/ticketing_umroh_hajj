<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class Invoice {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new invoice with its flight and fare lines.
     */
    public static function create(array $header, array $flightLines, array $fareLines) {
        try {
            self::$pdo->beginTransaction();

            $sqlHeader = "INSERT INTO invoices (
                invoice_no, invoice_date, corporate_id, corporate_name, attention_to, 
                address, ref_text, pnr, tour_code, total_pax, fare_per_pax, amount_idr
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtHeader = self::$pdo->prepare($sqlHeader);
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
            
            $invoiceId = self::$pdo->lastInsertId();

            // Insert Flight Lines
            $sqlFlight = "INSERT INTO invoice_flight_lines (invoice_id, line_no, flight_date, flight_no, sector, time_range) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtFlight = self::$pdo->prepare($sqlFlight);
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
            $stmtFare = self::$pdo->prepare($sqlFare);
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

            self::$pdo->commit();
            return $invoiceId;
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            error_log("Error creating invoice: " . $e->getMessage());
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
Invoice::init($pdo);