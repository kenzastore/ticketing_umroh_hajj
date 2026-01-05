<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class AuditLog {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
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
AuditLog::init($pdo);
