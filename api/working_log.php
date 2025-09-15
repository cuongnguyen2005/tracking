<?php
header("Content-Type: application/json");
include '../config/config.php'; // file này phải khởi tạo $conn (MySQLi)

$empCode = $_GET['emp_code'] ?? '';
$year = isset($_GET['year']) ? intval($_GET['year']) : null;
$month = isset($_GET['month']) ? intval($_GET['month']) : null;
$action = $_GET['action'] ?? null;

if (!$empCode) {
    http_response_code(400);
    echo json_encode(["error" => "Missing emp_code"]);
    exit;
}

try {
    // Nếu không có year, month hoặc action=months => trả về danh sách năm tháng
    if ($action === 'months' || ($year === null || $month === null)) {
        $sql = "SELECT DISTINCT 
                    YEAR(check_date) AS year, 
                    MONTH(check_date) AS month 
                FROM working_log 
                WHERE emp_code = ?
                ORDER BY year DESC, month DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param("s", $empCode);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        exit;
    }

    // Nếu có year và month hoặc action=days => trả về chi tiết ngày trong tháng đó
    if ($action === 'days' || ($year !== null && $month !== null)) {
        // Tính ngày đầu và cuối tháng (month có thể từ 0-11 hoặc 1-12, tùy bạn client gửi)
        // Ở đây giả sử month là số 1-12 đúng chuẩn
        $start_date = sprintf("%04d-%02d-01", $year, $month);
        $end_date = date("Y-m-t", strtotime($start_date));

        $sql = "SELECT emp_code, check_date, working_hours, status FROM working_log
                WHERE emp_code = ? AND check_date BETWEEN ? AND ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param("sss", $empCode, $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $day = date('j', strtotime($row['check_date'])); // Lấy ngày (1-31)
            $data[$day] = [
                'hours' => $row['working_hours'],
                'status' => $row['status']
            ];
        }

        echo json_encode($data);
        exit;
    }

    // Nếu không thỏa điều kiện trên, trả lỗi
    http_response_code(400);
    echo json_encode(["error" => "Invalid parameters"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database error",
        "details" => $e->getMessage(),
        "emp_code" => $empCode,
        "year" => $year,
        "month" => $month,
        "action" => $action
    ]);
}
