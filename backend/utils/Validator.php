<?php

namespace Utils;

class Validator {
    /**
     * Validate string contains only letters and spaces (Strict Name)
     */
    public static function name($name) {
        return preg_match("/^[a-zA-Z\s]+$/", $name);
    }

    /**
     * Format and Normalize Ethiopian Phone Number to +251...
     */
    public static function formatPhone($phone) {
        // Remove spaces, dashes, parentheses
        $phone = preg_replace("/[\s\-\(\)]+/", "", trim($phone));
        
        // If starts with 09... or 07..., replaces 0 with +251
        if (preg_match("/^0[79][0-9]{8}$/", $phone)) {
            return "+251" . substr($phone, 1);
        }
        
        // If starts with 9... or 7..., prepend +251
        if (preg_match("/^[79][0-9]{8}$/", $phone)) {
            return "+251" . $phone;
        }

        return $phone;
    }

    /**
     * Validate Ethiopian Phone Number (+251...)
     */
    public static function phone($phone) {
        // Matches +251 followed by 9 digits (standardized)
        return preg_match("/^\+251[79][0-9]{8}$/", $phone);
    }

    /**
     * Validate Email
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Sanitize String for output (XSS Protection)
     */
    public static function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    /**
     * Validate File Upload Securely
     */
    public static function validateFile($file, $allowedTypes = [], $maxSize = 5242880) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ["success" => false, "message" => "File upload error"];
        }

        if ($file['size'] > $maxSize) {
            return ["success" => false, "message" => "File too large (Max 5MB)"];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            return ["success" => false, "message" => "Invalid file type: $mimeType"];
        }

        return ["success" => true, "mime" => $mimeType];
    }

    /**
     * Validate Number (int/float)
     */
    public static function isPositive($value) {
        return is_numeric($value) && $value >= 0;
    }
}
