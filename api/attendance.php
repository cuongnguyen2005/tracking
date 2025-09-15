<?php
header("Content-Type: application/json");
include '../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Lấy tham số action (để phân biệt loại dữ liệu cần lấy)
$action = $_GET['action'] ?? '';

// Xử lý GET
if ($method === 'GET') {
    $empCode = $_GET['emp_code'] ?? '';

    if (!$empCode) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu mã nhân viên"]);
        exit;
    }

    // 1. Lấy toàn bộ danh sách chấm công
    if ($action === 'all') {
        $sql = "SELECT DATE(check_date) AS day, TIME(check_time) AS time, status
                FROM attendance
                WHERE emp_code = ?
                ORDER BY check_date DESC, check_time DESC
                LIMIT 100";
    }

    // 2. Lấy chấm công hôm nay
    elseif ($action === 'today') {
        $sql = "SELECT DATE(check_date) AS day, TIME(check_time) AS time, status
                FROM attendance
                WHERE emp_code = ?
                AND DATE(check_date) = CURDATE()
                ORDER BY check_date DESC, check_time DESC
                LIMIT 4";
    }

    // Không có action hợp lệ
    else {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu hoặc sai action"]);
        exit;
    }

    // Thực thi SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $empCode);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
}

// Xử lý POST: thêm dữ liệu
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["error" => "JSON không hợp lệ"]);
        exit;
    }

    $empCode = $data['emp_code'] ?? '';

    if (!$empCode) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu mã nhân viên"]);
        exit;
    }

    // Lấy ngày hiện tại
    $currentDate = date('Y-m-d');
    
    // Kiểm tra xem đã có bản ghi nào cho nhân viên này trong ngày chưa
    $sql_check = "SELECT * FROM attendance WHERE emp_code = ? AND check_date = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $empCode, $currentDate);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    // Nếu là bản ghi đầu tiên trong ngày, status = 1
    $status = $result_check->num_rows === 0 ? 1 : 2;

    // Thực hiện truy vấn chèn dữ liệu
    $sql = "INSERT INTO attendance (emp_code, check_date, check_time, status) VALUES (?, CURDATE(), NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $empCode, $status);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    exit;
}

else {
    http_response_code(405);
    echo json_encode(["error" => "Phương thức không hỗ trợ"]);
}
