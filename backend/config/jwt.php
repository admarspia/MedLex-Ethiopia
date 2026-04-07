<?php

return [
    "secret" => "K8v!d92@xQz#P1mL8sT!wR4yU7nB9cV2hJ6k",
    "issuer" => "medlex-api"
];

?>
<?php
// backend/config/jwt.php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

require_once __DIR__ . '/../vendor/autoload.php'; // make sure firebase/php-jwt is installed

// Load config
$config = include __DIR__ . '/jwt_config.php';
$secretKey = $config['secret'];
$issuer = $config['issuer'];

// Generate JWT token
function generateJWT($payload) {
    global $secretKey, $issuer;

    $issuedAt = time();
    $expire = $issuedAt + 3600; // 1 hour

    $tokenPayload = array_merge($payload, [
        'iat' => $issuedAt,
        'exp' => $expire,
        'iss' => $issuer
    ]);

    return JWT::encode($tokenPayload, $secretKey, 'HS256');
}

// Verify JWT token
function verifyJWT($token) {
    global $secretKey;

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return (array)$decoded;
    } catch (Exception $e) {
        return false; // invalid or expired token
    }
}