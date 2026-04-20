<?php

function get_db() {
    static $db = null;
    if ($db) return $db;

    $host = 'localhost';
    $name = 'medlex_ethiopia';
    $user = 'root';
    $pass = ''; 
    $port = '3306';
    $path = __DIR__ . '/database/medlex.db';

    try {
        // Try MySQL
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4;port=$port";
        $db = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        // Fallback to SQLite
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
        $db = new PDO("sqlite:" . $path);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $db;
}

function init_db() {
    $db = get_db();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $isSqlite = ($driver === 'sqlite');

    $auto = $isSqlite ? "INTEGER PRIMARY KEY AUTOINCREMENT" : "INT AUTO_INCREMENT PRIMARY KEY";
    $engine = $isSqlite ? "" : " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $text = $isSqlite ? "TEXT" : "VARCHAR(255)";

    $db->exec("CREATE TABLE IF NOT EXISTS pharmacies (id $auto, name $text, address TEXT, phone $text, email $text UNIQUE, password $text, license_path $text, token $text, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) $engine;");
    $db->exec("CREATE TABLE IF NOT EXISTS medicines (id $auto, generic_name $text, brand_name $text, purpose TEXT, `usage` TEXT, warnings TEXT, dosage TEXT, ask_a_doctor TEXT, stop_use TEXT, manufacturer $text) $engine;");
    $db->exec("CREATE TABLE IF NOT EXISTS pharmacy_medicines (id $auto, pharmacy_id INT, medicine_id INT, count INT DEFAULT 0, price DECIMAL(10,2), image_path $text, UNIQUE (pharmacy_id, medicine_id), FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id), FOREIGN KEY (medicine_id) REFERENCES medicines(id)) $engine;");
}
