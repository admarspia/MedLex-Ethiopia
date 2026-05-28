<?php

## Load required files
require_once __DIR__ . '/../services/services.pharmacy.php';
require_once __DIR__ . '/../services/services.medicine.php';
require_once __DIR__ . '/../models/models.pharmacyMedicine.php';
require_once __DIR__ . '/../validators/validators.pharmacy.php';
require_once __DIR__ . '/../validators/validator.medicine.php';
require_once __DIR__ . '/../config/jwt_helper.php';
require_once __DIR__ . '/../helpers/logger.php';

class PharmacyController {

    ## Service instances
    private $pharmacyService;
    private $medicineService;

    ## Validator instances
    private $pharmacyValidator;
    private $medicineValidator;

    ## JWT configuration
    private $jwtConfig;

    public function __construct() {
        ## Initialize services and validators
        $this->pharmacyService = new PharmacyService();
        $this->medicineService = new MedicineService();
        $this->pharmacyValidator = new PharmacyValidator();
        $this->medicineValidator = new MedicineValidator();
        $this->jwtConfig = require __DIR__ . '/../config/jwt.php';
    }

    public function register() {
        ## Allow only POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method not allowed");
        }

        try {
            ## Validate pharmacy registration input
            $data = $this->pharmacyValidator->validateRegistration($_POST);

            ## Validate uploaded pharmacy license
            $this->pharmacyValidator->validateUpload($_FILES['license']);

            ## Check if pharmacy email already exists
            $existing = $this->pharmacyService->findByEmail($data['email']);

            if ($existing['status'] === 'ok') {
                return $this->response(409, "Email already exists");
            }

            ## Save uploaded license file
            $licensePath = $this->saveFile($_FILES['license'], "licenses");

            ## Hash password before storing
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

            ## Create pharmacy account
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

            ## Store pharmacy id in session
            $_SESSION["pharmacy_id"] = $result['id'];

            ## Generate JWT token after registration
            $token = generateJWT([
                "sub" => $data['email'],
                "id" => $result['id']
            ]);

            ## Log registration success
            Helper::logger(201, "Registration successful");

            return $this->response(201, [
                "message" => "Registration successful",
                "token" => $token
            ]);

        } catch (Exception $e) {
            ## Log registration errors
            Helper::logger(422, $e->getMessage());
            return $this->response(422, $e->getMessage());
        }
    }

    public function login() {
        ## Allow only POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method not allowed");
        }

        try {
            ## Read raw JSON request body
            $raw = file_get_contents("php://input");
            $data = json_decode($raw, true);

            if (!$data) {
                throw new Exception("Invalid JSON");
            }

            ## Sanitize email and get password
            $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $data['password'] ?? '';

            ## Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email");
            }

            ## Fetch pharmacy by email
            $result = $this->pharmacyService->findByEmail($email);

            if ($result['status'] !== 'ok' || !$result['data']) {
                return $this->response(404, "User not found");
            }

            $user = $result['data'];

            ## Verify password against stored hash
            if (!password_verify($password, $user['password_hash'])) {
                return $this->response(401, "Invalid password");
            }

            ## Save pharmacy session
            $_SESSION["pharmacy_id"] = $user['id'];

            ## Generate JWT token
            $token = generateJWT([
                "sub" => $email,
                "id" => $user['id']
            ]);

            ## Log login success
            Helper::logger(200, "Login successful");

            return $this->response(200, [
                "message" => "Login successful",
                "token" => $token
            ]);

        } catch (Exception $e) {
            ## Log login errors
            Helper::logger(422, $e->getMessage());
            return $this->response(400, $e->getMessage());
        }
    }

    public function addMedicine() {
        ## Require authenticated pharmacy session
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Unauthorized");
        }

        try {
            $pharmacyId = $_SESSION["pharmacy_id"];

            ## Get medicine details from form
            $genericName = trim($_POST['generic_name'] ?? '');
            $count = intval($_POST['count'] ?? 0);
            $price = floatval($_POST['price'] ?? 0);

            ## Validate medicine inputs
            if (empty($genericName)) {
                throw new Exception("Generic name required");
            }

            if ($count <= 0) {
                throw new Exception("Invalid count");
            }

            if ($price <= 0) {
                throw new Exception("Invalid price");
            }

            ## Validate medicine image upload
            $this->medicineValidator->validateImage($_FILES['image']);

            ## Save image file
            $imagePath = $this->saveFile($_FILES['image'], "medicines");

            ## Find medicine by generic name
            $medicine = $this->medicineService->getMedicineByGenericName($genericName);

            if (!$medicine) {
                throw new Exception("Medicine not found");
            }

            $medicineId = $medicine->getId();

            ## Create pharmacy inventory record
            $pharmacyMedicine = new PharmacyMedicine();
            $pharmacyMedicine->setMedicineId($medicineId);
            $pharmacyMedicine->setPharmacyId($pharmacyId);
            $pharmacyMedicine->setCount($count);
            $pharmacyMedicine->setPrice($price);
            $pharmacyMedicine->setImagePath($imagePath);

            ## Add medicine to pharmacy inventory
            $result = $this->pharmacyService->addMedicineToPharmacy($pharmacyMedicine);

            return $this->response(201, $result);

        } catch (Exception $e) {
            ## Log medicine creation errors
            Helper::logger(422, $e->getMessage());
            return $this->response(422, $e->getMessage());
        }
    }

    public function updatePrice() {
        ## Require authenticated pharmacy session
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Unauthorized");
        }

        $pharmacyId = $_SESSION["pharmacy_id"];

        ## Read JSON body if provided
        $raw = file_get_contents("php://input");
        $jsonData = json_decode($raw, true);

        ## Support both JSON and form-data input
        $medicineId = intval($jsonData['medicine_id'] ?? $_POST['medicine_id'] ?? 0);
        $newPrice = floatval($jsonData['price'] ?? $_POST['price'] ?? -1);
        $newCount = isset($jsonData['count'])
            ? intval($jsonData['count'])
            : (isset($_POST['count']) ? intval($_POST['count']) : null);

        ## Validate input
        if ($medicineId <= 0 || $newPrice < 0) {
            return $this->response(
                400,
                "Invalid input. Price and valid medicine_id are required."
            );
        }

        ## Update medicine price and stock count
        $result = $this->pharmacyService->updatePrice(
            $pharmacyId,
            $medicineId,
            $newPrice,
            $newCount
        );

        if ($result['status'] === 'error') {
            return $this->response(500, $result['message']);
        }

        return $this->response(200, $result['message']);
    }

    public function removeMedicine() {
        ## Require authenticated pharmacy session
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Unauthorized");
        }

        $pharmacyId = $_SESSION["pharmacy_id"];

        ## Support JSON body, POST, or query parameter
        $raw = file_get_contents("php://input");
        $jsonData = json_decode($raw, true);

        $medicineId = $jsonData['medicine_id']
            ?? $_POST['medicine_id']
            ?? $_GET['id']
            ?? null;

        if (!$medicineId) {
            return $this->response(400, "Medicine ID required");
        }

        ## Remove medicine from inventory
        $result = $this->pharmacyService->removeMedicine($pharmacyId, $medicineId);

        return $this->response(200, $result);
    }

    public function getPharmacies() {
        ## Return all pharmacies
        $result = $this->pharmacyService->getPharmacies();

        return $this->response(200, $result['data']);
    }

    public function getMedicines() {
        ## Get pharmacy id from query or session
        $pharmacyId = $_GET['id'] ?? $_SESSION['pharmacy_id'] ?? null;

        if (!$pharmacyId) {
            return $this->response(400, "Pharmacy ID required");
        }

        ## Return medicines for selected pharmacy
        $result = $this->pharmacyService->getMedicines($pharmacyId);

        return $this->response(200, $result['data']);
    }

    public function getSession() {
        ## Return current session pharmacy id
        if (!isset($_SESSION["pharmacy_id"])) {
            return $this->response(401, "Not logged in");
        }

        return $this->response(200, $_SESSION["pharmacy_id"]);
    }

    private function saveFile($file, $folder) {
        ## Build upload directory path
        $uploadDir = __DIR__ . "/../uploads/" . $folder . "/";

        ## Create directory if it does not exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        ## Generate unique file name
        $fileName = uniqid() . "_" . basename($file['name']);
        $fullPath = $uploadDir . $fileName;

        ## Move uploaded file to destination
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception("File upload failed");
        }

        ## Return relative file path for storage
        return "uploads/" . $folder . "/" . $fileName;
    }

    private function response($status, $data) {
        ## Send standard JSON API response
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
