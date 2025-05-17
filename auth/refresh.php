<?php
require 'config.php';
require 'vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header("Access-Control-Allow-Origin: https://{$_SERVER['HTTP_HOST']}");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if (!isset($_COOKIE['refresh_token'])) {
    http_response_code(401);
    echo json_encode(["error" => "No refresh token found"]);
    exit;
} else {
	
	$refresh_token = $_COOKIE['refresh_token'];
	
	if ($refresh_token != "") {

		try {
			$decoded = JWT::decode($refresh_token, new Key($key, 'HS256'));
			
			$payload = [
				'iss' => 'https://auth.zigglor.com',
				'aud' => $decoded->sub,
				'sub' => $decoded->sub,
				'role' => $decoded->role,
				'tenant_id' => $decoded->tenant_id,
				'exp' => time() + (15 * 60) // 15 minutes
			];

			$access_token = JWT::encode($payload, $key, 'HS256');

			echo json_encode([
				"access_token" => $access_token
			]);
		} catch (Exception $e) {
			http_response_code(401);
			echo json_encode(["error" => "Invalid refresh token", "details" => $e->getMessage()]);
		}
	}
}
