<?php

require_once __DIR__ . '/../model/pharmacies.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

class PharmacyAPI {

    private $model;
    private $jwtConfig;

    public function __construct() {
        $this->model = new Pharmacy();
        $this->jwtConfig = require __DIR__ . '/../config/jwt.php';
    }

    public function register() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method Not Allowed");
        }

        $name     = trim($_POST['name'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (strlen($name) < 3) return $this->response(422, "Invalid name");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->response(422, "Invalid email");
        if (strlen($password) < 6) return $this->response(422, "Weak password");

        if ($this->model->findByEmail($email)) {
            return $this->response(409, "Email already exists");
        }

        $uploadDir = __DIR__ . '/../uploads/licenses/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = uniqid() . "_" . basename($_FILES['license']['name']);
        move_uploaded_file($_FILES['license']['tmp_name'], $uploadDir . $fileName);

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->model->create([
            ":name" => $name,
            ":address" => $address,
            ":phone" => $phone,
            ":email" => $email,
            ":password_hash" => $passwordHash,
            ":license" => "uploads/licenses/" . $fileName
        ]);

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

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->model->findByEmail($email);

        if (!$user) {
            return $this->response(404, "User not found");
        }

        if (!password_verify($password, $user['password_hash'])) {
            return $this->response(401, "Invalid password");
        }

        $token = $this->generateJWT($email);

        return $this->response(200, [
            "message" => "Login successful",
            "token" => $token
        ]);
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

    private function response($status, $message) {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode([
            "status" => $status,
            "data" => $message
        ]);
        exit;
    }
}
