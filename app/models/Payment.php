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
     * @param array $data Associative array containing payment data.
     * @return int|false The ID of the newly created payment, or false on failure.
     */
    public static function create(array $data) {
        $sql = "INSERT INTO payments (invoice_id, amount_paid, payment_date, payment_method, reference_number, notes, receipt_hash) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try {
            $inTransaction = self::$pdo->inTransaction();
            if (!$inTransaction) self::$pdo->beginTransaction();

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

            // Update Invoice Status
            self::updateInvoiceStatus(self::$pdo, $data['invoice_id']);

            // Update Movement Status if stage is provided
            if (!empty($data['payment_stage'])) {
                self::updateMovementStatus(self::$pdo, $data['invoice_id'], $data['payment_stage']);
            }

            if (!$inTransaction) self::$pdo->commit();

            // Audit Log
            $userId = $_SESSION['user_id'] ?? null;
            AuditLog::log($userId, 'PAYMENT_RECORDED', 'payment', $paymentId, null, json_encode(array_merge($data, ['receipt_hash' => $receiptHash])));

            return $paymentId;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error recording payment: " . $e->getMessage());
            return false;
        }
    }

    public static function readAllByInvoice(int $invoiceId) {
        $sql = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY payment_date DESC, created_at DESC";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$invoiceId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading payments: " . $e->getMessage());
            return [];
        }
    }

    private static function updateInvoiceStatus(PDO $pdo, int $invoiceId) {
        $stmt = $pdo->prepare("SELECT amount_idr FROM invoices WHERE id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) return;

        $stmt = $pdo->prepare("SELECT SUM(amount_paid) FROM payments WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        $totalPaid = $stmt->fetchColumn();

        $newStatus = 'UNPAID';
        if ($totalPaid >= $invoice['amount_idr']) {
            $newStatus = 'PAID';
        } elseif ($totalPaid > 0) {
            $newStatus = 'PARTIALLY_PAID';
        }

        $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $invoiceId]);
    }

    private static function updateMovementStatus(PDO $pdo, int $invoiceId, string $stage) {
        // Link Invoice -> PNR -> Movement
        $stmt = $pdo->prepare("SELECT pnr FROM invoices WHERE id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice || empty($invoice['pnr'])) return;

        $column = '';
        if ($stage === 'DP1') $column = 'dp1_status';
        elseif ($stage === 'DP2') $column = 'dp2_status';
        elseif ($stage === 'Full Payment') $column = 'fp_status';

        if ($column) {
            // Find movement by PNR
            $stmt = $pdo->prepare("UPDATE movements SET $column = 'PAID' WHERE pnr = ?");
            $stmt->execute([$invoice['pnr']]);
        }
    }
}

global $pdo;
Payment::init($pdo);