<?php

namespace Utils;

class Response {
    public static function json($status, $data = null, $message = null) {
        http_response_code($status);
        header('Content-Type: application/json');

        $response = [
            "success" => $status >= 200 && $status < 300,
            "status" => $status
        ];

        if ($data !== null) {
            $response["data"] = $data;
        }

        if ($message !== null) {
            $response["message"] = $message;
        }

        echo json_encode($response);
        exit;
    }
}
