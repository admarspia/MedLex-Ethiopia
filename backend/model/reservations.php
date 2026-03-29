<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $medicine_id = mysqli_real_escape_string($conn, $_POST['medicine_id']);
    $pharmacy_id = mysqli_real_escape_string($conn, $_POST['pharmacy_id']);

    
    if (!empty($user_id) && !empty($medicine_id) && !empty($pharmacy_id)) {
        
        
        $sql = "INSERT INTO reservations (user_id, medicine_id, pharmacy_id, status) 
                VALUES ('$user_id', '$medicine_id', '$pharmacy_id', 'pending')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "status" => "success", 
                "message" => "Great! The medicine has been reserved."
            ]);
        } else {
            echo json_encode([
                "status" => "error", 
                "message" => "Database error: " . mysqli_error($conn)
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Please provide the user, medicine, and pharmacy IDs."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "This file only accepts POST requests."
    ]);
}

mysqli_close($conn);
?>