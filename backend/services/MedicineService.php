<?php

require_once __DIR__ . '/../config/database.php';

class MedicineService {

  private $conn;

  public function __construct() {
    $this->conn = getConnection();
  }

  public function search($search_term) {
    try {
      $stmt = $this->conn->prepare(
          "SELECT m.*, pm.image_path
            FROM pharmacy_medicines pm
            JOIN medicines m ON pm.medicine_id = m.id
            WHERE m.generic_name LIKE :term 
            OR m.brand_name LIKE :term"
          );

            $stmt->execute([
                ":term" => "%" . $search_term . "%"
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ["status" => "error", "message" => "not found"];
            }

            return ["status" => "ok", "data" => $result];

        } catch(PDOException $e){
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function addMedicine($m) {
        $stmt = $this->conn->prepare(
            "INSERT INTO medicines
            (generic_name, brand_name, purpose, usage, warnings,dosage, stop_use, ask_a_doctor, manufacturer)
            VALUES (:g, :b, :p, :u, :w, :d, :s, :a, :m)"
        );

        $stmt->execute([
            ":g" => $m['generic_name'],
            ":b" => $m['brand_name'],
            ":p" => $m['purpose'],
            ":u" => $m['usage'],
            ":w" => $m['warnings'],
            ":d" => $m['dosage'],
            ":s" => $m['stop_use'],
            ":a" => $m['ask_a_doctor'],
            ":m" => $m['manufacturer']
        ]);
    }

    public function searchPharmacies($medicine_id){
        try {
            $stmt = $this->conn->prepare(
                "SELECT pharmacy_id FROM pharmacy_medicines WHERE medicine_id = :id"
            );

            $stmt->execute([":id" => $medicine_id]);

            $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($result)) {
                return ["status" => "error", "message" => "no pharmacies"];
            }

            return ["status" => "ok", "data" => $result];

        } catch(PDOException $e){
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function addPharmacyMedicine($pharmacyId, $medicineId, $count, $price, $imagePath) {
      try {
        $stmt = $this->conn->prepare(
          "INSERT INTO pharmacy_medicines
          (pharmacy_id, medicine_id, count, price, image_path)
          VALUES (:p, :m, :c, :pr, :img)"
          );

          $stmt->execute([
              ":p" => $pharmacyId,
              ":m" => $medicineId,
              ":c" => $count,
              ":pr" => $price,
              ":img" => $imagePath
          ]);

          return ["status" => "ok", "message" => "Pharmacy medicine added"];

      } catch (PDOException $e) {
          return ["status" => "error", "message" => $e->getMessage()];
    }
  }
}
