// habit.js

const habitName       = document.getElementById("habitName");
const habitTarget     = document.getElementById("habitTarget");
const createHabitBtn  = document.getElementById("createHabitButton");
const habitsList      = document.getElementById("habitsList");

// NEW controls for update/delete
const habitIdInput   = document.getElementById("habitIdInput");
const habitNewTarget = document.getElementById("habitNewTarget");
const updateHabitBtn = document.getElementById("updateHabitButton");
const deleteHabitBtn = document.getElementById("deleteHabitButton");

const habitNameToField = {
    sleep:    'sleep_hours',
    steps:    'steps_count',
    exercise: 'exercise_minutes',
    coffee:   'caffeine_cups',
    water:    'water_liters',
    mood:     'mood_score',
};

const habitNameUnit = {
    sleep:    'hour/s',
    steps:    'step/s',
    exercise: 'min/s',
    coffee:   'cup/s',
    water:    'liter/s',
    mood:     'm',
};

// ------------------ create habit ------------------
createHabitBtn.addEventListener('click', async () => {
    try {
        const category    = habitName.value;
        const entryField  = habitNameToField[category];
        const categoryunit = habitNameUnit[category];

        if (!entryField) {
            alert("Invalid habit category");
            return;
        }

        if (!habitTarget.value) {
            alert("Please enter a target value");
            return;
        }

        const body = {
            name: category,
            entry_field: entryField,
            unit: categoryunit,
            target_value: Number(habitTarget.value),
        };

        const response = await axios.post(
            "../Backend/index.php?route=/habits/create",
            body,
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') },
            }
        );

        alert(response.data.message || 'Habit created');

        habitTarget.value = '';
        await loadHabits();
    } catch (error) {
        console.error('Habit create error:', error);
        alert('Error creating habit');
    }
});

//-------------------- habit list -------------------
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
            const id = habit.id;

            html += `
                <div class="habit-card" style="border:1px solid #ccc; padding:6px; margin-bottom:4px;">
                    <div>
                        <strong>ID: ${id}</strong> – ${habit.name} 
                        (<code>${habit.entry_field}</code>) – target: 
                        ${habit.target_value} ${habit.unit}
                    </div>
                </div>
            `;
        });

        habitsList.innerHTML = html;
    } catch (error) {
        console.error('Load habits error:', error);
        habitsList.innerHTML = '<p>Error loading habits.</p>';
    }
}

loadHabits();

// ------------------ delete habit by ID ------------------
deleteHabitBtn.addEventListener('click', async () => {
    const habitId = habitIdInput.value;

    if (!habitId) {
        alert("Please enter a Habit ID to delete");
        return;
    }

    const confirmDelete = confirm(`Delete habit #${habitId}?`);
    if (!confirmDelete) return;

    try {
        const response = await axios.post(
            "../Backend/index.php?route=/habits/delete",
            { habit_id: Number(habitId) },
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Delete habit response:', response.data);
        alert(response.data.message || 'Habit deleted');

        habitIdInput.value = '';
        habitNewTarget.value = '';
        await loadHabits();
    } catch (error) {
        console.error('Delete habit error:', error);
        alert('Error deleting habit');
    }
});

// ------------------ update habit target by ID ------------------
updateHabitBtn.addEventListener('click', async () => {
    const habitId    = habitIdInput.value;
    const newTarget  = habitNewTarget.value;

    if (!habitId) {
        alert("Please enter a Habit ID to update");
        return;
    }
    if (!newTarget || isNaN(Number(newTarget))) {
        alert("Please enter a valid new target");
        return;
    }

    try {
        const response = await axios.post(
            "../Backend/index.php?route=/habits/update",
            {
                habit_id: Number(habitId),
                target_value: Number(newTarget)
            },
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Update habit response:', response.data);
        alert(response.data.message || 'Habit updated');

        habitNewTarget.value = '';
        await loadHabits();
    } catch (error) {
        console.error('Update habit error:', error);
        alert('Error updating habit');
    }
});
