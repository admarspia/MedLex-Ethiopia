<?php
session_start();

// Comprehensive CORS Headers
$allowedOrigins = [
    'http://localhost:5173',
    'http://localhost:3000', 
    'http://localhost:8080',
    'http://127.0.0.1:5173',
    'http://127.0.0.1:3000'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: http://localhost:5173");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/api/PharmacyAPI.php';
require_once __DIR__ . '/api/MedicineAPI.php';
require_once __DIR__ . '/api/ReservationAPI.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$pharmacy = new PharmacyAPI();
$medicine = new MedicineAPI();
$reservation = new ReservationAPI();

// Remove /backend prefix if present
$uri = preg_replace('#^/backend#', '', $uri);

// ── Pharmacy / Auth routes ────────────────────────────────────────────────
if ($uri === '/register' && $method === 'POST') {
    $pharmacy->register();

} elseif ($uri === '/login' && $method === 'POST') {
    $pharmacy->login();

} elseif ($uri === '/add-medicine' && $method === 'POST') {
    $pharmacy->addMedicine();

} elseif ($uri === '/remove-medicine' && ($method === 'GET' || $method === 'POST')) {
    $pharmacy->removeMedicine();

} elseif ($uri === '/get-medicines' && $method === 'GET') {
    $pharmacy->getMedicines();

} elseif ($uri === '/medicine' && $method === 'GET') {
    $medicine->getById($_GET['id']);

} elseif ($uri === '/get-session' && $method === 'GET') {
    $pharmacy->getSession();

} elseif ($uri === '/get-pharmacies' && $method === 'GET') {
    $pharmacy->getPharmacies();

} elseif ($uri === '/update-price' && $method === 'POST') {
    $pharmacy->updatePrice();

// ── Medicine routes ───────────────────────────────────────────────────────
} elseif ($uri === '/search-medicine' && $method === 'GET') {
    $name = $_GET['name'] ?? '';
    
    if (empty(trim($name))) {
        http_response_code(422);
        echo json_encode([
            "status" => 422,
            "success" => false,
            "data" => "Missing 'name'"
        ]);
        exit;
    }

    $medicine->searchByGenericName($name);

} elseif (preg_match('#^/medicine/(\d+)$#', $uri, $m) && $method === 'GET') {
    $medicine->getById($m[1]);

// ── Reservation routes ────────────────────────────────────────────────────
} elseif ($uri === '/reservation/create' && $method === 'POST') {
    $reservation->create();

} elseif ($uri === '/reservation/cancel' && $method === 'POST') {
    $reservation->cancel();

} elseif ($uri === '/reservation/list' && $method === 'GET') {
    $reservation->getReservations();

} elseif ($uri === '/reservation/user' && $method === 'GET') {
    $reservation->getUserReservations();

} elseif ($uri === '/reservation/notify-expiring' && $method === 'GET') {
    $reservation->notifyExpiring();

} elseif ($uri === '/reservation/auto-cancel' && $method === 'GET') {
    $reservation->autoCancelExpired();

} else {
    http_response_code(404);

    echo json_encode([
        "status" => 404,
        "success" => false,
        "data" => "Route not found: " . $uri
    ]);
}
?>
