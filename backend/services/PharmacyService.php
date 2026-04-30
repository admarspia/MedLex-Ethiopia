<?php

require_once __DIR__ . '/../config/database.php';

class PharmacyService {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }


    public function create($data) {
        try {
            $sql = "INSERT INTO pharmacies
                (name, address, phone, email, password_hash, license)
                VALUES (:name, :address, :phone, :email, :password_hash, :license)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ":name" => $data["name"],
                ":address" => $data["address"],
                ":phone" => $data["phone"],
                ":email" => $data["email"],
                ":password_hash" => $data["password_hash"],
                ":license" => $data["license"]
            ]);

            return [
                "status" => "ok",
                "id" => $this->conn->lastInsertId()
            ];

        } catch (PDOException $e) {
            echo $e;
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function findByEmail($email) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM pharmacies WHERE email = :email LIMIT 1"
            );

            $stmt->execute([":email" => $email]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ["status" => "error", "message" => "Pharmacy not found"];
            }

            return ["status" => "ok", "data" => $result];

        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    
    public function findById($id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM pharmacies WHERE id = :id LIMIT 1"
            );

            $stmt->execute([":id" => $id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ["status" => "error", "message" => "No pharmacy found"];
            }

            return ["status" => "ok", "data" => $result];

        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    
    public function addMedicine($pharmacyId, $medicineId, $imagePath) {
        try {
            
            $check = $this->conn->prepare(
                "SELECT id FROM pharmacy_medicines 
                 WHERE pharmacy_id = :p AND medicine_id = :m"
            );

            $check->execute([
                ":p" => $pharmacyId,
                ":m" => $medicineId
            ]);

            if ($check->fetch()) {
                return ["status" => "ok", "message" => "Already exists"];
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO pharmacy_medicines 
                (pharmacy_id, medicine_id, image_path)
                VALUES (:p, :m, :img)"
            );

            $stmt->execute([
                ":p" => $pharmacyId,
                ":m" => $medicineId,
                ":img" => $imagePath
            ]);

            return ["status" => "ok", "message" => "Medicine added"];

        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function removeMedicine($pharmacyId, $medicineId) {
        try {
            $stmt = $this->conn->prepare(
                "DELETE FROM pharmacy_medicines 
                 WHERE pharmacy_id = :p AND medicine_id = :m"
            );

            $stmt->execute([
                ":p" => $pharmacyId,
                ":m" => $medicineId
            ]);

            return ["status" => "ok", "message" => "Removed"];

        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }


    public function getPharmacies(){
      try {
        $stmt = $this->conn->prepare("SELECT * from pharmacies");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ["status"=>"ok", "data"=>$result];
      }catch(PDOException $e){
        return ["status"=>"error","message"=>$e->getMessage()];
      }
    }

    public function getMedicines($pharmacyId) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT m.*, pm.image_path, pm.price
                 FROM pharmacy_medicines pm
                 JOIN medicines m ON pm.medicine_id = m.id
                 WHERE pm.pharmacy_id = :p"
            );

            $stmt->execute([":p" => $pharmacyId]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ["status" => "ok", "data" => $result];

        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
