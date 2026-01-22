<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class Movement {
    private static $pdo;
    private static $lastError = null;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    public static function getLastError() {
        return self::$lastError;
    }

    /**
     * Reads all movements from the database, optionally filtered by category.
     * @param string|null $category 'UMRAH' or 'HAJJI'
     * @return array
     */
    public static function readAll($category = null) {
        $sql = "SELECT * FROM movements";
        $params = [];
        if ($category) {
            $sql .= " WHERE category = ?";
            $params[] = $category;
        }
        $sql .= " ORDER BY created_date DESC, id DESC";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading movements: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Creates a new movement with optional flight legs.
     * @param array $data
     * @param array $legs
     * @param int|null $userId
     * @return int|false
     */
    public static function create(array $data, array $legs = [], $userId = null) {
        self::$lastError = null;
        $fields = array_keys($data);
        if (empty($fields)) return false;

        // TCP Validation
        $tourCode = $data['tour_code'] ?? null;
        $movementNo = $data['movement_no'] ?? null;
        $paxCount = $data['passenger_count'] ?? 0;
        
        if ($tourCode && $movementNo) {
            $tcp = $data['tcp'] ?? self::getGroupTcp($tourCode, $movementNo);
            if ($tcp !== null) {
                $currentSum = self::getGroupPassengerSum($tourCode, $movementNo);
                if (($currentSum + $paxCount) > $tcp) {
                    self::$lastError = "TCP validation failed: Total passengers (" . ($currentSum + $paxCount) . ") would exceed TCP ($tcp) for group $tourCode / $movementNo.";
                    return false;
                }
            }
        }

        $cols = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO movements ($cols) VALUES ($placeholders)";

        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($data));
            $id = $db->lastInsertId();

            // Insert Flight Legs if provided
            if (!empty($legs)) {
                $sqlLeg = "INSERT INTO flight_legs (
                    movement_id, leg_no, direction, carrier, flight_no, sector, 
                    origin_iata, dest_iata, scheduled_departure, scheduled_arrival, time_range
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtLeg = $db->prepare($sqlLeg);
                foreach ($legs as $index => $leg) {
                    $stmtLeg->execute([
                        $id,
                        $leg['leg_no'] ?? ($index + 1),
                        $leg['direction'] ?? 'OUT',
                        $leg['carrier'] ?? $data['carrier'] ?? null,
                        $leg['flight_no'] ?? null,
                        $leg['sector'] ?? null,
                        $leg['origin_iata'] ?? null,
                        $leg['dest_iata'] ?? null,
                        $leg['flight_date'] ?? $leg['scheduled_departure'] ?? null, // handle both formats
                        $leg['scheduled_arrival'] ?? null,
                        $leg['time_range'] ?? null
                    ]);
                }
            }

            // Audit Log
            $newMovement = self::readById($id);
            AuditLog::log($userId, 'CREATE', 'movement', $id, null, json_encode($newMovement));

            if (!$inTransaction) $db->commit();
            return $id;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error creating movement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing movement.
     * @param int|string $id
     * @param array $data
     * @param int|null $userId
     * @return bool
     */
    public static function update($id, array $data, $userId = null) {
        self::$lastError = null;
        $oldMovement = self::readById($id);
        if (!$oldMovement) return false;

        // TCP Validation
        $tourCode = $data['tour_code'] ?? $oldMovement['tour_code'];
        $movementNo = $data['movement_no'] ?? $oldMovement['movement_no'];
        $newPaxCount = isset($data['passenger_count']) ? (int)$data['passenger_count'] : (int)$oldMovement['passenger_count'];
        $newTcp = isset($data['tcp']) ? (int)$data['tcp'] : null;

        if ($tourCode && $movementNo) {
            $tcp = $newTcp ?? self::getGroupTcp($tourCode, $movementNo);
            if ($tcp !== null) {
                $currentSum = self::getGroupPassengerSum($tourCode, $movementNo);
                // Adjust sum: remove old count for THIS record, add new count
                $adjustedSum = $currentSum - (int)$oldMovement['passenger_count'] + $newPaxCount;
                
                if ($adjustedSum > $tcp) {
                    self::$lastError = "TCP validation failed: Total passengers ($adjustedSum) would exceed TCP ($tcp) for group $tourCode / $movementNo.";
                    return false;
                }
            }
        }

        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }

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
            error_log("Error updating movement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculates the sum of passenger counts for a group identified by Tour Code and Request ID.
     * @param string $tourCode
     * @param int|string|null $movementNo (Request ID)
     * @return int
     */
    public static function getGroupPassengerSum($tourCode, $movementNo) {
        $sql = "SELECT SUM(passenger_count) FROM movements WHERE tour_code = ?";
        $params = [$tourCode];

        if ($movementNo === null) {
            $sql .= " AND movement_no IS NULL";
        } else {
            $sql .= " AND movement_no = ?";
            $params[] = $movementNo;
        }

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting group passenger sum: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retrieves the Target TCP for a group.
     * @param string $tourCode
     * @param int|string|null $movementNo
     * @return int|null
     */
    public static function getGroupTcp($tourCode, $movementNo) {
        // First try to get it from movements table (manually entered for the split)
        $sql = "SELECT tcp FROM movements WHERE tour_code = ?";
        $params = [$tourCode];

        if ($movementNo === null) {
            $sql .= " AND movement_no IS NULL";
        } else {
            $sql .= " AND movement_no = ?";
            $params[] = $movementNo;
        }
        
        $sql .= " AND tcp IS NOT NULL LIMIT 1";

        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            $tcp = $stmt->fetchColumn();
            
            if ($tcp !== false && $tcp !== null) {
                return (int)$tcp;
            }

            // Fallback: check if there's a linked booking_request
            $sqlFallback = "SELECT br.tcp 
                            FROM movements m
                            JOIN booking_requests br ON m.booking_request_id = br.id
                            WHERE m.tour_code = ?";
            $paramsFallback = [$tourCode];

            if ($movementNo === null) {
                $sqlFallback .= " AND m.movement_no IS NULL";
            } else {
                $sqlFallback .= " AND m.movement_no = ?";
                $paramsFallback[] = $movementNo;
            }
            
            $sqlFallback .= " LIMIT 1";

            $stmtFallback = self::$pdo->prepare($sqlFallback);
            $stmtFallback->execute($paramsFallback);
            $tcpFallback = $stmtFallback->fetchColumn();

            return ($tcpFallback !== false && $tcpFallback !== null) ? (int)$tcpFallback : null;
        } catch (PDOException $e) {
            error_log("Error getting group TCP: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validates if the sum of passengers matches the Target TCP.
     * @param string $tourCode
     * @param int|string $movementNo
     * @return bool
     */
    public static function validateTcp($tourCode, $movementNo) {
        $tcp = self::getGroupTcp($tourCode, $movementNo);
        if ($tcp === null) return true; // If no TCP is defined, we can't validate (or assume valid)

        $sum = self::getGroupPassengerSum($tourCode, $movementNo);
        return $sum === $tcp;
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
        return self::getDeadlinesByCategory('ticketing', $days);
    }

    /**
     * Get urgent deadlines by category (ticketing, dp1, dp2, fp).
     * Includes past due items.
     * @param string $category
     * @param int $days
     * @return array
     */
    public static function getDeadlinesByCategory($category, $days = 3) {
        $category = strtolower($category);
        $where = "";
        $orderBy = "";

        switch ($category) {
            case 'ticketing':
                $where = "ticketing_deadline IS NOT NULL AND ticketing_done = 0 
                          AND ticketing_deadline <= DATE_ADD(CURDATE(), INTERVAL ? DAY)";
                $orderBy = "ticketing_deadline ASC";
                break;
            case 'dp1':
                $where = "(deposit1_airlines_date IS NOT NULL OR deposit1_eemw_date IS NOT NULL) 
                          AND dp1_status != 'PAID' 
                          AND (deposit1_airlines_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                               OR deposit1_eemw_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY))";
                $orderBy = "LEAST(COALESCE(deposit1_airlines_date, '9999-12-31'), COALESCE(deposit1_eemw_date, '9999-12-31')) ASC";
                break;
            case 'dp2':
                $where = "(deposit2_airlines_date IS NOT NULL OR deposit2_eemw_date IS NOT NULL) 
                          AND dp2_status != 'PAID' 
                          AND (deposit2_airlines_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                               OR deposit2_airlines_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY))";
                $orderBy = "LEAST(COALESCE(deposit2_airlines_date, '9999-12-31'), COALESCE(deposit2_eemw_date, '9999-12-31')) ASC";
                break;
            case 'fp':
                $where = "(fullpay_airlines_date IS NOT NULL OR fullpay_eemw_date IS NOT NULL) 
                          AND fp_status != 'PAID' 
                          AND (fullpay_airlines_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) 
                               OR fullpay_eemw_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY))";
                $orderBy = "LEAST(COALESCE(fullpay_airlines_date, '9999-12-31'), COALESCE(fullpay_eemw_date, '9999-12-31')) ASC";
                break;
            default:
                return [];
        }

        $sql = "SELECT * FROM movements WHERE $where ORDER BY $orderBy";
        
        try {
            $stmt = self::$pdo->prepare($sql);
            $count = substr_count($where, '?');
            $params = array_fill(0, $count, $days);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting deadlines for $category: " . $e->getMessage());
            return [];
        }
    }
}

// Initialize the PDO instance
global $pdo;
Movement::init($pdo);
