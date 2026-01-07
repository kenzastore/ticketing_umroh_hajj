<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class Notification {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    public static function create($data) {
        $sql = "INSERT INTO notifications (entity_type, entity_id, message, alert_type) VALUES (?, ?, ?, ?)";
        try {
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute([
                $data['entity_type'] ?? null,
                $data['entity_id'] ?? null,
                $data['message'],
                $data['alert_type'] ?? 'SYSTEM'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    public static function getUnread() {
        $sql = "SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function markAsRead($id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function markAllAsRead() {
        $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
        try {
            return self::$pdo->exec($sql);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function existsUnread($entityType, $entityId, $messagePrefix = '') {
        $sql = "SELECT id FROM notifications 
                WHERE entity_type = ? 
                AND entity_id = ? 
                AND is_read = 0";
        $params = [$entityType, $entityId];

        if (!empty($messagePrefix)) {
            $sql .= " AND message LIKE ?";
            $params[] = $messagePrefix . '%';
        }

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return (bool) $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (isset($pdo)) {
    Notification::init($pdo);
}
