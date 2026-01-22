<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

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
            $newId = self::$pdo->lastInsertId();
            if ($newId) {
                $logData = $data;
                unset($logData['password']);
                AuditLog::log(null, 'USER_CREATED', 'user', $newId, null, $logData);
            }
            return $newId;
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
        $oldUser = self::readById($id);
        if (!$oldUser) return false;
        unset($oldUser['password']);

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
            $success = $stmt->execute($params);
            if ($success) {
                $newUser = self::readById($id);
                unset($newUser['password']);
                AuditLog::log(null, 'USER_UPDATED', 'user', $id, $oldUser, $newUser);
            }
            return $success;
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
        $oldUser = self::readById($id);
        if ($oldUser) {
            unset($oldUser['password']);
        }
        $sql = "DELETE FROM users WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            $success = $stmt->rowCount() > 0;
            if ($success && $oldUser) {
                AuditLog::log(null, 'USER_DELETED', 'user', $id, $oldUser, null);
            }
            return $success;
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the User class
global $pdo;
User::init($pdo);
