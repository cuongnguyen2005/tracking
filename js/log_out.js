document.getElementById('logoutBtn').addEventListener('click', async () => {
    const res = await fetch('api/logout.php', { method: 'POST' });
    const data = await res.json();
    if (data.success) {
        // logout xong chuyển về login.html
        window.location.href = 'pages/login.html';
    }
});