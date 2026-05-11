<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/jwt.php';

$jwtConfig = include __DIR__ . '/jwt.php';
$secretKey = $jwtConfig['secret'];
$issuer = $jwtConfig['issuer'];

function generateJWT($payload) {
    global $secretKey, $issuer;
    
    $issuedAt = time();
    $expire = $issuedAt + 3600;
    
    $tokenPayload = array_merge($payload, [
        'iat' => $issuedAt,
        'exp' => $expire,
        'iss' => $issuer
    ]);
    
    return JWT::encode($tokenPayload, $secretKey, 'HS256');
}

function verifyJWT($token) {
    global $secretKey;
    
    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return (array)$decoded;
    } catch (Exception $e) {
        return false;
    }
}
