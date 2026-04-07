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
// Pharmacy-only: add medicines
elseif ($uri == '/add-medicine' && $method == 'POST') {
    AuthMiddleware::handle('pharmacy');
    $controller->addMedicine();
}

// Pharmacy-only: check missing medicines
elseif ($uri == '/missing-medicines' && $method == 'GET') {
    AuthMiddleware::handle('pharmacy');
    $controller->checkMissingMedicines();
}

// User: search medicines
elseif ($uri == '/search-medicine' && $method == 'GET') {
    AuthMiddleware::handle(); // any logged-in user
    $controller->searchMedicine();
}

// User: buy medicine
elseif ($uri == '/buy-medicine' && $method == 'POST') {
    AuthMiddleware::handle('user');
    $controller->buyMedicine();
}

// Get profile (both user & pharmacy)
elseif ($uri == '/profile' && $method == 'GET') {
    AuthMiddleware::handle();
    $controller->getProfile();
}
