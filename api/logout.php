<?php
header("Content-Type: application/json");
include '../config/config.php';

$token = $_COOKIE['auth_token'] ?? '';
if ($token) {
    $stmt = $conn->prepare("DELETE FROM login_tokens WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
}

// expire cookie
setcookie('auth_token', '', time() - 3600, '/');

// respond
echo json_encode(['success'=>true]);
