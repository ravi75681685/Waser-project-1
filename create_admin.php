<?php
// scripts/create_admin.php
require_once __DIR__ . '/../includes/db.php';

if (php_sapi_name() !== 'cli') {
    echo "Run from CLI only.";
    exit;
}

$email = readline("Admin email: ");
$pass  = readline("Password (will be hashed): ");
$display = readline("Display name (optional): ");

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email.\n";
    exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (email, password_hash, display_name) VALUES (?, ?, ?)");
$stmt->execute([$email, $hash, $display]);

echo "Admin created: {$email}\n";