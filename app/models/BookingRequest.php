<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class BookingRequest {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new booking request with its flight legs.
     * @param array $header Data for booking_requests table.
     * @param array $legs Array of leg data for booking_request_legs table.
     * @param int|null $userId ID of the user performing the action.
     * @return int|false The ID of the newly created booking request, or false on failure.
     */
    public static function create(array $header, array $legs, $userId = null) {
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $sqlHeader = "INSERT INTO booking_requests (
                request_no, corporate_id, corporate_name, agent_id, agent_name, skyagent_id, 
                group_size, tcp, gp_approved_fare, selling_fare, nett_fare, 
                duration_days, add1_days, ttl_days, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtHeader = $db->prepare($sqlHeader);
            $stmtHeader->execute([
                $header['request_no'] ?? null,
                $header['corporate_id'] ?? null,
                $header['corporate_name'] ?? null,
                $header['agent_id'] ?? null,
                $header['agent_name'] ?? null,
                $header['skyagent_id'] ?? null,
                $header['group_size'] ?? null,
                $header['tcp'] ?? null,
                $header['gp_approved_fare'] ?? null,
                $header['selling_fare'] ?? null,
                $header['nett_fare'] ?? null,
                $header['duration_days'] ?? null,
                $header['add1_days'] ?? null,
                $header['ttl_days'] ?? null,
                $header['notes'] ?? null
            ]);
            
            $requestId = $db->lastInsertId();

            $sqlLeg = "INSERT INTO booking_request_legs (
                booking_request_id, leg_no, flight_date, flight_no, sector, origin_iata, dest_iata
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmtLeg = $db->prepare($sqlLeg);
            foreach ($legs as $index => $leg) {
                $stmtLeg->execute([
                    $requestId,
                    $leg['leg_no'] ?? ($index + 1),
                    $leg['flight_date'] ?? null,
                    $leg['flight_no'] ?? null,
                    $leg['sector'] ?? null,
                    $leg['origin_iata'] ?? null,
                    $leg['dest_iata'] ?? null
                ]);
            }

            // Audit Log
            $newRequest = self::readById($requestId);
            AuditLog::log($userId, 'CREATE', 'booking_request', $requestId, null, json_encode($newRequest));

            if (!$inTransaction) $db->commit();
            return $requestId;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error creating booking request: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all booking requests with their flight legs.
     * @return array
     */
    public static function readAll() {
        $sql = "SELECT * FROM booking_requests ORDER BY created_at DESC";
        try {
            $stmt = self::$pdo->query($sql);
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($requests)) {
                return [];
            }

            // Get IDs to fetch legs
            $ids = array_column($requests, 'id');
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            $sqlLegs = "SELECT * FROM booking_request_legs WHERE booking_request_id IN ($placeholders) ORDER BY leg_no ASC";
            $stmtLegs = self::$pdo->prepare($sqlLegs);
            $stmtLegs->execute($ids);
            $allLegs = $stmtLegs->fetchAll(PDO::FETCH_ASSOC);

            // Group legs by request_id
            $legsByRequest = [];
            foreach ($allLegs as $leg) {
                $legsByRequest[$leg['booking_request_id']][] = $leg;
            }

            // Attach legs to requests
            foreach ($requests as &$req) {
                $req['legs'] = $legsByRequest[$req['id']] ?? [];
            }
            unset($req); // Break reference

            return $requests;
        } catch (PDOException $e) {
            error_log("Error reading booking requests: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reads a single booking request with its legs.
     * @param int|string $id
     * @return array|false
     */
    public static function readById($id) {
        try {
            $stmt = self::$pdo->prepare("SELECT * FROM booking_requests WHERE id = ?");
            $stmt->execute([$id]);
            $header = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$header) return false;

            $stmtLegs = self::$pdo->prepare("SELECT * FROM booking_request_legs WHERE booking_request_id = ? ORDER BY leg_no ASC");
            $stmtLegs->execute([$id]);
            $header['legs'] = $stmtLegs->fetchAll(PDO::FETCH_ASSOC);

            return $header;
        } catch (PDOException $e) {
            error_log("Error reading booking request by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a booking request and its legs.
     * @param int|string $id
     * @param int|null $userId ID of the user performing the action.
     * @return bool
     */
    public static function delete($id, $userId = null) {
        $oldRequest = self::readById($id);
        if (!$oldRequest) return false;

        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            // Audit Log
            AuditLog::log($userId, 'DELETE', 'booking_request', $id, json_encode($oldRequest), null);

            // Foreign key with ON DELETE CASCADE will handle legs
            $stmt = $db->prepare("DELETE FROM booking_requests WHERE id = ?");
            $result = $stmt->execute([$id]);

            if (!$inTransaction) $db->commit();
            return $result;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error deleting booking request: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance
global $pdo;
BookingRequest::init($pdo);
