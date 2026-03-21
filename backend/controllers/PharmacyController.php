<?php
require_once __DIR__ . '/../models/Pharmacy.php';

class PharmacyController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response(405, "Method Not Allowed");
        }

        // Sanitize input
        $name       = trim($_POST['name'] ?? '');
        $address    = trim($_POST['address'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $latitude   = trim($_POST['latitude'] ?? '');
        $longitude  = trim($_POST['longitude'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $password   = $_POST['password'] ?? '';

        $errors = [];

        // Validation
        if (empty($name) || strlen($name) < 3) {
            $errors[] = "Pharmacy name must be at least 3 characters.";
        }

        if (empty($address)) {
            $errors[] = "Address is required.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (!preg_match('/^[0-9]{9,15}$/', $phone)) {
            $errors[] = "Invalid phone number.";
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            $errors[] = "Invalid coordinates.";
        }

        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }

        // License upload validation
        if (!isset($_FILES['license']) || $_FILES['license']['error'] !== 0) {
            $errors[] = "License file is required.";
        }

        if (!empty($errors)) {
            $this->response(422, $errors);
        }

        // Handle file upload
        $uploadDir = __DIR__ . '/../uploads/licenses/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES['license']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($_FILES['license']['type'], $allowedTypes)) {
            $this->response(422, "Only PDF or image files allowed for license.");
        }

        move_uploaded_file($_FILES['license']['tmp_name'], $filePath);

        // Create Pharmacy Object
        $pharmacy = new Pharmacy();
        $pharmacy->setName(htmlspecialchars($name));
        $pharmacy->setAddress(htmlspecialchars($address));
        $pharmacy->setPhone($phone);
        $pharmacy->setLatitude($latitude);
        $pharmacy->setLongitude($longitude);
        $pharmacy->setEmail($email);
        $pharmacy->setPasswordHash(password_hash($password, PASSWORD_BCRYPT));
        $pharmacy->setLicsence("uploads/licenses/" . $fileName);

        //Save to database (call repository)

        $this->response(201, "Pharmacy registered successfully.");
    }

    private function response($status, $message)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            "status" => $status,
            "message" => $message
        ]);
        exit;
    }
}
