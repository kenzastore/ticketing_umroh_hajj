<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class Corporate {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new corporate.
     * @param array $data Associative array containing corporate data (name, address).
     * @return int|false The ID of the newly created corporate, or false on failure.
     */
    public static function create(array $data) {
        $sql = "INSERT INTO corporates (name, address) VALUES (?, ?)";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['address'] ?? null
            ]);
            return self::$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating corporate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all corporates from the database.
     * @return array An array of corporate records.
     */
    public static function readAll() {
        $sql = "SELECT * FROM corporates ORDER BY name ASC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading all corporates: " . $e->getMessage());
            return [];
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
     * @return bool True on success, false on failure.
     */
    public static function update($id, array $data) {
        $sql = "UPDATE corporates SET name = ?, address = ? WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['address'] ?? null,
                $id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error updating corporate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a corporate by its ID.
     * @param int|string $id The ID of the corporate to delete.
     * @return bool True on success, false on failure.
     */
    public static function delete($id) {
        $sql = "DELETE FROM corporates WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting corporate: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the Corporate class
Corporate::init($pdo);
