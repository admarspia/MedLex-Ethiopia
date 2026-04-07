<?php
// backend/api/auth.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // adjust for your frontend domain in production
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/database.php"; // your PDO connection
require_once "../config/jwt.php";      // the helper above

$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$email = htmlspecialchars(trim($data['email']));
$password = htmlspecialchars(trim($data['password']));

try {
    // Use prepared statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    // Verify hashed password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    // Generate JWT token
    $payload = [
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    $token = generateJWT($payload);

    echo json_encode(['success' => true, 'token' => $token]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}