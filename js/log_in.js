const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');

const usernameError = document.getElementById('username-error');
const passwordError = document.getElementById('password-error');
const loginError = document.getElementById('login-error');

// === Khi rời khỏi ô input (blur) ===
usernameInput.addEventListener('blur', () => {
    if (!usernameInput.value.trim()) {
        usernameError.textContent = '请输入用户名';
    }
});

passwordInput.addEventListener('blur', () => {
    if (!passwordInput.value.trim()) {
        passwordError.textContent = '请输入密码';
    }
});

// === Khi bắt đầu nhập lại (input) => xóa lỗi ===
usernameInput.addEventListener('input', () => {
    if (usernameInput.value.trim()) {
        usernameError.textContent = '';
    }
});

passwordInput.addEventListener('input', () => {
    if (passwordInput.value.trim()) {
        passwordError.textContent = '';
    }
});

// === Khi nhấn nút "登录" ===
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    // Reset lỗi
    usernameError.textContent = '';
    passwordError.textContent = '';
    loginError.textContent = '';

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    let hasError = false;

    if (!username) {
        usernameError.textContent = '请输入用户名';
        hasError = true;
    }

    if (!password) {
        passwordError.textContent = '请输入密码';
        hasError = true;
    }

    if (hasError) return;

    const res = await fetch('../api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    });

    const data = await res.json();

    if (data.success) {
        localStorage.setItem('emp_code', data.emp_code);
        window.location.href = '../index.html';
    } else {
        if (data.error === 'missing') {
            loginError.textContent = '请输入所有信息';
        } else if (data.error === 'invalid') {
            loginError.textContent = '用户名或密码错误';
        } else {
            loginError.textContent = '登录失败';
        }
    }
});
