<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class PaymentAdvise {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new payment advise.
     */
    public static function create(array $data, $userId = null) {
        $fields = array_keys($data);
        if (empty($fields)) return false;

        $cols = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO payment_advises ($cols) VALUES ($placeholders)";

        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($data));
            $id = $db->lastInsertId();

            // Audit Log
            $newAdvise = self::readById($id);
            AuditLog::log($userId, 'CREATE', 'payment_advise', $id, null, json_encode($newAdvise));

            if (!$inTransaction) $db->commit();
            return $id;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error creating payment advise: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all payment advises.
     */
    public static function readAll() {
        $sql = "SELECT * FROM payment_advises ORDER BY created_at DESC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading payment advises: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reads a single payment advise by ID.
     */
    public static function readById($id) {
        try {
            $stmt = self::$pdo->prepare("SELECT * FROM payment_advises WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading payment advise by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing payment advise.
     */
    public static function update($id, array $data, $userId = null) {
        $oldAdvise = self::readById($id);
        if (!$oldAdvise) return false;

        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE payment_advises SET " . implode(', ', $fields) . " WHERE id = ?";

        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($params);

            // Audit Log
            $newAdvise = self::readById($id);
            AuditLog::log($userId, 'UPDATE', 'payment_advise', $id, json_encode($oldAdvise), json_encode($newAdvise));

            if (!$inTransaction) $db->commit();
            return $result;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error updating payment advise: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a payment advise.
     */
    public static function delete($id, $userId = null) {
        $oldAdvise = self::readById($id);
        if (!$oldAdvise) return false;

        $sql = "DELETE FROM payment_advises WHERE id = ?";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$id]);

            // Audit Log
            AuditLog::log($userId, 'DELETE', 'payment_advise', $id, json_encode($oldAdvise), null);

            if (!$inTransaction) $db->commit();
            return $result;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error deleting payment advise: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize
global $pdo;
PaymentAdvise::init($pdo);
