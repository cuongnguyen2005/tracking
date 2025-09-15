<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
include '../config/config.php';

// Read JSON body
$input = json_decode(file_get_contents("php://input"), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'missing']);
    exit;
}

// 1) Get user by username (adjust field names)
$sql = "SELECT emp_code, password FROM employee WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo json_encode(['success'=>false,'error'=>'invalid']);
    exit;
}

// 2) Verify password
// NOTE: use password_hash() when storing password. If your DB stores plain text, adjust accordingly (but move to hashed asap).
if ($password !== $user['password']) {
    echo json_encode(['success'=>false,'error'=>'invalid']);
    exit;
}

$_SESSION['emp_code'] = $user['emp_code'];

// 3) Generate token and save
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', time() + 60*60*8); // 8 hour expiry (tùy chỉnh)

$sql2 = "INSERT INTO login_tokens (emp_code, token, expiry) VALUES (?, ?, ?)";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("sss", $user['emp_code'], $token, $expiry);
$stmt2->execute();

// 4) Set cookie (HttpOnly) — path "/" để site toàn bộ dùng được
setcookie('auth_token', $token, [
    'expires' => time() + 60*60*8,
    'path' => '/',
    'httponly' => true,
    // 'secure' => true, // bật khi dùng HTTPS
    'samesite' => 'Lax'
]);

echo json_encode([
    'success'=>true,
    'emp_code'=>$user['emp_code'],
    'emp_name'=>$user['emp_name'] ?? '',
    'dept_code'=>$user['dept_code'] ?? ''
]);
