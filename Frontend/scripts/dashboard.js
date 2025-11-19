
// ------------------ auth & logout ------------------
const localtoken = localStorage.getItem("token");
const localrole  = localStorage.getItem("role");

if (!localtoken) {
    window.location.href = "login.html";
}

const logoutbtn = document.getElementById("logoutButton");

logoutbtn.addEventListener('click', async () => {
    localStorage.removeItem("id");
    localStorage.removeItem("token");
    localStorage.removeItem("role");

    window.location.href = "login.html";
});
