<?php
$username = $_POST['username'];
$password = $_POST['password'];

if($username == "admin" && $password == "1234"){
    echo "Login successful";
}else{
    echo "Invalid login";
}
//////////////////////////////
$database = new Database();
$pdo = $database->getConnection();

$auth = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

if ($action === 'register') {
    $result = $auth->register($input);
} elseif ($action === 'login') {
    $result = $auth->login($input);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

http_response_code($result['status']);
echo json_encode($result['body']);

?>
