<?php

require_once __DIR__ . '/../config/database.php';

class Pharmacy {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO pharmacies
        (name, address, phone, email, password_hash, license)
        VALUES (:name, :address, :phone, :email, :password_hash, :license)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($data);
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM pharmacies WHERE email = :email");
        $stmt->execute([":email" => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
