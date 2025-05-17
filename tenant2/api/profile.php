<?php
require '../../auth/config.php';
require '../../auth/jwt_helper.php';

header('Content-Type: application/json');

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "Missing token"]);
    exit;
}

$token = str_replace('Bearer ', '', $headers['Authorization']);
try {
    $payload = validate_jwt($token, $key);

    if (!isset($payload->sub) || !isset($payload->tenant_id)) {
        http_response_code(400);
        echo json_encode(["error" => "Token missing required fields"]);
        exit;
    }	

    $user_id = $payload->sub;
    $tenant_id = $payload->tenant_id;

    $stmt = $db->prepare("SELECT name, email, role FROM users WHERE id = ? AND tenant_id = ?");
    $stmt->bind_param("ii", $user_id, $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo json_encode([
            "name" => $user['name'],
            "email" => $user['email'],
            "role" => $user['role']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}
