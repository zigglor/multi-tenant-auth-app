<?php
header('Content-Type: application/json');

// Include your existing database config
require_once 'config.php'; // adjust the path if needed

$sql = "SELECT subdomain FROM tenants";
$result = $db->query($sql);

$subdomains = [];
while ($row = $result->fetch_assoc()) {
    $subdomains[] = $row['subdomain'];
}

echo json_encode($subdomains);
?>
