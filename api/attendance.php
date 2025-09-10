<?php
include __DIR__ . '/../config/config.php';

function getAttendance($empCode) {
    global $conn;
    $sql = "SELECT DATE(check_time) AS day, 
                   TIME(check_time) AS time, 
                   status
            FROM attendance
            WHERE emp_code = ?
            ORDER BY check_time DESC
            LIMIT 100";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $empCode);
    $stmt->execute();
    return $stmt->get_result();
}

function getTodayAttendance($empCode) {
    global $conn;
    $sql = "SELECT DATE(check_time) AS day, 
                   TIME(check_time) AS time, 
                   status
            FROM attendance
            WHERE emp_code = ?
              AND DATE(check_time) = CURDATE()
            ORDER BY check_time DESC
            LIMIT 4";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $empCode);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Hàm thêm bản ghi chấm công mới (check-in/check-out)
 */
function addAttendance($empCode) {
    global $conn;
    $sql = "INSERT INTO attendance (emp_code, check_time, status) VALUES (?, NOW(), 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $empCode);
    return $stmt->execute(); 
}