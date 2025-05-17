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

    // Validate tenant_id exists in token
    if (!isset($payload->tenant_id)) {
        http_response_code(400);
        echo json_encode(["error" => "Token missing tenant_id"]);
        exit;
    }

    $tenant_id = $payload->tenant_id;

    // Fetch tenant info
    $stmt = $db->prepare("SELECT name, subdomain FROM tenants WHERE id = ?");
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tenant = $result->fetch_assoc();

    if ($tenant) {
        echo json_encode([
            "name" => $tenant['name'],
            "domain" => $tenant['subdomain'] . ".zigglor.com",
            "status" => "active"
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Tenant not found"]);
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
}
