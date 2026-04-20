<?php

namespace Controllers;

use Utils\Response;

class BaseController {
    protected function input($key = null, $default = null) {
        static $input = null;
        if ($input === null) {
            $json = file_get_contents("php://input");
            $input = json_decode($json, true) ?? [];
            $input = array_merge($input, $_POST);
        }

        if ($key === null) return $input;
        return $input[$key] ?? $default;
    }

    protected function query($key = null, $default = null) {
        if ($key === null) return $_GET;
        return $_GET[$key] ?? $default;
    }
}
