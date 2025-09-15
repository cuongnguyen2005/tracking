<?php
header("Content-Type: application/json");
include '../config/config.php';

$token = $_COOKIE['auth_token'] ?? '';

if (!$token) {
    echo json_encode(['logged' => false]);
    exit;
}

$sql = "SELECT emp_code, expiry FROM login_tokens WHERE token = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    echo json_encode(['logged' => false]);
    exit;
}

// check expiry
if (strtotime($row['expiry']) < time()) {
    // expired, optionally delete token
    $del = $conn->prepare("DELETE FROM login_tokens WHERE token = ?");
    $del->bind_param("s", $token);
    $del->execute();
    echo json_encode(['logged'=>false]);
    exit;
}

// ok
echo json_encode(['logged' => true, 'emp_code' => $row['emp_code']]);
