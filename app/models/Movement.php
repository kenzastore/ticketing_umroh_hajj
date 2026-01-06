<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class Movement {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Reads all movements from the database.
     * @return array
     */
    public static function readAll() {
        $sql = "SELECT * FROM movements ORDER BY created_date DESC, id DESC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading movements: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reads a single movement with its flight legs.
     * @param int|string $id
     * @return array|false
     */
    public static function readById($id) {
        try {
            $stmt = self::$pdo->prepare("SELECT * FROM movements WHERE id = ?");
            $stmt->execute([$id]);
            $movement = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movement) return false;

            $stmtLegs = self::$pdo->prepare("SELECT * FROM flight_legs WHERE movement_id = ? ORDER BY direction ASC, leg_no ASC");
            $stmtLegs->execute([$id]);
            $movement['legs'] = $stmtLegs->fetchAll(PDO::FETCH_ASSOC);

            return $movement;
        } catch (PDOException $e) {
            error_log("Error reading movement by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates movement status fields (DP1, DP2, FP).
     * @param int|string $id
     * @param array $data
     * @param int|null $userId ID of the user performing the action.
     * @return bool
     */
    public static function updateStatus($id, array $data, $userId = null) {
        $oldMovement = self::readById($id);
        if (!$oldMovement) return false;

        $fields = [];
        $params = [];
        
        if (isset($data['dp1_status'])) { $fields[] = "dp1_status = ?"; $params[] = $data['dp1_status']; }
        if (isset($data['dp2_status'])) { $fields[] = "dp2_status = ?"; $params[] = $data['dp2_status']; }
        if (isset($data['fp_status'])) { $fields[] = "fp_status = ?"; $params[] = $data['fp_status']; }
        if (isset($data['ticketing_done'])) { $fields[] = "ticketing_done = ?"; $params[] = $data['ticketing_done']; }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE movements SET " . implode(', ', $fields) . " WHERE id = ?";
        
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($params);

            // Audit Log
            $newMovement = self::readById($id);
            AuditLog::log($userId, 'UPDATE', 'movement', $id, json_encode($oldMovement), json_encode($newMovement));

            if (!$inTransaction) $db->commit();
            return $result;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error updating movement status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get movements with ticketing deadline within the next $days days.
     * Only includes those where ticketing_done is 0 (not done).
     * @param int $days
     * @return array
     */
    public static function getUpcomingDeadlines($days = 3) {
        $sql = "SELECT * FROM movements 
                WHERE ticketing_deadline IS NOT NULL 
                AND ticketing_deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND ticketing_done = 0
                ORDER BY ticketing_deadline ASC";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting deadlines: " . $e->getMessage());
            return [];
        }
    }
}

// Initialize the PDO instance
global $pdo;
Movement::init($pdo);
