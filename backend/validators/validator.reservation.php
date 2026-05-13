<?php

class ReservationValidator {

    public function validate(array $data): array {
        $email = trim($data['reserver_email'] ?? '');
        $generic = trim($data['generic_name'] ?? '');
        $quantity = intval($data['quantity'] ?? 0);
        
        $errors = [];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
        }
        
        if (strlen($generic) < 2) {
            $errors[] = "Invalid generic name";
        }
        
        if ($quantity <= 0 || $quantity > 1000) {
            $errors[] = "Invalid quantity";
        }
        
        if (!empty($errors)) {
            throw new Exception(json_encode($errors));
        }
        
        return [
            "reserver_email" => $email,
            "generic_name" => $generic,
            "quantity" => $quantity
        ];
    }

    public function validatePrescription(array $file): void {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Prescription file required");
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("File too large (max 5MB)");
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (!in_array($mime, $allowed)) {
            throw new Exception("Invalid file type");
        }
    }
}
