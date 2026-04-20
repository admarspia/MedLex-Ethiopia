<?php

namespace Controllers;

use Database;
use Utils\Response;
use Utils\Validator;
use AuthMiddleware;

class MedicineController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function search() {
        $term = Validator::sanitize($this->query('name', ''));
        if (empty($term)) {
            Response::json(422, null, "Search term 'name' is required");
        }

        try {
            // Search in medicines table first
            $stmt = $this->db->prepare("SELECT * FROM medicines WHERE generic_name LIKE ? OR brand_name LIKE ?");
            $stmt->execute(["%$term%", "%$term%"]);
            $medicines = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // If not found locally, fetch from FDA (optional but good for this project)
            if (empty($medicines)) {
                $medicines = $this->fetchFromFDA($term);
            }

            // For each medicine, find pharmacies that have it
            foreach ($medicines as &$m) {
                if (!isset($m['id'])) continue; 
                
                $stmt = $this->db->prepare("
                    SELECT p.id, p.name, p.address, pm.price, pm.count, pm.image_path 
                    FROM pharmacy_medicines pm 
                    JOIN pharmacies p ON pm.pharmacy_id = p.id 
                    WHERE pm.medicine_id = ?
                ");
                $stmt->execute([$m['id']]);
                $m['pharmacies'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            Response::json(200, $medicines);
        } catch (\PDOException $e) {
            Response::json(500, null, $e->getMessage());
        }
    }

    public function list() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM medicines");
            $stmt->execute();
            $medicines = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            Response::json(200, $medicines);
        } catch (\PDOException $e) {
            Response::json(500, null, $e->getMessage());
        }
    }

    public function getById() {
        $id = (int)$this->query('id');
        if (!$id) {
            Response::json(422, null, "Valid ID is required");
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM medicines WHERE id = ?");
            $stmt->execute([$id]);
            $medicine = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$medicine) {
                Response::json(404, null, "Medicine not found");
            }

            // Include pharmacy stock info
            $stmt = $this->db->prepare("
                SELECT p.id, p.name, p.address, p.phone, pm.price, pm.count, pm.image_path 
                FROM pharmacy_medicines pm 
                JOIN pharmacies p ON pm.pharmacy_id = p.id 
                WHERE pm.medicine_id = ?
            ");
            $stmt->execute([$id]);
            $medicine['pharmacies'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::json(200, $medicine);
        } catch (\PDOException $e) {
            Response::json(500, null, $e->getMessage());
        }
    }

    public function addStock() {
        $user = AuthMiddleware::handle();
        $pharmacyEmail = $user['sub'];

        // Get pharmacy ID
        $stmt = $this->db->prepare("SELECT id FROM pharmacies WHERE email = ?");
        $stmt->execute([$pharmacyEmail]);
        $pharmacy = $stmt->fetch(\PDO::FETCH_ASSOC);
        $pharmacyId = $pharmacy['id'];

        $genericName = Validator::sanitize($this->input('generic_name', ''));
        $count = $this->input('count');
        $price = $this->input('price');

        if (empty($genericName) || !Validator::isPositive($count) || !Validator::isPositive($price)) {
            Response::json(422, null, "Valid generic_name, count, and price are required");
        }

        try {
            // Find or Create medicine in global table
            $stmt = $this->db->prepare("SELECT id FROM medicines WHERE generic_name = ?");
            $stmt->execute([$genericName]);
            $medicine = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$medicine) {
                // Fetch details from FDA to populate the table automatically
                $fdaData = $this->fetchFromFDA($genericName);
                if (!empty($fdaData)) {
                    $m = $fdaData[0];
                    $stmt = $this->db->prepare("INSERT INTO medicines (generic_name, brand_name, purpose, `usage`, warnings, dosage, stop_use, ask_a_doctor, manufacturer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$m['generic_name'], $m['brand_name'], $m['purpose'], $m['usage'], $m['warnings'], $m['dosage'], $m['stop_use'], $m['ask_a_doctor'], $m['manufacturer']]);
                    $medicineId = $this->db->lastInsertId();
                } else {
                    // Just insert the name if FDA fails
                    $stmt = $this->db->prepare("INSERT INTO medicines (generic_name) VALUES (?)");
                    $stmt->execute([$genericName]);
                    $medicineId = $this->db->lastInsertId();
                }
            } else {
                $medicineId = $medicine['id'];
            }

            // Handle Image Upload Securly
            $imagePath = null;
            if (isset($_FILES['image'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $fileValidation = Validator::validateFile($_FILES['image'], $allowedTypes, 5 * 1024 * 1024); // 5MB limit
                
                if ($fileValidation['success']) {
                    $uploadDir = "/home/as/MedLex-Ethiopia/storage/medicines/";
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $fileName = bin2hex(random_bytes(16)) . "." . $extension;
                    $imagePath = "medicines/" . $fileName; // Store relative path
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                        $imagePath = null;
                    }
                }
            }

            // Add to pharmacy_medicines
            $stmt = $this->db->prepare("INSERT INTO pharmacy_medicines (pharmacy_id, medicine_id, count, price, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$pharmacyId, $medicineId, (int)$count, (float)$price, $imagePath]);

            Response::json(201, null, "Medicine added to stock");
        } catch (\PDOException $e) {
            // Check for unique constraint violation (driver-agnostic check for simplicity or use error codes)
            if ($e->getCode() == '23000' || strpos($e->getMessage(), 'UNIQUE') !== false) {
                Response::json(409, null, "This medicine is already in your stock. Use 'Update' to change quantity or price.");
            }
            Response::json(500, null, "Error adding stock: " . $e->getMessage());
        }
    }

    public function updateStock() {
        $user = AuthMiddleware::handle();
        $pharmacyEmail = $user['sub'];

        $stmt = $this->db->prepare("SELECT id FROM pharmacies WHERE email = ?");
        $stmt->execute([$pharmacyEmail]);
        $pharmacyId = $stmt->fetchColumn();

        $medicineId = (int)$this->input('medicine_id');
        $count = $this->input('count');
        $price = $this->input('price');

        if (!$medicineId || !Validator::isPositive($count) || !Validator::isPositive($price)) {
            Response::json(422, null, "Valid medicine_id, count, and price are required");
        }

        try {
            $stmt = $this->db->prepare("UPDATE pharmacy_medicines SET count = ?, price = ? WHERE pharmacy_id = ? AND medicine_id = ?");
            $stmt->execute([(int)$count, (float)$price, $pharmacyId, $medicineId]);

            if ($stmt->rowCount() === 0) {
                Response::json(404, null, "No stock record found to update");
            }

            Response::json(200, null, "Stock updated successfully");
        } catch (\PDOException $e) {
            Response::json(500, null, "Error updating stock: " . $e->getMessage());
        }
    }

    public function removeStock() {
        $user = AuthMiddleware::handle();
        $pharmacyEmail = $user['sub'];

        $stmt = $this->db->prepare("SELECT id FROM pharmacies WHERE email = ?");
        $stmt->execute([$pharmacyEmail]);
        $pharmacyId = $stmt->fetchColumn();

        $medicineId = (int)$this->input('medicine_id');

        if (!$medicineId) {
            Response::json(422, null, "Valid medicine_id is required");
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM pharmacy_medicines WHERE pharmacy_id = ? AND medicine_id = ?");
            $stmt->execute([$pharmacyId, $medicineId]);

            if ($stmt->rowCount() === 0) {
                Response::json(404, null, "Medicine not found in your stock");
            }

            Response::json(200, null, "Medicine removed from stock");
        } catch (\PDOException $e) {
            Response::json(500, null, "Error removing stock: " . $e->getMessage());
        }
    }
    public function updateMedicine() {
        // Conceptually Admin, but available for management
        $id = (int)$this->input('id');
        $genericName = Validator::sanitize($this->input('generic_name', ''));
        $brandName = Validator::sanitize($this->input('brand_name', ''));
        $purpose = Validator::sanitize($this->input('purpose', ''));
        $manufacturer = Validator::sanitize($this->input('manufacturer', ''));

        if (!$id || empty($genericName)) {
            Response::json(422, null, "Valid ID and generic_name are required");
        }

        try {
            $stmt = $this->db->prepare("UPDATE medicines SET generic_name = ?, brand_name = ?, purpose = ?, manufacturer = ? WHERE id = ?");
            $stmt->execute([$genericName, $brandName, $purpose, $manufacturer, $id]);
            Response::json(200, null, "Global medicine details updated");
        } catch (\PDOException $e) {
            Response::json(500, null, "Error updating medicine: " . $e->getMessage());
        }
    }

    public function deleteMedicine() {
        $id = (int)$this->input('id');
        if (!$id) {
            Response::json(422, null, "Valid ID is required");
        }

        try {
            // Check if any pharmacy is using this before delete (integrity)
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM pharmacy_medicines WHERE medicine_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                Response::json(409, null, "Cannot delete: Medicine is currently in stock at one or more pharmacies.");
            }

            $stmt = $this->db->prepare("DELETE FROM medicines WHERE id = ?");
            $stmt->execute([$id]);
            Response::json(200, null, "Medicine deleted from global catalog");
        } catch (\PDOException $e) {
            Response::json(500, null, "Error deleting medicine: " . $e->getMessage());
        }
    }

    private function fetchFromFDA($name) {
        $url = "https://api.fda.gov/drug/label.json?search=openfda.generic_name:" . urlencode($name) . "&limit=1";
        $res = @file_get_contents($url);
        if ($res === false) return [];

        $data = json_decode($res, true);
        if (!isset($data['results'][0])) return [];

        $m = $data['results'][0];
        $result = [
            "generic_name" => $m['openfda']['generic_name'][0] ?? $name,
            "brand_name" => $m['openfda']['brand_name'][0] ?? null,
            "purpose" => $m['purpose'][0] ?? null,
            "usage" => $m['indications_and_usage'][0] ?? null,
            "warnings" => $m['warnings'][0] ?? null,
            "stop_use" => $m['stop_use'][0] ?? null,
            "ask_a_doctor" => $m['ask_a_doctor'][0] ?? null,
            "dosage" => $m['dosage_and_administration'][0] ?? null,
            "manufacturer" => $m['openfda']['manufacturer_name'][0] ?? null
        ];
        
        return [$result];
    }
}
