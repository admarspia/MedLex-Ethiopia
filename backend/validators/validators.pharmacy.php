<?php

class PharmacyValidator {

    public function validateRegistration(array $data): array {
        $name = trim($data['name'] ?? '');
        $address = trim($data['address'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        
        if (empty($name)) {
            throw new Exception("Name is required");
        }
        
        if (strlen($name) < 3 || strlen($name) > 100) {
            throw new Exception("Name must be 3-100 characters");
        }
        
        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            throw new Exception("Invalid name format");
        }
        
        if (empty($address)) {
            throw new Exception("Address is required");
        }
        
        if (strlen($address) < 5) {
            throw new Exception("Invalid address");
        }
        
        if (empty($phone)) {
            throw new Exception("Phone is required");
        }
        
        if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
            throw new Exception("Invalid phone number");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email");
        }
        
        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters");
        }
        
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).+$/', $password)) {
            throw new Exception("Password must contain uppercase, lowercase, and number");
        }
        
        return [
            "name" => $name,
            "address" => $address,
            "phone" => $phone,
            "email" => $email,
            "password" => $password
        ];
    }

    public function validateUpload(array $file): void {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed");
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("File too large");
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        
        if (!in_array($mime, $allowed)) {
            throw new Exception("Invalid file type");
        }
    }
}
