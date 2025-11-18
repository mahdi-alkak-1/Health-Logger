
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

// ------------------ create entry ------------------
const rawText = document.getElementById("rawText");
const submit  = document.getElementById("submit");
const entriesList = document.getElementById("entriesList");

submit.addEventListener('click', async () => {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/entries/create",
            {
                raw_text: rawText.value,
            },
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Entry response:', response.data);
        alert(response.data.message || 'Entry response');

        rawText.value = '';

        //reload entries after creating a new one
        await loadEntries();
    } catch (error) {
        console.error('Entry error:', error);
    }
});

// ------------------ load entries list ------------------
async function loadEntries() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/entries/list",
            {}, // no body needed
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Entries list response:', response.data);

        if (!response.data || response.data.status !== 200) {
            entriesList.innerHTML = `<p>${response.data?.message || 'Failed to load entries'}</p>`;
            return;
        }

        const entries = response.data.data || [];

        if (entries.length === 0) {
            entriesList.innerHTML = '<p>No entries yet. Create your first one above!</p>';
            return;
        }

        let html = '';
        entries.forEach(entry => {
            const createdAt = entry.created_at ?? '';
            const raw = entry.raw_text ?? '';

            html += `
                <div class="entry-card" style="border:1px solid #ccc; padding:8px; margin-bottom:6px;">
                    <div><strong>${createdAt}</strong></div>
                    <div>${raw}</div>
                </div>
            `;
        });

        entriesList.innerHTML = html;

    } catch (error) {
        console.error('Load entries error:', error);
    }
}

// Call on page load
loadEntries();

//--------------------Habits--------------------
// ---------- Habits elements ----------
const habitName       = document.getElementById("habitName");
const habitEntryField = document.getElementById("habitEntryField");
const habitUnit       = document.getElementById("habitUnit");
const habitTarget     = document.getElementById("habitTarget");
const createHabitBtn  = document.getElementById("createHabitButton");
const habitsList      = document.getElementById("habitsList");
    
createHabitBtn.addEventListener('click', async () => {
    try {
        const body = {
            name: habitName.value,
            entry_field: habitEntryField.value,
            unit: habitUnit.value,
            target_value: Number(habitTarget.value),
        };

        const response = await axios.post(
            "../Backend/index.php?route=/habits/create",
            body,
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Habit create response:', response.data);
        alert(response.data.message || 'Habit created');

        // Reload habits
        await loadHabits();
    } catch (error) {
        console.error('Habit create error:', error);
        // if (error.response) {
        //     alert('Error: ' + JSON.stringify(error.response.data));
        // } else {
        //     alert('Error creating habit');
        // }
    }
});

//--------------------habit list-------------------

async function loadHabits() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/habits/list",
            {},
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Habits list response:', response.data);

        if (!response.data || response.data.status !== 200) {
            habitsList.innerHTML = `<p>${response.data?.message || 'Failed to load habits'}</p>`;
            return;
        }

        const habits = response.data.data || [];

        if (habits.length === 0) {
            habitsList.innerHTML = '<p>No habits yet. Create one above!</p>';
            return;
        }

        let html = '';
        habits.forEach(habit => {
            html += `
                <div class="habit-card" style="border:1px solid #ccc; padding:6px; margin-bottom:4px;">
                    <strong>${habit.name}</strong> 
                    (<code>${habit.entry_field}</code>) – target: 
                    ${habit.target_value} ${habit.unit}
                </div>
            `;
        });

        habitsList.innerHTML = html;

    } catch (error) {
        console.error('Load habits error:', error);
        // if (error.response) {
        //     habitsList.innerHTML = `<p>Error: ${JSON.stringify(error.response.data)}</p>`;
        // } else {
        //     habitsList.innerHTML = '<p>Unexpected error loading habits.</p>';
        // }
    }
}

loadHabits();