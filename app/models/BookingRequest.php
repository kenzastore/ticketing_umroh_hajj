<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class BookingRequest {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new booking request with its flight legs.
     * @param array $header Data for booking_requests table.
     * @param array $legs Array of leg data for booking_request_legs table.
     * @return int|false The ID of the newly created booking request, or false on failure.
     */
    public static function create(array $header, array $legs) {
        try {
            self::$pdo->beginTransaction();

            $sqlHeader = "INSERT INTO booking_requests (
                request_no, corporate_id, corporate_name, agent_id, agent_name, skyagent_id, 
                group_size, tcp, gp_approved_fare, selling_fare, nett_fare, 
                duration_days, add1_days, ttl_days, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtHeader = self::$pdo->prepare($sqlHeader);
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
            
            $requestId = self::$pdo->lastInsertId();

            $sqlLeg = "INSERT INTO booking_request_legs (
                booking_request_id, leg_no, flight_date, flight_no, sector, origin_iata, dest_iata
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmtLeg = self::$pdo->prepare($sqlLeg);
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

            self::$pdo->commit();
            return $requestId;
        } catch (PDOException $e) {
            self::$pdo->rollBack();
            error_log("Error creating booking request: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all booking requests.
     * @return array
     */
    public static function readAll() {
        $sql = "SELECT * FROM booking_requests ORDER BY created_at DESC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     * @return bool
     */
    public static function delete($id) {
        try {
            // Foreign key with ON DELETE CASCADE will handle legs
            $stmt = self::$pdo->prepare("DELETE FROM booking_requests WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting booking request: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance
BookingRequest::init($pdo);
