<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'api/attendance.php';
include 'api/employee.php';

$empCode = "000001";
$employee = getEmployeeInfo($empCode)->fetch_assoc();
$todayAttendance = getTodayAttendance('000001');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin'])) {
    if (addAttendance($empCode)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking</title>
    <!--css-->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tracking.css">
</head>

<body>
    <div class="page">
        <div class="page-up">
            <!--info-->
            <div class="page-up-btn info">
                <div class="info-content">
                    <div class="info-left">
                        <span><img src="images/hinh-anh-mat-troi-18.jpg" alt=""></span>
                    </div>
                    <div class="info-right">
                        <span><?= htmlspecialchars($employee['emp_name']) ?> - <?= $employee['emp_code'] ?></span>
                        <span><?= $employee['dept_code'] ?></span>
                        <span><?= $employee['dept_name'] ?></span>
                    </div>
                </div>
                <div class="tracking-btn">
                    <hr>
                    <div class="tracking-btn-up">
                        <button style="color: #FF8A65;">薪资查询</button>
                        <button style="color: #2563EB;">我的任务</button>
                    </div>
                    <form method="POST"><button type="submit" name="checkin">线上打卡</button></form>
                </div>
            </div>
            <!--打卡时间-->
            <div class="page-up-btn tracking tracking-time">
                <div class="tracking-up">
                    <span class="tracking-title">考勤打卡</span>
                    <hr>
                    <div class="tracking-note">
                        <span style="color: #2563EB;">排班时间 (09:00-18:00)</span>
                        <?php if ($todayAttendance && $todayAttendance->num_rows > 0): ?>
                            <?php while ($row = $todayAttendance->fetch_assoc()): ?>
                                <div class="time-line">
                                    <span class="date"><?= htmlspecialchars($row['day']) ?></span>
                                    <span class="hour"><?= htmlspecialchars($row['time']) ?></span>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div><button class="tracking-page">本月出勤纪录</button></div>
            </div>
            <!--年假-->
            <div class="page-up-btn tracking year-time">
                <div class="tracking-up">
                    <span class="tracking-title">今年假别额度</span>
                    <hr>
                    <div class="tracking-note">
                        <div class="time-line">
                            <span class="date">年假 2024</span>
                            <span class="hour">15 日</span>
                        </div>

                        <div class="time-line">
                            <span class="date">年假 2025</span>
                            <span class="hour"><?= $employee['leave_days'] ?> 日</span>
                        </div>
                    </div>
                </div>
                <div><button class="request-leave">请假纪录</button></div>
            </div>
        </div>
        <div class="hr-wrapper" style="padding: 0 100px;">
            <hr>
        </div>
        <div class="page-down" id="page-down">

        </div>
    </div>

    <!--js-->
    <script src="js/page_load.js"></script>
</body>

</html>