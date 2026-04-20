<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function getConnection() {

    try {
        $dbPath = __DIR__ . '/../database/medlex.db';

        $conn = new PDO("sqlite:" . $dbPath);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $conn->exec("
            CREATE TABLE IF NOT EXISTS pharmacies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                address TEXT,
                phone TEXT,
                email TEXT UNIQUE,
                password_hash TEXT,
                license TEXT
            );
        ");

        $conn->exec("
            CREATE TABLE IF NOT EXISTS medicines (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                generic_name TEXT,
                brand_name TEXT,
                purpose TEXT,
                usage TEXT,
                warnings TEXT,
                dosage TEXT,
                ask_a_doctor TEXT,
                stop_use TEXT,
                manufacturer TEXT
            );
        ");

        $conn->exec("
            CREATE TABLE IF NOT EXISTS pharmacy_medicines (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                pharmacy_id INTEGER,
                medicine_id INTEGER,
                count INTEGER,
                price DOUBLE,
                image_path TEXT,
                FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id),
                FOREIGN KEY (medicine_id) REFERENCES medicines(id)
            );
        ");

        return $conn;

    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
