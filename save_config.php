<?php
// api/save_config.php
require_once __DIR__ . '/../includes/functions.php';
require_login();

header('Content-Type: application/json');

// Ensure POST and CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Invalid JSON']);
    exit;
}

// CSRF check — frontend must include X-CSRF-Token header
$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!check_csrf_token($csrf)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'CSRF token invalid']);
    exit;
}

// file path outside webroot
$config_path = __DIR__ . '/../config.json';

// Atomic write with temp + rename
$temp = tempnam(sys_get_temp_dir(), 'cfg');
if (file_put_contents($temp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Write failed']);
    exit;
}
if (!rename($temp, $config_path)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Atomic write failed']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Saved']);