<?php

class MedicineValidator {
    public function validateImage(array $file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception("Medicine image required");
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Image upload failed");
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("Image file too large (max 5MB)");
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($mime, $allowed)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WEBP images are allowed.");
        }
    }

    public function validatePrescription(array $file) {
        $this->validateImage($file);
    }
}