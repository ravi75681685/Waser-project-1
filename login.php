<?php
// auth/login.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$pass  = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
    echo json_encode(['ok' => false, 'message' => 'Invalid credentials']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, password_hash, display_name FROM admins WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password_hash'])) {
    echo json_encode(['ok' => false, 'message' => 'Invalid email or password']);
    exit;
}

// Regenerate session id to prevent fixation
session_regenerate_id(true);
$_SESSION['admin_id'] = $user['id'];
$_SESSION['admin_email'] = $email;
$_SESSION['admin_name'] = $user['display_name'] ?? explode('@', $email)[0];

$csrf = create_csrf_token();

echo json_encode(['ok' => true, 'message' => 'Logged in', 'csrf' => $csrf]);