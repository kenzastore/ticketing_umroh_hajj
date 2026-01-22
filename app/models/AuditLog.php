<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class AuditLog {
    private static $pdo;
    private static $currentUserId = null;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    public static function setCurrentUser($userId) {
        self::$currentUserId = $userId;
    }

    public static function getCurrentUser() {
        return self::$currentUserId;
    }

    /**
     * Reads audit logs with filtering and pagination.
     */
    public static function readAll($limit = 50, $offset = 0, $filters = []) {
        $sql = "SELECT a.*, u.username FROM audit_logs a 
                LEFT JOIN users u ON a.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = ?";
            $params[] = $filters['user_id'];
        }
        if (!empty($filters['entity_type'])) {
            $sql .= " AND a.entity_type = ?";
            $params[] = $filters['entity_type'];
        }
        if (!empty($filters['action'])) {
            $sql .= " AND a.action = ?";
            $params[] = $filters['action'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading audit logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts total audit logs with filters.
     */
    public static function countAll($filters = []) {
        $sql = "SELECT COUNT(*) FROM audit_logs a WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = ?";
            $params[] = $filters['user_id'];
        }
        if (!empty($filters['entity_type'])) {
            $sql .= " AND a.entity_type = ?";
            $params[] = $filters['entity_type'];
        }
        if (!empty($filters['action'])) {
            $sql .= " AND a.action = ?";
            $params[] = $filters['action'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Logs an action to the audit_logs table.
     * 
     * @param int|null $userId The ID of the user performing the action (can be null for system actions).
     * @param string $action The action name (e.g., 'INVOICE_CREATED').
     * @param string $entityType The type of entity modified (e.g., 'invoice', 'payment').
     * @param int $entityId The ID of the entity.
     * @param string|null $oldValue JSON or string representation of the old value.
     * @param string|null $newValue JSON or string representation of the new value.
     * @return bool True on success.
     */
    public static function log($userId, string $action, string $entityType, int $entityId, $oldValue = null, $newValue = null) {
        if ($userId === null || $userId === false || $userId === '') {
            $userId = self::$currentUserId;
        }
        
        // Final safety check for integer user_id
        if (!is_numeric($userId) || (int)$userId <= 0) {
            $userId = null;
        } else {
            $userId = (int)$userId;
        }

        if (is_array($oldValue) || is_object($oldValue)) {
            $oldValue = json_encode($oldValue);
        }
        if (is_array($newValue) || is_object($newValue)) {
            $newValue = json_encode($newValue);
        }

        $sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_value, new_value) VALUES (?, ?, ?, ?, ?, ?)";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $userId,
                $action,
                $entityType,
                $entityId,
                $oldValue,
                $newValue
            ]);
            return true;
        } catch (PDOException $e) {
            // Silently fail or log to file to avoid breaking the main transaction
            error_log("Audit Log Error: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize
global $pdo;
AuditLog::init($pdo);
