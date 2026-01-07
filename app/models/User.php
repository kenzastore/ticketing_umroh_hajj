<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class User {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new user.
     * @param array $data Associative array containing user data (username, password, role_id, full_name).
     * @return int|false The ID of the newly created user, or false on failure.
     */
    public static function create(array $data) {
        $sql = "INSERT INTO users (username, password, role_id, full_name) VALUES (?, ?, ?, ?)";
        try {
            $stmt = self::$pdo->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $data['username'],
                $hashedPassword,
                $data['role_id'],
                $data['full_name']
            ]);
            return self::$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all users from the database with pagination support.
     * @param int|null $limit
     * @param int $offset
     * @return array An array of user records.
     */
    public static function readAll($limit = null, $offset = 0) {
        $sql = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.username ASC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading all users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts total users.
     * @return int
     */
    public static function countAll() {
        try {
            return (int)self::$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Reads a single user by its ID.
     * @param int $id The ID of the user to retrieve.
     * @return array|false An associative array of the user's data, or false if not found.
     */
    public static function readById(int $id) {
        $sql = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading user by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing user.
     * @param int $id The ID of the user to update.
     * @param array $data Associative array containing the updated user data.
     *                    Includes optional 'password' key for password change.
     * @return bool True on success, false on failure.
     */
    public static function update(int $id, array $data) {
        $fields = [];
        $params = [];

        if (isset($data['username'])) {
            $fields[] = 'username = ?';
            $params[] = $data['username'];
        }
        if (isset($data['role_id'])) {
            $fields[] = 'role_id = ?';
            $params[] = $data['role_id'];
        }
        if (isset($data['full_name'])) {
            $fields[] = 'full_name = ?';
            $params[] = $data['full_name'];
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return false; // Nothing to update
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;

        try {
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a user by its ID.
     * @param int $id The ID of the user to delete.
     * @return bool True on success, false on failure.
     */
    public static function delete(int $id) {
        $sql = "DELETE FROM users WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the User class
User::init($pdo);
