function loadPage(file) {
    fetch(file)
        .then(response => response.text())
        .then(data => {
            document.getElementById("page-down").innerHTML = data;
        })
        .catch(error => console.error("Error loading page:", error));
}

// Khi click nút
document.querySelector(".tracking-page").addEventListener("click", () => {
    loadPage("pages/tracking_page.php");
});

document.querySelector(".request-leave").addEventListener("click", () => {
    loadPage("pages/request_leave.html");
});

//default loading
window.addEventListener("DOMContentLoaded", () => {
    loadPage("pages/tracking_page.php");
});