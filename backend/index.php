<?php

require_once __DIR__ . '/api/Pharmacy.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$controller = new PharmacyAPI();

// ROUTES
if ($uri == '/register' && $method == 'POST') {
    $controller->register();
}
elseif ($uri == '/login' && $method == 'POST') {
    $controller->login();
}
else {
    http_response_code(404);
    echo json_encode(["message" => "Route not found"]);
}
