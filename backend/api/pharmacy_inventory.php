<?php
// backend/api/pharmacy_inventory.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $pharmacy_id = isset($_GET['pharmacy_id']) ? intval($_GET['pharmacy_id']) : 0;
    
    if ($pharmacy_id === 0) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "pharmacy_id is required"]);
        exit;
    }

    $sql = "SELECT m.id as med_id, m.generic_name, m.brand_names, pm.stock_status, pm.price
            FROM medicines m
            JOIN pharmacy_medicines pm ON m.id = pm.medicine_id
            WHERE pm.pharmacy_id = $pharmacy_id
            ORDER BY m.generic_name ASC";
            
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $inventory = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(["status" => "success", "data" => $inventory]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database query failed"]);
    }
} 
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->pharmacy_id) || !isset($data->medicine_id) || !isset($data->stock_status) || !isset($data->price)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }
    
    $pharmacy_id = intval($data->pharmacy_id);
    $medicine_id = intval($data->medicine_id);
    $stock_status = mysqli_real_escape_string($conn, $data->stock_status);
    $price = floatval($data->price);
    
    // Check if medicine mapping already exists
    $check_sql = "SELECT id FROM pharmacy_medicines WHERE pharmacy_id = $pharmacy_id AND medicine_id = $medicine_id";
    $check_res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_res) > 0) {
        // Update
        $sql = "UPDATE pharmacy_medicines 
                SET stock_status = '$stock_status', price = $price 
                WHERE pharmacy_id = $pharmacy_id AND medicine_id = $medicine_id";
    } else {
        // Insert
        $sql = "INSERT INTO pharmacy_medicines (pharmacy_id, medicine_id, stock_status, price) 
                VALUES ($pharmacy_id, $medicine_id, '$stock_status', $price)";
    }
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Inventory updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to update inventory: " . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>
