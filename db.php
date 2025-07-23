<?php
require 'vendor/autoload.php';
require 'config.php';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct($host, $username, $password, $dbname) {
        $this->connection = new mysqli($host, $username, $password, $dbname);
        
        if ($this->connection->connect_error) {
            throw new Exception("Database connection failed: " . $this->connection->connect_error);
        }
        
        $this->connection->set_charset('utf8mb4');
    }

    public static function getInstance() {
        global $dbHost, $dbUsername, $dbPassword, $dbName;
        
        if (!self::$instance) {
            self::$instance = new self($dbHost, $dbUsername, $dbPassword, $dbName);
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        return $stmt;
    }

    public function fetchAssoc($stmt) {
        return $stmt->get_result()->fetch_assoc();
    }

    public function fetchAll($stmt) {
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function __destruct() {
        $this->close();
    }
}

function generateSecureString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Singleton database instance
function DB() {
    return Database::getInstance();
}