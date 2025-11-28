<?php
// includes/db.php
// PDO connection with exceptions and charset set
$DB_HOST = 'db.fr-pari1.bengt.wasmernet.com';
$DB_PORT = '10272';
$DB_NAME = 'RVMod';
$DB_USER = '93e9dd917592800054b6e946232e';
$DB_PASS = '069293e9-dd91-76b1-8000-7c7e1fff1461';

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Exception $e) {
    // For production, log error instead of showing it
    http_response_code(500);
    echo "Database connection failed.";
    error_log("DB connect error: " . $e->getMessage());
    exit;
}