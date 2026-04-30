<?php
session_start();
error_log(session_id());
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


require_once __DIR__ . '/api/PharmacyAPI.php';
require_once __DIR__ . '/api/MedicineAPI.php';
require_once __DIR__ . '/middleware/authMiddleware.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$pharmacy = new PharmacyAPI();
$medicine = new MedicineAPI();


if ($uri === '/register' && $method === 'POST') {
    $pharmacy->register();
}

elseif ($uri === '/login' && $method === 'POST') {
    $pharmacy->login();
}

elseif ($uri === '/add-medicine' && $method === 'POST') {
    /* AuthMiddleware::handle('pharmacy'); */
    $pharmacy->addMedicine();
}

elseif ($uri === '/remove-medicine' && $method === 'POST') {
    /* AuthMiddleware::handle('pharmacy'); */
    $pharmacy->removeMedicine();
}

elseif ($uri === '/get-medicines' && $method === 'GET') {
    $pharmacy->getMedicines();
}

elseif ($uri === '/get-session' && $method === 'GET') {
    $pharmacy->getSession();
}


elseif ($uri === '/get-pharmacies' && $method === 'GET') {
    $pharmacy->getPharmacies();
}



elseif ($uri === '/search-medicine' && $method === 'GET') {

    $name = $_GET['name'] ?? '';

    if (empty($name)) {
        http_response_code(422);
        echo json_encode(["status" => 422, "data" => "Missing 'name'", "name" => $name]);
        exit;
    }

    $medicine->searchByGenericName($name);
}

elseif ($uri === '/profile' && $method === 'GET') {
    AuthMiddleware::handle(); 
    echo json_encode(["message" => "Profile endpoint not implemented"]);
}

else {
    http_response_code(404);
    echo json_encode([
        "status" => 404,
        "data" => "Route not found" . $uri
    ]);
}
