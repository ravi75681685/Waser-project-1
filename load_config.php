<?php
// api/load_config.php
require_once __DIR__ . '/../includes/functions.php';
require_login();
header('Content-Type: application/json');

// config file outside webroot recommended
$config_path = __DIR__ . '/../config.json';

if (!file_exists($config_path)) {
    // return defaults if desired
    echo json_encode(['ok' => true, 'config' => new stdClass()]);
    exit;
}

$content = file_get_contents($config_path);
$json = json_decode($content, true);
if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Invalid config file']);
    exit;
}

echo json_encode(['ok' => true, 'config' => $json]);