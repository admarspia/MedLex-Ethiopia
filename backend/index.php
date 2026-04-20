<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// 100% Native PHP - No Vendor / Composer or JWT libraries
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/utils/Validator.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/middleware/authMiddleware.php';
require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/MedicineController.php';
require_once __DIR__ . '/controllers/PharmacyController.php';
require_once __DIR__ . '/controllers/StorageController.php';

use Controllers\AuthController;
use Controllers\MedicineController;
use Controllers\PharmacyController;
use Controllers\StorageController;
use Utils\Response;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Strip /backend if present in the URI
$uri = str_replace('/backend', '', $uri);
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController();
$medicine = new MedicineController();
$pharmacyAction = new PharmacyController();
$storage = new StorageController();

try {
    // Route matching
    if ($uri === '/uploads' && $method === 'GET') {
        $storage->serve();
    } elseif ($uri === '/register' && $method === 'POST') {
        $auth->register();
    } elseif ($uri === '/login' && $method === 'POST') {
        $auth->login();
    } elseif ($uri === '/pharmacies' && $method === 'GET') {
        $auth->list();
    } elseif ($uri === '/medicines' && $method === 'GET') {
        $medicine->list();
    } elseif ($uri === '/medicine-details' && $method === 'GET') {
        $medicine->getById();
    } elseif ($uri === '/update-medicine' && $method === 'POST') {
        $medicine->updateMedicine();
    } elseif ($uri === '/delete-medicine' && $method === 'POST') {
        $medicine->deleteMedicine();
    } elseif ($uri === '/search-medicine' && $method === 'GET') {
        $medicine->search();
    } elseif ($uri === '/add-medicine' && $method === 'POST') {
        $medicine->addStock();
    } elseif ($uri === '/update-stock' && $method === 'POST') {
        $medicine->updateStock();
    } elseif ($uri === '/remove-medicine' && $method === 'POST') {
        $medicine->removeStock();
    } elseif ($uri === '/pharmacy-inventory' && $method === 'GET') {
        $pharmacyAction->getInventory();
    } elseif ($uri === '/profile' && $method === 'GET') {
        $user = AuthMiddleware::handle();
        Response::json(200, ["user" => $user], "Profile fetched");
    } else {
        Response::json(404, null, "Route not found: " . $uri);
    }
} catch (Exception $e) {
    Response::json(500, null, "Server Error: " . $e->getMessage());
}
