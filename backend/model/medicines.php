<?php



header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';


$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';


if (!empty($search_term)) {
    $sql = "SELECT * FROM medicines 
            WHERE generic_name LIKE '%$search_term%' 
            OR brand_names LIKE '%$search_term%'";
} else {
    $sql = "SELECT * FROM medicines";
}

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    
    $medicines = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode([
        "status" => "success", 
        "data" => $medicines
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "No medicines found in your database."
    ]);
}

mysqli_close($conn);
?>