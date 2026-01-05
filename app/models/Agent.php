<?php
require_once __DIR__ . '/../../includes/db_connect.php';

class Agent {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new agent.
     * @param array $data Associative array containing agent data (name, skyagent_id, phone, email).
     * @return int|false The ID of the newly created agent, or false on failure.
     */
    public static function create(array $data) {
        $sql = "INSERT INTO agents (name, skyagent_id, phone, email) VALUES (?, ?, ?, ?)";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['skyagent_id'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null
            ]);
            return self::$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating agent: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads all agents from the database.
     * @return array An array of agent records.
     */
    public static function readAll() {
        $sql = "SELECT * FROM agents ORDER BY name ASC";
        try {
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading all agents: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Reads a single agent by its ID.
     * @param int|string $id The ID of the agent to retrieve.
     * @return array|false An associative array of the agent's data, or false if not found.
     */
    public static function readById($id) {
        $sql = "SELECT * FROM agents WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading agent by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing agent.
     * @param int|string $id The ID of the agent to update.
     * @param array $data Associative array containing the updated agent data.
     * @return bool True on success, false on failure.
     */
    public static function update($id, array $data) {
        $sql = "UPDATE agents SET name = ?, skyagent_id = ?, phone = ?, email = ? WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['skyagent_id'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error updating agent: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an agent by its ID.
     * @param int|string $id The ID of the agent to delete.
     * @return bool True on success, false on failure.
     */
    public static function delete($id) {
        $sql = "DELETE FROM agents WHERE id = ?";
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting agent: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the Agent class
Agent::init($pdo);