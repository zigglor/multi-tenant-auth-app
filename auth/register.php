<?php
require '../auth/config.php'; // Connects to $db
header('Content-Type: application/json');

// Get POST data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$tenant = $_POST['tenant'] ?? '';

// Basic validation
if (!$name || !$email || !$password || !$tenant) {
    echo json_encode(["success" => false, "error" => "All fields are required"]);
    exit;
}

// Check if tenant exists, or create it
$stmt = $db->prepare("SELECT id FROM tenants WHERE subdomain = ?");
$stmt->bind_param("s", $tenant);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $tenant_id = $row['id'];
} else {
    // Create new tenant
    $stmt = $db->prepare("INSERT INTO tenants (name, subdomain) VALUES (?, ?)");
    $tenant_name = ucfirst($tenant);
    $stmt->bind_param("ss", $tenant_name, $tenant);
    $stmt->execute();
    $tenant_id = $stmt->insert_id;
}

// Check if email already exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Email already exists"]);
    exit;
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $db->prepare("INSERT INTO users (name, email, password, tenant_id, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->bind_param("sssi", $name, $email, $hashed, $tenant_id);
$stmt->execute();

echo json_encode(["success" => true]);
