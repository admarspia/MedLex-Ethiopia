<?php

namespace Controllers;

use Database;
use Utils\Response;
use Utils\Validator;
use AuthMiddleware;

class AuthController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register() {
        $name = Validator::sanitize($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $address = Validator::sanitize($this->input('address', ''));
        $phone = Validator::formatPhone($this->input('phone', ''));

        // Strict Validations
        if (!Validator::name($name)) {
            Response::json(422, null, "Name should only contain letters and spaces");
        }

        if (!Validator::email($email)) {
            Response::json(422, null, "Invalid email format");
        }

        if (!Validator::phone($phone)) {
            Response::json(422, null, "Invalid phone format. Use +251XXXXXXXXX");
        }

        if (strlen($password) < 6) {
            Response::json(422, null, "Password must be at least 6 characters");
        }

        // Check if email exists
        $stmt = $this->db->prepare("SELECT id FROM pharmacies WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            Response::json(409, null, "Email already registered");
        }

        // Handle License Upload Securly
        $licensePath = null;
        if (isset($_FILES['license'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $fileValidation = Validator::validateFile($_FILES['license'], $allowedTypes, 10 * 1024 * 1024); // 10MB limit

            if (!$fileValidation['success']) {
                Response::json(422, null, $fileValidation['message']);
            }

            $uploadDir = "/home/as/MedLex-Ethiopia/storage/licenses/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            // Secure renaming
            $extension = pathinfo($_FILES['license']['name'], PATHINFO_EXTENSION);
            $fileName = bin2hex(random_bytes(16)) . "." . $extension;
            $licensePath = "licenses/" . $fileName; // Strip the base path for storage in DB
            
            if (!move_uploaded_file($_FILES['license']['tmp_name'], $uploadDir . $fileName)) {
                Response::json(500, null, "Failed to save license file");
            }
        } else {
            Response::json(422, null, "Pharmacy license is required");
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
      try {
            $stmt = $this->db->prepare("INSERT INTO pharmacies (name, email, password, address, phone, license_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $passwordHash, $address, $phone, $licensePath]);
            
            $token = $this->generateToken($email);
            Response::json(201, ["token" => $token], "Registration successful");
        } catch (\PDOException $e) {
            Response::json(500, null, "Server error during registration: " . $e->getMessage());
        }
    }

    public function list() {
        try {
            $stmt = $this->db->prepare("SELECT id, name, address, phone, email, license_path FROM pharmacies");
            $stmt->execute();
            $pharmacies = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            Response::json(200, $pharmacies);
        } catch (\PDOException $e) {
            Response::json(500, null, $e->getMessage());
        }
    }

    public function login() {
        $email = filter_var(trim($this->input('email', '')), FILTER_SANITIZE_EMAIL);
        $password = $this->input('password', '');

        if (empty($email) || empty($password)) {
            Response::json(422, null, "Email and password are required");
        }

        $stmt = $this->db->prepare("SELECT * FROM pharmacies WHERE email = ?");
        $stmt->execute([$email]);
        $pharmacy = $stmt->fetch(\PDO::FETCH_ASSOC);
          


        if (!$pharmacy || !password_verify($password, $pharmacy['password'])) {
            Response::json(401, null, "Invalid email or password");
        }

        $token = $this->generateToken($email);
        Response::json(200, ["token" => $token, "pharmacy" => [
            "id" => $pharmacy["id"],
            "name" => $pharmacy["name"],
            "email" => $pharmacy["email"]
        ]], "Login successful");
    }

    private function generateToken($email) {
        $token = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare("UPDATE pharmacies SET token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);
        return $token;
    }
}
