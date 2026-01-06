<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/AuditLog.php';

class Agent {
    private static $pdo;

    public static function init(PDO $pdo_instance) {
        self::$pdo = $pdo_instance;
    }

    /**
     * Creates a new agent.
     * @param array $data Associative array containing agent data (name, skyagent_id, phone, email).
     * @param int|null $userId ID of the user performing the action.
     * @return int|false The ID of the newly created agent, or false on failure.
     */
    public static function create(array $data, $userId = null) {
        $sql = "INSERT INTO agents (name, skyagent_id, phone, email) VALUES (?, ?, ?, ?)";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['skyagent_id'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null
            ]);
            $agentId = $db->lastInsertId();
            
            // Audit Log
            $newAgent = self::readById($agentId);
            AuditLog::log($userId, 'CREATE', 'agent', $agentId, null, json_encode($newAgent));

            if (!$inTransaction) $db->commit();
            return $agentId;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
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
     * @param int|null $userId ID of the user performing the action.
     * @return bool True on success, false on failure.
     */
    public static function update($id, array $data, $userId = null) {
        $oldAgent = self::readById($id);
        if (!$oldAgent) return false;

        $sql = "UPDATE agents SET name = ?, skyagent_id = ?, phone = ?, email = ? WHERE id = ?";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['skyagent_id'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $id
            ]);

            // Audit Log
            $newAgent = self::readById($id);
            AuditLog::log($userId, 'UPDATE', 'agent', $id, json_encode($oldAgent), json_encode($newAgent));

            if (!$inTransaction) $db->commit();
            return true;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error updating agent: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an agent by its ID.
     * @param int|string $id The ID of the agent to delete.
     * @param int|null $userId ID of the user performing the action.
     * @return bool True on success, false on failure.
     */
    public static function delete($id, $userId = null) {
        $oldAgent = self::readById($id);
        if (!$oldAgent) return false;

        $sql = "DELETE FROM agents WHERE id = ?";
        try {
            $db = self::$pdo;
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) $db->beginTransaction();

            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);

            // Audit Log
            AuditLog::log($userId, 'DELETE', 'agent', $id, json_encode($oldAgent), null);

            if (!$inTransaction) $db->commit();
            return true;
        } catch (PDOException $e) {
            if (self::$pdo->inTransaction()) self::$pdo->rollBack();
            error_log("Error deleting agent: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize the PDO instance for the Agent class
global $pdo;
Agent::init($pdo);