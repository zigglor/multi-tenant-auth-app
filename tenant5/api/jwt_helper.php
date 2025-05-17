<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generate_jwt($userId, $role, $tenantId, $key, $issuer) {
    $payload = [
        "iss" => $issuer,
        "iat" => time(),
        "exp" => time() + 900, // 15 mins
        "sub" => $userId,
        "role" => $role,
        "tenant_id" => $tenantId
    ];
    return JWT::encode($payload, $key, 'HS256');
}

function validate_jwt($token, $key) {
    return JWT::decode($token, new Key($key, 'HS256'));
}
?>