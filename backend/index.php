<?php
session_start();
error_log(session_id());

/*
|--------------------------------------------------------------------------
| CORS Headers
|--------------------------------------------------------------------------
| These headers allow the frontend application
| (running on localhost:5173) to communicate with this backend API.
|
| Access-Control-Allow-Origin:
|   Allows requests from the frontend URL.
|
| Access-Control-Allow-Credentials:
|   Allows cookies/sessions to be sent with requests.
|
| Access-Control-Allow-Headers:
|   Specifies allowed request headers.
|
| Access-Control-Allow-Methods:
|   Specifies allowed HTTP methods.
|--------------------------------------------------------------------------
*/


header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/*
|--------------------------------------------------------------------------
| Include API Classes
|--------------------------------------------------------------------------
| These files contain the business logic for:
| - Pharmacy operations
| - Medicine operations
| - Reservation operations
|--------------------------------------------------------------------------
*/


require_once __DIR__ . '/api/PharmacyAPI.php';
require_once __DIR__ . '/api/MedicineAPI.php';
require_once __DIR__ . '/api/ReservationAPI.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$pharmacy = new PharmacyAPI();
$medicine = new MedicineAPI();
$reservation = new ReservationAPI();

/*
|--------------------------------------------------------------------------
| Routing System
|--------------------------------------------------------------------------
| This section checks the requested route and method,
| then calls the appropriate API function.
|--------------------------------------------------------------------------
*/


if ($uri === '/register' && $method === 'POST') {
    $pharmacy->register();
} elseif ($uri === '/login' && $method === 'POST') {
    $pharmacy->login();
} elseif ($uri === '/add-medicine' && $method === 'POST') {
    $pharmacy->addMedicine();
} elseif ($uri === '/remove-medicine' && $method === 'GET') {
    $pharmacy->removeMedicine();
} elseif ($uri === '/get-medicines' && $method === 'GET') {
    $pharmacy->getMedicines();
} elseif ($uri === '/get-session' && $method === 'GET') {
    $pharmacy->getSession();
} elseif ($uri === '/get-pharmacies' && $method === 'GET') {
    $pharmacy->getpharmacies();
}
elseif ($uri === '/update-price' && $method === 'POST') {
    $pharmacy->updatePrice();
} elseif ($uri === '/search-medicine' && $method === 'GET') {
    $name = $_GET['name'] ?? '';
    if (empty($name)) {
        http_response_code(422);
        echo json_encode(["status" => 422, "data" => "Missing 'name'", "name" => $name]);
        exit;
    }
    $medicine->searchByGenericName($name);
} elseif ($uri === '/medicine' && $method === 'GET'){
    $medicine->getById($_GET['id']);

} elseif ($uri === '/reservation/create' && $method === 'POST') {
    $reservation->create();
} elseif ($uri === '/reservation/cancel' && $method === 'POST') {
    $reservation->cancel();
} elseif ($uri === '/reservation/notify-expiring' && $method === 'GET') {
    $reservation->notifyExpiringReservations();
} elseif ($uri === '/profile' && $method === 'GET') {
    echo json_encode(["message" => "Profile endpoint not implemented"]);
} else {
    http_response_code(404);
    echo json_encode(["status" => 404, "data" => "Route not found: " . $uri]);
}
