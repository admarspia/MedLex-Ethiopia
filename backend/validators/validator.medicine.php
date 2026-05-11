<?php

class MedicineValidator {
    public function validatePrescription(array $file) {
        if (!isset($file['tmp_name'])) {
            throw new Exception("Prescription required");
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed");
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("File too large");
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