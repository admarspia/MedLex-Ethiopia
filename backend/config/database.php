<?php

use Utils\Response;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Simple Config - Hardcoded for maximum portability
        $host = 'localhost';
        $db   = 'medlex_ethiopia';
        $user = 'root';
        $pass = ''; 
        $port = '3306';
        $dbPath = __DIR__ . '/../database/medlex.db';

        try {
            // ATTEMPT MYSQL
            try {
                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4;port=$port";
                $this->conn = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                // FALLBACK TO SQLITE if MySQL fails
                if (!is_dir(dirname($dbPath))) mkdir(dirname($dbPath), 0777, true);
                $this->conn = new PDO("sqlite:" . $dbPath);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            $this->initTables();
        } catch (PDOException $e) {
             Response::json(500, null, "Database Initialization Failed: " . $e->getMessage());
             exit;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    private function initTables() {
        $driver = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        $isSqlite = ($driver === 'sqlite');

        $autoIncrement = $isSqlite ? "INTEGER PRIMARY KEY AUTOINCREMENT" : "INT AUTO_INCREMENT PRIMARY KEY";
        $engine = $isSqlite ? "" : " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $textType = $isSqlite ? "TEXT" : "VARCHAR(255)";
        $shortText = $isSqlite ? "TEXT" : "VARCHAR(50)";

        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS pharmacies (
                id $autoIncrement,
                name $textType,
                address TEXT,
                phone $shortText,
                email $textType UNIQUE,
                password $textType,
                license_path $textType,
                token $textType,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) $engine;
        ");

        // Use backticks for 'usage' as it's a reserved keyword in some SQL dialects
        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS medicines (
                id $autoIncrement,
                generic_name $textType,
                brand_name $textType,
                purpose TEXT,
                `usage` TEXT,
                warnings TEXT,
                dosage TEXT,
                ask_a_doctor TEXT,
                stop_use TEXT,
                manufacturer $textType
            ) $engine;
        ");

        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS pharmacy_medicines (
                id $autoIncrement,
                pharmacy_id INT,
                medicine_id INT,
                count INT DEFAULT 0,
                price DECIMAL(10,2),
                image_path $textType,
                UNIQUE (pharmacy_id, medicine_id),
                FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id),
                FOREIGN KEY (medicine_id) REFERENCES medicines(id)
            ) $engine;
        ");
    }
}

// For backward compatibility if needed, but we should use Database::getInstance()->getConnection()
function getConnection() {
    return Database::getInstance()->getConnection();
}
