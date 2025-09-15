let empCode = localStorage.getItem("emp_code");
if (!empCode) {
    window.location.href = "pages/login.html";
}

function loadEmployeeInfo() {
    fetch(`api/employee.php?emp_code=${empCode}`)
        .then(res => {
            if (!res.ok) throw new Error("Không thể tải dữ liệu nhân viên");
            return res.json();
        })
        .then(data => {
            const infoDiv = document.getElementById('employee-info');
            infoDiv.innerHTML = `
                <span>${data.emp_name} - ${data.emp_code}</span>
                <span>${data.dept_code}</span>
                <span>${data.dept_name}</span>
            `;
        })
        .catch(err => {
            console.log("Error employee:", err);
        });
}

function loadEmployeeDetail() {
    fetch(`api/employee.php?emp_code=${empCode}`)
        .then(res => {
            if (!res.ok) throw new Error("Không thể tải dữ liệu nhân viên");
            return res.json();
        })
        .then(data => {
            const tableDiv = document.getElementById('table-salary');
            if (!data) {
                tableDiv.innerHTML = `<p>没有出勤纪录</p>`;
                return;
            }

            tableDiv.innerHTML = `
                <table>
                    <tr>
                        <th>员工编号</th>
                        <th>姓名</th>
                        <th>性别</th>
                        <th>部门</th>
                        <th>薪资</th>
                        <th>状态</th>
                    </tr>
                    <tr>
                        <td>${data.emp_code}</td>
                        <td>${data.emp_name}</td>
                        <td>${data.gender == 0 ? "男" : "女"}</td>
                        <td>${data.dept_name}</td>
                        <td>${data.salary}</td>
                        <td>${data.status == 1 ? "在职" : "离职"}</td>
                    </tr>
                </table>
            `;
        })
        .catch(err => {
            console.log("Error salary:", err);
        });
}

function loadLeaveday() {
    const year = new Date().getFullYear();
    fetch(`api/employee.php?emp_code=${empCode}`)
        .then(res => {
            if (!res.ok) throw new Error("Không thể tải dữ liệu nhân viên");
            return res.json();
        })
        .then(data => {
            const infoDiv = document.getElementById('leave-note');
            infoDiv.innerHTML = `
                <div class="time-line">
                    <span class="date">年假 ${year}</span>
                    <span class="hour">${data.leave_days} 日</span>
                </div>
            `;
        })
        .catch(err => {
            console.log("Error leave day:", err);
        });
}

function loadAttendance() {
    fetch(`api/attendance.php?emp_code=${empCode}&action=all`)
        .then(res => {
            if (!res.ok) throw new Error("Không thể tải dữ liệu chấm công");
            return res.json();
        })
        .then(data => {
            const tableDiv = document.getElementById('table-attendance');
            if (!data || data.length === 0) {
                tableDiv.innerHTML = `<p>没有出勤纪录</p>`;
                return;
            }

            let rows = data.map(row => `
                <tr>
                    <td>${row.day}</td>
                    <td>${row.time}</td>
                    <td></td>
                </tr>
            `).join("");

            tableDiv.innerHTML = `
                <table>
                    <tr>
                        <th>打卡日期</th>
                        <th>时间</th>
                        <th>状态</th>
                    </tr>
                    ${rows}
                </table>
            `;
        })
        .catch(err => {
            console.log("Error:", err);
        });
}

function loadCurrentAttendance() {
    fetch(`api/attendance.php?emp_code=${empCode}&action=today`)
        .then(res => {
            if (!res.ok) throw new Error("Không thể tải dữ liệu chấm công");
            return res.json();
        })
        .then(data => {
            const div = document.getElementById('time-line');
            if (!data || data.length === 0) {
                return;
            }

            let rows = data.map(row => `
                <div class="time-line">
                    <span class="date">${row.day}</span>
                    <span class="hour">${row.time}</span>
                </div>
            `).join("");

            div.innerHTML = `
                <span style="color: #2563EB;">排班时间 (09:00-18:00)</span>
                ${rows}
            `;
        })
        .catch(err => {
            console.log("Error:", err);
        });
}

function checkIn() {
    fetch(`api/attendance.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ emp_code: empCode })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("打卡失败：" + (data.error || ""));
            }
        });
}

function renderCalendar(year, month) {
    const calendarContainer = document.getElementById('calendar');
    if (!calendarContainer) return;

    // Month: 0-11
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startWeekday = (firstDay.getDay() + 6) % 7;

    // API call
    fetch(`api/working_log.php?emp_code=${empCode}&year=${year}&month=${month + 1}&action=days`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            const monthLabel = `${year}-${String(month + 1).padStart(2, '0')}`;
            let html = '';
            html += `<div class="calendar-header">${monthLabel}</div>`;
            html += '<table class="calendar-table">';
            html += '<thead><tr>' + ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].map(d => `<th>${d}</th>`).join('') + '</tr></thead>';
            html += '<tbody>';

            let day = 1;
            for (let row = 0; row < 6 && day <= daysInMonth; row++) {
                html += '<tr>';
                for (let col = 0; col < 7; col++) {
                    if ((row === 0 && col < startWeekday) || day > daysInMonth) {
                        html += '<td></td>';
                    } else {
                        const entry = data[day];
                        let badge = '';
                        if (entry) {
                            const colorClass = entry.status == 1 ? 'cal-badge-green' : 'cal-badge-red';
                            badge = `<span class="cal-badge ${colorClass}">${entry.hours}h</span>`;
                        }
                        html += `<td><div class="cal-cell"><div class="cal-day">${day}</div>${badge}</div></td>`;
                        day++;
                    }
                }
                html += '</tr>';
            }

            html += '</tbody></table>';
            calendarContainer.innerHTML = html;
        });
}

function loadYearMonthOptions() {
    fetch(`api/working_log.php?emp_code=${empCode}`)
        .then(response => response.json())
        .then(data => {
            const yearSelect = document.getElementById('year-select');
            const monthSelect = document.getElementById('month-select');

            const years = [...new Set(data.map(item => item.year))];
            const months = [...new Set(data.map(item => item.month))];

            yearSelect.innerHTML = years.map(y => `<option value="${y}">${y}</option>`).join('');
            monthSelect.innerHTML = months.map(m => `<option value="${m - 1}">${m}月</option>`).join('');

            // Render calendar lần đầu với giá trị đầu tiên
            const selectedYear = parseInt(yearSelect.value);
            const selectedMonth = parseInt(monthSelect.value);
            renderCalendar(selectedYear, selectedMonth);

            // Thêm event listener để cập nhật khi người dùng chọn thay đổi
            yearSelect.onchange = () => {
                renderCalendar(parseInt(yearSelect.value), parseInt(monthSelect.value));
            };
            monthSelect.onchange = () => {
                renderCalendar(parseInt(yearSelect.value), parseInt(monthSelect.value));
            };
        });
}
