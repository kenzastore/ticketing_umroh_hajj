<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class Payment {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Records a new payment for an invoice.
     * @param array $data Associative array containing payment data (invoice_id, amount_paid, payment_date, payment_method, reference_number, notes).
     * @return int|false The ID of the newly created payment, or false on failure.
     */
    public static function create(array $data) {
        $sql = "INSERT INTO payments (invoice_id, amount_paid, payment_date, payment_method, reference_number, notes, receipt_hash) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try {
            self::$pdo->beginTransaction();

            // Generate Receipt Hash
            $receiptHash = hash('sha256', $data['invoice_id'] . $data['amount_paid'] . $data['payment_date'] . uniqid(mt_rand(), true));

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['invoice_id'],
                $data['amount_paid'],
                $data['payment_date'],
                $data['payment_method'],
                $data['reference_number'] ?? null,
                $data['notes'] ?? null,
                $receiptHash
            ]);
            $paymentId = self::$pdo->lastInsertId();

            // Update invoice status based on total payments
            self::updateInvoiceStatus(self::$pdo, $data['invoice_id']);

            self::$pdo->commit();

            // Audit Log
            $userId = $_SESSION['user_id'] ?? null;
            AuditLog::log($userId, 'PAYMENT_RECORDED', 'payment', $paymentId, null, json_encode(array_merge($data, ['receipt_hash' => $receiptHash])));

            return $paymentId;
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            error_log("Error recording payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all payments for a given invoice.
     * @param int $invoiceId The ID of the invoice.
     * @return array An array of payment records.
     */
    public static function readAllByInvoice(int $invoiceId) {
        $sql = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY payment_date DESC, created_at DESC";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$invoiceId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading payments for invoice: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper to update invoice and booking status based on payments received.
     * @param PDO $pdo
     * @param int $invoiceId
     * @return void
     */
    private static function updateInvoiceStatus(PDO $pdo, int $invoiceId) {
        // Get invoice details
        $stmt = $pdo->prepare("SELECT booking_id, amount_total FROM invoices WHERE id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            return;
        }

        // Calculate total paid for this invoice
        $stmt = $pdo->prepare("SELECT SUM(amount_paid) FROM payments WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        $totalPaid = $stmt->fetchColumn();

        $newInvoiceStatus = 'UNPAID';
        $newBookingStatus = null;

        if ($totalPaid >= $invoice['amount_total']) {
            $newInvoiceStatus = 'PAID';
            $newBookingStatus = 'PAID_FULL';
        } elseif ($totalPaid > 0) {
            $newInvoiceStatus = 'PARTIALLY_PAID';
            $newBookingStatus = 'PAID_DEPOSIT';
        }

        // Update invoice status
        $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE id = ?");
        $stmt->execute([$newInvoiceStatus, $invoiceId]);

        // Update booking status if applicable
        if ($newBookingStatus) {
            $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->execute([$newBookingStatus, $invoice['booking_id']]);
        }
    }
}

// Initialize the PDO instance for the Payment class
Payment::init($pdo);
