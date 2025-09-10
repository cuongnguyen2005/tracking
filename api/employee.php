<?php
include __DIR__ . '/../config/config.php';

/**
 * Lấy thông tin nhân viên + phòng ban
 * @param string $empCode
 * @return array|null
 */
function getEmployeeInfo($empCode) {
    global $conn;
    $sql = "SELECT e.emp_code, 
                   e.emp_name, 
                   e.username, 
                   e.leave_days, 
                   d.dept_code, 
                   d.dept_name
            FROM employee e
            INNER JOIN department d ON e.dept_code = d.dept_code
            WHERE e.emp_code = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $empCode);
    $stmt->execute();
    return $stmt->get_result();
}