fetch('api/check_auth.php', {
    credentials: 'include'
})
    .then(r => r.json())
    .then(data => {
        if (!data.logged) {
            window.location.href = 'pages/login.html';
        } else {
            window.empCode = data.emp_code;
            loadEmployeeInfo();
            loadAttendance();
            loadCurrentAttendance();
        }
    });

function showContent(contentType) {
    const pageDownContentItems = document.querySelectorAll('.page-down-content');

    // Đầu tiên, ẩn tất cả các phần tử page-down-content
    pageDownContentItems.forEach(content => {
        content.classList.remove('active');
    });

    // Sau đó, chỉ thêm class active vào phần tử phù hợp
    let activeContent;
    switch (contentType) {
        case 'tracking':
            activeContent = document.querySelector('.page-down-content:nth-child(2)');
            loadAttendance();
            loadCurrentAttendance();
            break;

        case 'salary':
            activeContent = document.querySelector('.page-down-content:nth-child(1)');
            loadEmployeeDetail();
            break;

        case 'leave':
            activeContent = document.querySelector('.page-down-content:nth-child(3)');
            loadYearMonthOptions();
            break;

        default:
            activeContent = document.querySelector('.page-down-content:nth-child(2)');
            loadAttendance();
            loadCurrentAttendance();
            break;
    }

    if (activeContent) {
        activeContent.classList.add('active');
    }
}

// Khi click nút
document.querySelector(".tracking-page").addEventListener("click", () => {
    showContent('tracking');
});

document.querySelector(".request-leave").addEventListener("click", () => {
    showContent('leave');
});

document.querySelector(".salary-btn").addEventListener("click", () => {
    showContent('salary');
});

// default loading
window.addEventListener("DOMContentLoaded", () => {
    loadAttendance();
    loadCurrentAttendance();
    loadEmployeeInfo();
    loadLeaveday();
});