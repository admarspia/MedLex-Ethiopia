<?php

require_once __DIR__ . '/../services/services.pharmacy.php';
require_once __DIR__ . '/../services/services.medicine.php';
require_once __DIR__ . '/../models/models.pharmacyMedicine.php';
require_once __DIR__ . '/../validators/validators.pharmacy.php';
require_once __DIR__ . '/../validators/validator.medicine.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../helpers/logger.php';

class PharmacyController {

    private $pharmacyService;
    private $medicineService;
    private $pharmacyValidator;
    private $medicineValidator;
    private $jwtConfig;

    public function __construct() {
        $this->pharmacyService = new PharmacyService();
        $this->medicineService = new MedicineService();
        $this->pharmacyValidator = new PharmacyValidator();
        $this->medicineValidator = new MedicineValidator();
        $this->jwtConfig = require __DIR__ . '/../config/jwt.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method not allowed");
        }
        
        try {
            $data = $this->pharmacyValidator->validateRegistration($_POST);
            $this->pharmacyValidator->validateUpload($_FILES['license']);
            
            $existing = $this->pharmacyService->findByEmail($data['email']);
            
            if ($existing['status'] === 'ok') {
                return $this->response(409, "Email already exists");
            }
            
            $licensePath = $this->saveFile($_FILES['license'], "licenses");
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $result = $this->pharmacyService->create([
                "name" => $data['name'],
                "address" => $data['address'],
                "phone" => $data['phone'],
                "email" => $data['email'],
                "password_hash" => $passwordHash,
                "license" => $licensePath
            ]);
            
            if ($result['status'] !== 'ok') {
                return $this->response(500, $result['message']);
            }
            
            $_SESSION["pharmacy_id"] = $result['id'];
            $token = generateJWT(["sub" => $data['email'], "id" => $result['id']]);
            
            
            Helper::logger(201, "Registration successful");
            
            return $this->response(201, ["message" => "Registration successful", "token" => $token]);

        } catch (Exception $e) {
            Helper::logger(422, $e.getMessage());
            return $this->response(422, $e->getMessage());
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method not allowed");
        }
        
        try {
            $raw = file_get_contents("php://input");
            $data = json_decode($raw, true);
            
            if (!$data) {
                throw new Exception("Invalid JSON");
            }
            
            $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $data['password'] ?? '';
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email");
            }
            
            $result = $this->pharmacyService->findByEmail($email);
            
            if ($result['status'] !== 'ok' || !$result['data']) {
                return $this->response(404, "User not found");
            }
            
            $user = $result['data'];
            
            if (!password_verify($password, $user['password_hash'])) {
                return $this->response(401, "Invalid password");
            }
            
            $_SESSION["pharmacy_id"] = $user['id'];
            $token = generateJWT(["sub" => $email, "id" => $user['id']]);
            Helper::logger(200, "Login successful");

            return $this->response(200, ["message" => "Login successful", "token" => $token]);
        } catch (Exception $e) {
            Helper::logger(422, $e.getMessage());
          return $this->response(400, $e->getMessage());
        }
    }

    public function addMedicine() {
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Unauthorized");
        }
        
        try {
            $pharmacyId = $_SESSION["pharmacy_id"];
            $genericName = trim($_POST['generic_name'] ?? '');
            $count = intval($_POST['count'] ?? 0);
            $price = floatval($_POST['price'] ?? 0);
            
            if (empty($genericName)) {
                throw new Exception("Generic name required");
            }
            
            if ($count <= 0) {
                throw new Exception("Invalid count");
            }
            
            if ($price <= 0) {
                throw new Exception("Invalid price");
            }
            
            $this->medicineValidator->validateImage($_FILES['image']);
            $imagePath = $this->saveFile($_FILES['image'], "medicines");
            
            $medicine = $this->medicineService->getMedicineByGenericName($genericName);
            
            if (!$medicine) {
                throw new Exception("Medicine not found");
            }
            
            $medicineId = $medicine->getId();

            $pharmacyMedicine = new PharmacyMedicine();

            $pharmacyMedicine->setMedicineId($medicineId);
            $pharmacyMedicine->setPharmacyId($pharmacyId);
            $pharmacyMedicine->setCount($count);
            $pharmacyMedicine->setPrice($price);
            $pharmacyMedicine->setImagePath($imagePath);

            
            $result = $this->pharmacyService->addMedicineToPharmacy($pharmacyMedicine);
            
            return $this->response(201, $result);
        } catch (Exception $e) {
          Helper::logger(422, $e.getMessage());
            return $this->response(422, $e->getMessage());
        }
    }

    public function updatePrice() {
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Unauthorized");
        }
        
        $pharmacyId = $_SESSION["pharmacy_id"];

        // Decode JSON body or fallback to POST variables
        $raw = file_get_contents("php://input");
        $jsonData = json_decode($raw, true);

        $medicineId = intval($jsonData['medicine_id'] ?? $_POST['medicine_id'] ?? 0);
        $newPrice = floatval($jsonData['price'] ?? $_POST['price'] ?? -1);
        $newCount = isset($jsonData['count']) ? intval($jsonData['count']) : (isset($_POST['count']) ? intval($_POST['count']) : null);

        if ($medicineId <= 0 || $newPrice < 0) {
            return $this->response(400, "Invalid input. Price and valid medicine_id are required.");
        }

        $result = $this->pharmacyService->updatePrice($pharmacyId, $medicineId, $newPrice, $newCount);
        
        if ($result['status'] === 'error') {
            return $this->response(500, $result['message']);
        }
        
        return $this->response(200, $result['message']);
    }

    public function removeMedicine() {
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Unauthorized");
        }
        
        $pharmacyId = $_SESSION["pharmacy_id"];
        
        // Support POST, GET, or raw JSON body
        $raw = file_get_contents("php://input");
        $jsonData = json_decode($raw, true);
        
        $medicineId = $jsonData['medicine_id'] ?? $_POST['medicine_id'] ?? $_GET['id'] ?? null;
        
        if (!$medicineId) {
            return $this->response(400, "Medicine ID required");
        }
        
        $result = $this->pharmacyService->removeMedicine($pharmacyId, $medicineId);
        return $this->response(200, $result);
    }

    public function getPharmacies() {
        $result = $this->pharmacyService->getPharmacies();
        return $this->response(200, $result['data']);
    }

    public function getMedicines() {
        $pharmacyId = $_GET['id'] ?? $_SESSION['pharmacy_id'] ?? null;
        
        if (!$pharmacyId) {
            return $this->response(400, "Pharmacy ID required");
        }
        
        $result = $this->pharmacyService->getMedicines($pharmacyId);
        return $this->response(200, $result['data']);
    }

    public function getSession() {
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Not logged in");
        }
        
        return $this->response(200, $_SESSION["pharmacy_id"]);
    }

    private function saveFile($file, $folder) {
        $uploadDir = __DIR__ . "/../uploads/" . $folder . "/";
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . "_" . basename($file['name']);
        $fullPath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception("File upload failed");
        }
        
        return "uploads/" . $folder . "/" . $fileName;
    }

    private function response($status, $data) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            "status" => $status,
            "success" => $status >= 200 && $status < 300,
            "data" => $data
        ]);
        exit;
    }
}
