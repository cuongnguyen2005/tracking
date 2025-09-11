<?php
header("Content-Type: application/json");
include '../config/config.php';

$empCode = $_GET['emp_code'] ?? '';

if (!$empCode) {
    http_response_code(400);
    echo json_encode(["error" => "Missing emp_code"]);
    exit;
}

$sql = "SELECT e.*, d.dept_code, d.dept_name
        FROM employee e
        JOIN department d ON e.dept_code = d.dept_code
        WHERE e.emp_code = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $empCode);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);