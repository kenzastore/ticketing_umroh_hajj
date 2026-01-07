<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class Corporate {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new corporate.
     * @param array $data Associative array containing corporate data (name, address).
     * @param int|null $userId ID of the user performing the action.
     * @return int|false The ID of the newly created corporate, or false on failure.
     */
    public static function create(array $data, $userId = null) {
        $sql = "INSERT INTO corporates (name, address) VALUES (?, ?)";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['address'] ?? null
            ]);
            $corporateId = $db->lastInsertId();

            // Audit Log
            $newCorp = self::readById($corporateId);
            AuditLog::log($userId, 'CREATE', 'corporate', $corporateId, null, json_encode($newCorp));

            if (!$inTransaction) $db->commit();
            return $corporateId;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error creating corporate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all corporates from the database with pagination support.
     * @param int|null $limit
     * @param int $offset
     * @return array An array of corporate records.
     */
    public static function readAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM corporates ORDER BY name ASC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading all corporates: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts total corporates.
     * @return int
     */
    public static function countAll() {
        try {
            return (int)self::$pdo->query("SELECT COUNT(*) FROM corporates")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Reads a single corporate by its ID.
     * @param int|string $id The ID of the corporate to retrieve.
     * @return array|false An associative array of the corporate's data, or false if not found.
     */
    public static function readById($id) {
        $sql = "SELECT * FROM corporates WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading corporate by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing corporate.
     * @param int|string $id The ID of the corporate to update.
     * @param array $data Associative array containing the updated corporate data.
     * @param int|null $userId ID of the user performing the action.
     * @return bool True on success, false on failure.
     */
    public static function update($id, array $data, $userId = null) {
        $oldCorp = self::readById($id);
        if (!$oldCorp) return false;

        $sql = "UPDATE corporates SET name = ?, address = ? WHERE id = ?";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['address'] ?? null,
                $id
            ]);

            // Audit Log
            $newCorp = self::readById($id);
            AuditLog::log($userId, 'UPDATE', 'corporate', $id, json_encode($oldCorp), json_encode($newCorp));

            if (!$inTransaction) $db->commit();
            return true;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error updating corporate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a corporate by its ID.
     * @param int|string $id The ID of the corporate to delete.
     * @param int|null $userId ID of the user performing the action.
     * @return bool True on success, false on failure.
     */
    public static function delete($id, $userId = null) {
        $oldCorp = self::readById($id);
        if (!$oldCorp) return false;

        $sql = "DELETE FROM corporates WHERE id = ?";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);

            // Audit Log
            AuditLog::log($userId, 'DELETE', 'corporate', $id, json_encode($oldCorp), null);

            if (!$inTransaction) $db->commit();
            return true;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error deleting corporate: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the Corporate class
global $pdo;
Corporate::init($pdo);
