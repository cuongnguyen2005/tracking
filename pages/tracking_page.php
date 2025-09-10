<?php
include  '../api/attendance.php';

$result = getAttendance('000001');
?>

<div class="page-down-title">本月出勤纪录</div>
<table>
    <tr>
        <th>打卡日期</th>
        <th>时间</th>
        <th>状态</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['day']) ?></td>
                <td><?= htmlspecialchars($row['time']) ?></td>
                <td><?= $row['status'] == 0 ? "" : "休" ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">没有出勤纪录</td>
        </tr>
    <?php endif; ?>
</table>
