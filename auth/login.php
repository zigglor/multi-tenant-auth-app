<?php
require 'config.php';
require 'jwt_helper.php';
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $db->prepare("SELECT id, password, role, tenant_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Create refresh token
    $refresh_payload = [
		'sub' => (int)$user['id'],
		'tenant_id' => $user['tenant_id'],
		'role' => $user['role'],
		'exp' => time() + (7 * 24 * 60 * 60)
	];
    $refresh_token = JWT::encode($refresh_payload, $key, 'HS256');

    $access_payload = [
		'iss' => 'https://auth.zigglor.com',
		'sub' => $user['id'],
		'role' => $user['role'],
		'tenant_id' => $user['tenant_id'], // match profile.php expectations
		'exp' => time() + (15 * 60) // 15 minutes
	];
	$access_token = JWT::encode($access_payload, $key, 'HS256');

	// Store refresh token in cookie
	setcookie("refresh_token", $refresh_token, time() + 604800, "/", ".zigglor.com", true, true);

	echo json_encode([
		"access_token" => $access_token,
		"redirect" => "https://" . $user['tenant_id'] . ".zigglor.com"
	]);

} else {
    //http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}
