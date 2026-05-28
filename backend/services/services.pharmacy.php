<?php

## Load database connection and model
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/models.pharmacyMedicine.php';

class PharmacyService {

    ## Database connection instance
    private $conn;

    public function __construct() {
        ## Initialize database connection
        $this->conn = getConnection();
    }

    public function create($data) {
        try {

            ## Insert a new pharmacy record
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

            ## Execute insert query with provided pharmacy data
            $stmt->execute([
                ":name" => $data["name"],
                ":address" => $data["address"],
                ":phone" => $data["phone"],
                ":email" => $data["email"],
                ":password_hash" => $data["password_hash"],
                ":license" => $data["license"]
            ]);

            ## Return inserted pharmacy id
            return [
                "status" => "ok",
                "id" => $this->conn->lastInsertId()
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function findByEmail($email) {
        try {

            ## Find pharmacy by email
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

            ## Return null if pharmacy is not found
            if (!$result) {
                return null;
            }

            ## Return pharmacy data
            return [
                "status" => "ok",
                "data" => $result
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function findById($id) {
        try {

            ## Find pharmacy by id
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

            ## Return null if pharmacy is not found
            if (!$result) {
                return null;
            }

            ## Return pharmacy data
            return [
                "status" => "ok",
                "data" => $result
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function addMedicineToPharmacy(PharmacyMedicine $pharmacyMedicine) {
        try {

            ## Check if medicine already exists in pharmacy inventory
            $check = $this->conn->prepare(
                "SELECT id
                 FROM pharmacy_medicines
                 WHERE pharmacy_id = :p
                 AND medicine_id = :m"
            );

            $check->execute([
                ":p" => $pharmacyMedicine->getPharmacyId(),
                ":m" => $pharmacyMedicine->getMedicineId()
            ]);

            ## Prevent duplicate entries
            if ($check->fetch()) {
                return [
                    "status" => "ok",
                    "message" => "Already exists"
                ];
            }

            ## Insert medicine into pharmacy inventory
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
                ":pharmacy_id" => $pharmacyMedicine->getPharmacyId(),
                ":medicine_id" => $pharmacyMedicine->getMedicineId(),
                ":count" => $pharmacyMedicine->getCount(),
                ":price" => $pharmacyMedicine->getPrice(),
                ":image_path" => $pharmacyMedicine->getImagePath()
            ]);

            ## Return success response
            return [
                "status" => "ok",
                "message" => "Medicine added"
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function updatePrice($pharmacyId, $medicineId, $newPrice, $newCount = null) {
        try {

            ## Validate required input values
            if ($medicineId <= 0 || $newPrice < 0) {
                return [
                    "status" => "error",
                    "message" => "Invalid input"
                ];
            }

            ## Update both price and count if count is provided
            if ($newCount !== null) {
                $stmt = $this->conn->prepare(
                    "UPDATE pharmacy_medicines
                     SET price = :price, count = :count
                     WHERE medicine_id = :mid
                     AND pharmacy_id = :pid"
                );

                $stmt->execute([
                    ":price" => $newPrice,
                    ":count" => $newCount,
                    ":mid" => $medicineId,
                    ":pid" => $pharmacyId
                ]);

            } else {

                ## Update only price
                $stmt = $this->conn->prepare(
                    "UPDATE pharmacy_medicines
                     SET price = :price
                     WHERE medicine_id = :mid
                     AND pharmacy_id = :pid"
                );

                $stmt->execute([
                    ":price" => $newPrice,
                    ":mid" => $medicineId,
                    ":pid" => $pharmacyId
                ]);
            }

            ## Check whether any row was updated
            if ($stmt->rowCount() === 0) {
                return [
                    "status" => "error",
                    "message" => "No record updated"
                ];
            }

            ## Return success response
            return [
                "status" => "ok",
                "message" => "Stock updated"
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function removeMedicine($pharmacyId, $medicineId) {
        try {

            ## Delete medicine from pharmacy inventory
            $stmt = $this->conn->prepare(
                "DELETE FROM pharmacy_medicines
                 WHERE pharmacy_id = :p
                 AND medicine_id = :m"
            );

            $stmt->execute([
                ":p" => $pharmacyId,
                ":m" => $medicineId
            ]);

            ## Return success response
            return [
                "status" => "ok",
                "message" => "Removed"
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function getPharmaciesByMedicineId($medicine_id) {
        try {

            ## Validate medicine id
            if (!$medicine_id) {
                return [
                    "status" => "error",
                    "message" => "medicine_id is null"
                ];
            }

            ## Get all pharmacies that stock the medicine
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

            ## Return matching pharmacies
            return [
                "status" => "ok",
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => "Error: " . $e->getMessage()
            ];
        }
    }

    public function getPharmacies() {
        try {

            ## Fetch all pharmacies
            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM pharmacies"
            );

            $stmt->execute();

            ## Return all pharmacy records
            return [
                "status" => "ok",
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function getMedicines($pharmacyId) {
        try {

            ## Fetch all medicines for a given pharmacy
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

            ## Return medicine list
            return [
                "status" => "ok",
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {

            ## Return database error
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
}
