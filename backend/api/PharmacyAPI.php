<?php

require_once __DIR__ . '/../services/PharmacyService.php';
require_once __DIR__ . '/../services/MedicineService.php';
require_once __DIR__ . '/MedicineAPI.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

class PharmacyAPI {

  private $pharmacyService;
  private $medicineService;
  private $jwtConfig;

  public function __construct() {
    $this->pharmacyService = new PharmacyService();
    $this->medicineService = new MedicineService();
    $this->medicineAPI = new MedicineAPI();
    $this->jwtConfig = require __DIR__ . '/../config/jwt.php';
  }

  public function register() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->response(405, "Method Not Allowed");
    }

    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $phone   = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password= $_POST['password'] ?? '';

    if (strlen($name) < 3) return $this->response(422, "Invalid name");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->response(422, "Invalid email");
    if (strlen($password) < 6) return $this->response(422, "Password must be ≥ 6 chars");

    $existing = $this->pharmacyService->findByEmail($email);
    if ($existing["status"] === "ok") {
      return $this->response(409, "Email already exists");
    }

    if (!isset($_FILES['license']) || $_FILES['license']['error'] !== 0) {
      return $this->response(422, "License file required");
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

    if (!in_array($_FILES['license']['type'], $allowedTypes)) {
      return $this->response(422, "Invalid license format (PDF/JPG/PNG only)");
    }

    if ($_FILES['license']['size'] > 5 * 1024 * 1024) {
      return $this->response(422, "License max size is 5MB");
    }

    $uploadDir = __DIR__ . '/../uploads/licenses/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = uniqid() . "_" . basename($_FILES['license']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['license']['tmp_name'], $filePath)) {
      return $this->response(500, "File upload failed");
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $create = $this->pharmacyService->create([
      "name" => $name,
      "address" => $address,
      "phone" => $phone,
      "email" => $email,
      "password_hash" => $passwordHash,
      "license" => "uploads/licenses/" . $fileName
    ]);

    if ($create["status"] !== "ok") {
      return $this->response(500, $create["message"]);
    }

    $token = $this->generateJWT($email);

    return $this->response(201, [
      "message" => "Pharmacy registered",
      "token" => $token
    ]);
  }

  public function login() {


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->response(405, "Method Not Allowed");
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $data['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return $this->response(400, "Invalid email");
    }

    $result = $this->pharmacyService->findByEmail($email);

    if ($result["status"] !== "ok") {
      return $this->response(404, "User not found");
    }

    $user = $result["data"];

    if (!password_verify($password, $user['password_hash'])) {
      return $this->response(401, "Invalid password");
    }

    $_SESSION["pharmacy_id"] = $user["id"];
    error_log($_SESSION["pharmacy_id"]. " ".session_id());

    $token = $this->generateJWT($email);

    return $this->response(200, [
      "message" => "Login successful",
      "token" => $token
    ]);
  }

  public function addMedicine() {

    if (!isset($_SESSION["pharmacy_id"])) {
      return $this->response(401, "Unauthorized");
    }

    $pharmacyId = $_SESSION["pharmacy_id"];

    $genericName = htmlspecialchars(trim($_POST['generic_name'] ?? ''));
    $count = intval($_POST['count'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);

    if (empty($genericName)) {
      return $this->response(400, "Medicine name required");
    }

    if ($count <= 0) {
      return $this->response(400, "Invalid count");
    }

    if ($price <= 0) {
      return $this->response(400, "Invalid price");
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
      return $this->response(400, "Image required");
    }

    $allowedTypes = ['image/jpeg', 'image/png'];

    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
      return $this->response(400, "Only JPG/PNG allowed");
    }

    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
      return $this->response(400, "Image max size is 5MB");
    }

    $uploadDir = __DIR__ . '/../uploads/medicines/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
      return $this->response(500, "Upload failed");
    }

    try {
      // STEP 1: find or fetch medicine
      $result = $this->medicineAPI->getByGenericName($genericName);

      $medicine = $result["data"]["medicine"];

      if (!$medicine['generic_name']){
        return $this->response(400, $result["message"]);
      }

      $this->medicineService->addPharmacyMedicine(
        $pharmacyId,
        $medicineId,
        $count,
        $price,
        "uploads/medicines/" . $fileName
      );

      return $this->response(200, "Medicine added to pharmacy");

    } catch (Exception $e) {
      return $this->response(500, $e->getMessage());
    }
  }


  public function removeMedicine() {


    if (!isset($_SESSION["pharmacy_id"])) {
      return $this->response(401, "Unauthorized");
    }

    $pharmacyId = $_SESSION["pharmacy_id"];
    $medicineId = $_POST['id'] ?? '';

    $result = $this->pharmacyService->removeMedicine($pharmacyId, $medicineId);

    return $this->response(200, $result);
  }

  private function generateJWT($email) {

    $issuedAt = time();
    $expire = $issuedAt + 3600;

    $payload = [
      "iss" => $this->jwtConfig["issuer"],
      "iat" => $issuedAt,
      "exp" => $expire,
      "sub" => $email
    ];

    return JWT::encode($payload, $this->jwtConfig["secret"], 'HS256');
  }

  public function getPharmacies(){
    $result = $this->pharmacyService->getPharmacies();
    if ($result["status"] === "error"){
      return $this->response(404, $result["message"]);
    } 
    return $this->response(200, $result["data"]);
  }

  public function getMedicines(){
    $pharmacyId = $_GET["id"];
    if (empty($pharmacyId)){
      return $this->response(404, "Phamacy ID is required");
    }

    $result = $this->pharmacyService->getMedicines($pharmacyId);

    if ($result["status"] === "ok" && count($result["data"]) > 0){
      return $this->response(200, $result["data"]);
    }else{
      return $this->response(404, "Phamacy not found");
    }
  }

  public function getSession() {
    error_log("SESSION ID: " . session_id());
    error_log("SESSION DATA: " . json_encode($_SESSION));

    if (!isset($_SESSION["pharmacy_id"])) {
        return $this->response(400, "Not logged in");
    }

    return $this->response(200, $_SESSION["pharmacy_id"]);
  }

  private function response($status, $data) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode([
              "status" => $status,
              "data" => $data
          ]);
          exit;
      }
}
