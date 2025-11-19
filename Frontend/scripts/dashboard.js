
// ------------------ auth & logout ------------------
const localtoken = localStorage.getItem("token");
const localrole  = localStorage.getItem("role");

if (!localtoken) {
    window.location.href = "login.html";
}

const logoutbtn = document.getElementById("logoutButton");

logoutbtn.addEventListener('click', () => {
    localStorage.removeItem("id");
    localStorage.removeItem("token");
    localStorage.removeItem("role");

    window.location.href = "login.html";
});

// ------------------ admin button ------------------
const adminBtn = document.getElementById('adminButton');

if (adminBtn) {
    const role = localStorage.getItem('role');

    if (role !== 'admin') {
        // hide completely if not admin
        adminBtn.style.display = 'none';
    } else {
        // only admins can see + click it
        adminBtn.addEventListener('click', () => {
            window.location.href = 'admin.html';
        });
    }
}
// ------------------ tab navigation ------------------
const navTabs = document.querySelectorAll('.nav-tab');
const sections = {
    dashboard: document.getElementById('dashboardSection'),
    habits: document.getElementById('habitsSection'),
    entries: document.getElementById('entriesSection'),
    analytics: document.getElementById('analyticsSection'),
};

function showSection(name) {
    // toggle active tab
    navTabs.forEach(tab => {
        if (tab.dataset.section === name) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    // toggle sections
    Object.entries(sections).forEach(([key, el]) => {
        if (!el) return;
        if (key === name) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
}

// attach events
navTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const target = tab.dataset.section;
        if (target) {
            showSection(target);
        }
    });
});

// default: dashboard
showSection('dashboard');
