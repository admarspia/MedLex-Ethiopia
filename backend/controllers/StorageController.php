<?php

namespace Controllers;

use Utils\Response;
use AuthMiddleware;

class StorageController extends BaseController {
    private $storagePath = "/home/as/MedLex-Ethiopia/storage/";

    public function serve() {
        // Securely serve files. Requires authentication for licenses. 
        // Medicines might be public, but we'll use this endpoint for control.
        $path = $this->query('path');
        if (empty($path)) {
            Response::json(400, null, "File path is required");
        }

        // Prevention of Directory Traversal
        $realBase = realpath($this->storagePath);
        $fullPath = realpath($this->storagePath . $path);

        if (!$fullPath || strpos($fullPath, $realBase) !== 0) {
            Response::json(403, null, "Access denied or file not found");
        }

        if (!is_file($fullPath)) {
            Response::json(404, null, "File not found");
        }

        // Require auth for licenses
        if (strpos($path, 'licenses/') === 0) {
            AuthMiddleware::handle();
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fullPath);

        header("Content-Type: $mimeType");
        header("Content-Length: " . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}
