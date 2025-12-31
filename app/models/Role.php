<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class Role {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Reads all roles from the database.
     * @return array An array of role records.
     */
    public static function readAll() {
        $sql = "SELECT * FROM roles ORDER BY name ASC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading all roles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reads a single role by its ID.
     * @param int $id The ID of the role to retrieve.
     * @return array|false An associative array of the role's data, or false if not found.
     */
    public static function readById(int $id) {
        $sql = "SELECT * FROM roles WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading role by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads a single role by its name.
     * @param string $name The name of the role to retrieve.
     * @return array|false An associative array of the role's data, or false if not found.
     */
    public static function readByName(string $name) {
        $sql = "SELECT * FROM roles WHERE name = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading role by name: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the Role class
Role::init($pdo);
