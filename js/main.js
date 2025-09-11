const empCode = '000001';

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
            console.log(data);
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
