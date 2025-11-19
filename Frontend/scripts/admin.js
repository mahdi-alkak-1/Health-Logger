// admin.js

const adminEntriesDiv = document.getElementById('adminEntries');
const adminHabitsDiv  = document.getElementById('adminHabits');
const backBtn         = document.getElementById('backToDashboard');

backBtn.addEventListener('click', () => {
    window.location.href = 'dashboard.html';
});

async function loadAdminEntries() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/admin/entries",
            {},
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        if (!response.data || response.data.status !== 200) {
            adminEntriesDiv.innerHTML = `<p>${response.data?.message || 'Failed to load entries.'}</p>`;
            return;
        }

        const entries = response.data.data || [];
        if (entries.length === 0) {
            adminEntriesDiv.innerHTML = '<p>No entries found.</p>';
            return;
        }

        let html = `
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Raw Text</th>
                    </tr>
                </thead>
                <tbody>
        `;

        entries.forEach(e => {
            html += `
                <tr>
                    <td>${e.id}</td>
                    <td>${e.email}</td>
                    <td>${e.created_at}</td>
                    <td>${e.raw_text}</td>
                </tr>
            `;
        });

        html += `
                </tbody>
            </table>
        `;

        adminEntriesDiv.innerHTML = html;
    } catch (err) {
        console.error('Admin entries error:', err);
        adminEntriesDiv.innerHTML = '<p>Error loading admin entries.</p>';
    }
}

async function loadAdminHabits() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/admin/habits",
            {},
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        if (!response.data || response.data.status !== 200) {
            adminHabitsDiv.innerHTML = `<p>${response.data?.message || 'Failed to load habits.'}</p>`;
            return;
        }

        const habits = response.data.data || [];
        if (habits.length === 0) {
            adminHabitsDiv.innerHTML = '<p>No habits found.</p>';
            return;
        }

        let html = `
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Name</th>
                        <th>Field</th>
                        <th>Target</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
        `;

        habits.forEach(h => {
            html += `
                <tr>
                    <td>${h.id}</td>
                    <td>${h.email}</td>
                    <td>${h.name}</td>
                    <td>${h.entry_field}</td>
                    <td>${h.target_value}</td>
                    <td>${h.unit}</td>
                </tr>
            `;
        });

        html += `
                </tbody>
            </table>
        `;

        adminHabitsDiv.innerHTML = html;
    } catch (err) {
        console.error('Admin habits error:', err);
        adminHabitsDiv.innerHTML = '<p>Error loading admin habits.</p>';
    }
}

loadAdminEntries();
loadAdminHabits();
