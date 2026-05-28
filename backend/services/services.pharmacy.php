<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/models.pharmacyMedicine.php';

class PharmacyService {

  private $conn;

  public function __construct() {
    $this->conn = getConnection();
  }


  public function create($data) {

    try {

      $stmt = $this->conn->prepare(
        "INSERT INTO pharmacies
        (
          name,
          address,
          phone,
          email,
          password_hash,
          license
      )
      VALUES
      (
        :name,
        :address,
        :phone,
        :email,
        :password_hash,
        :license
      )"
            );


            $stmt->execute([

                ":name" =>
                    $data["name"],

                ":address" =>
                    $data["address"],

                ":phone" =>
                    $data["phone"],

                ":email" =>
                    $data["email"],

                ":password_hash" =>
                    $data["password_hash"],

                ":license" =>
                    $data["license"]
            ]);


            return [
                "status" => "ok",
                "id" => $this->conn->lastInsertId()
            ];

        } catch (PDOException $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


    public function findByEmail($email) {

        try {

            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM pharmacies
                 WHERE email = :email
                 LIMIT 1"
            );

            $stmt->execute([
                ":email" => $email
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$result) {
                return null;
            }

            return ["status"=>"ok", "data"=>$result];

        } catch (PDOException $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }

    public function findById($id) {

        try {

            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM pharmacies
                 WHERE id = :id
                 LIMIT 1"
            );

            $stmt->execute([
                ":id" => $id
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            return ["status"=>"ok", "data"=>$result];

        } catch (PDOException $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }

    public function addMedicineToPharmacy(
        PharmacyMedicine $pharmacyMedicine
    ) {

        try {

            $check = $this->conn->prepare(
                "SELECT id
                 FROM pharmacy_medicines
                 WHERE pharmacy_id = :p
                 AND medicine_id = :m"
            );


            $check->execute([

                ":p" =>
                    $pharmacyMedicine->getPharmacyId(),

                ":m" =>
                    $pharmacyMedicine->getMedicineId()
            ]);


            if ($check->fetch()) {

                return [
                    "status" => "ok",
                    "message" => "Already exists"
                ];
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO pharmacy_medicines
                (
                    pharmacy_id,
                    medicine_id,
                    count,
                    price,
                    image_path
                )
                VALUES
                (
                    :pharmacy_id,
                    :medicine_id,
                    :count,
                    :price,
                    :image_path
                )"
            );


            $stmt->execute([

                ":pharmacy_id" =>
                    $pharmacyMedicine->getPharmacyId(),

                ":medicine_id" =>
                    $pharmacyMedicine->getMedicineId(),

                ":count" =>
                    $pharmacyMedicine->getCount(),

                ":price" =>
                    $pharmacyMedicine->getPrice(),

                ":image_path" =>
                    $pharmacyMedicine->getImagePath()
            ]);


            return [
                "status" => "ok",
                "message" => "Medicine added"
            ];

        } catch (PDOException $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


    public function updatePrice($pharmacyId, $medicineId, $newPrice, $newCount = null) {

      try {

          if ($medicineId <= 0 || $newPrice < 0) {
              return [
                  "status" => "error",
                  "message" => "Invalid input"
              ];
          }

          if ($newCount !== null) {
              $stmt = $this->conn->prepare(
                  "UPDATE pharmacy_medicines
                   SET price = :price, count = :count
                   WHERE medicine_id = :mid AND pharmacy_id = :pid"
              );
              $stmt->execute([
                  ":price" => $newPrice,
                  ":count" => $newCount,
                  ":mid" => $medicineId,
                  ":pid" => $pharmacyId,
              ]);
          } else {
              $stmt = $this->conn->prepare(
                  "UPDATE pharmacy_medicines
                   SET price = :price
                   WHERE medicine_id = :mid AND pharmacy_id = :pid"
              );
              $stmt->execute([
                  ":price" => $newPrice,
                  ":mid" => $medicineId,
                  ":pid" => $pharmacyId,
              ]);
          }

          if ($stmt->rowCount() === 0) {
              return [
                  "status" => "error",
                  "message" => "No record updated"
              ];
          }

          return [
              "status" => "ok",
              "message" => "Stock updated"
          ];

      } catch (PDOException $e) {

          return [
              "status" => "error",
              "message" => $e->getMessage()
          ];
      }
  }


    public function removeMedicine(
        $pharmacyId,
        $medicineId
    ) {

        try {

            $stmt = $this->conn->prepare(
                "DELETE FROM pharmacy_medicines
                 WHERE pharmacy_id = :p
                 AND medicine_id = :m"
            );

            $stmt->execute([
                ":p" => $pharmacyId,
                ":m" => $medicineId
            ]);


            return [
              "status" => "ok",
              "message" => "Removed"
            ];

        } catch (PDOException $e) {

          return [
            "status" => "error",
            "message" => $e->getMessage()
          ];
        }
    }


    public function getPharmaciesByMedicineId($medicine_id) {
      try {

        if (!$medicine_id){
          return [
            "status" => "error",
            "message" => "medicine_id is null"
          ];
        }else {
          error_log($medicine_id);
        }
        $stmt = $this->conn->prepare(
            "SELECT 
                p.id,
                p.name,
                p.email,
                p.address,
                pm.count,
                pm.price
            FROM pharmacies p
            JOIN pharmacy_medicines pm 
                ON p.id = pm.pharmacy_id
            WHERE pm.medicine_id = :id"
        );

        $stmt->execute([
            ":id" => $medicine_id
        ]);

        return [
            "status" => "ok",
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];

    } catch (PDOException $e) {

        return [
            "status" => "error",
            "message" => "Error: " . $e->getMessage()
        ];
    }
}

    public function getPharmacies() {

        try {

            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM pharmacies"
            );

            $stmt->execute();

            return [
                "status" => "ok",
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }



    public function getMedicines($pharmacyId) {

        try {

            $stmt = $this->conn->prepare(
                "SELECT
                    m.*,
                    pm.count,
                    pm.price,
                    pm.image_path

                 FROM pharmacy_medicines pm

                 JOIN medicines m
                 ON pm.medicine_id = m.id

                 WHERE pm.pharmacy_id = :p"
            );

            $stmt->execute([
                ":p" => $pharmacyId
            ]);


            return [
                "status" => "ok",
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

}
