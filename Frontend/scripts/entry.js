// entry.js

const rawText     = document.getElementById("rawText");
const submit      = document.getElementById("submit");
const entriesList = document.getElementById("entriesList");

const entryIdInput    = document.getElementById("entryIdInput");
const entryUpdateText = document.getElementById("entryUpdateText");
const updateEntryBtn  = document.getElementById("updateEntryButton");
const deleteEntryBtn  = document.getElementById("deleteEntryButton");

// ------------------ create entry ------------------
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
        alert(response.data.message || 'Entry created');

        rawText.value = '';

        await loadEntries();
    } catch (error) {
        console.error('Entry error:', error);
        alert('Error creating entry');
    }
});

// ------------------ load entries list ------------------
async function loadEntries() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/entries/list",
            {},
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Entries list response:', response.data);

        if (!response.data || response.data.status !== 200) {
            entriesList.textContent = response.data?.message || 'Failed to load entries';
            return;
        }

        const entries = response.data.data || [];

        // clear old content
        entriesList.innerHTML = '';

        if (entries.length === 0) {
            entriesList.textContent = 'No entries yet. Create your first one above!';
            return;
        }

        entries.forEach(entry => {
            const id        = entry.id;
            const createdAt = entry.created_at ?? '';
            const raw       = entry.raw_text ?? '';

            // card div
            const card = document.createElement('div');
            card.className = 'entry-card';
            card.style.border = '1px solid #ccc';
            card.style.padding = '8px';
            card.style.marginBottom = '6px';

            // first line: ID + date
            const header = document.createElement('div');
            header.innerHTML = `<strong>ID: ${id}</strong> – ${createdAt}`;

            // second line: text
            const body = document.createElement('div');
            body.textContent = raw;

            card.appendChild(header);
            card.appendChild(body);
            entriesList.appendChild(card);
        });

    } catch (error) {
        console.error('Load entries error:', error);
        entriesList.textContent = 'Error loading entries.';
    }
}

// Call on page load
loadEntries();

// ------------------ delete entry by ID ------------------
deleteEntryBtn.addEventListener('click', async () => {  
    const entryId = entryIdInput.value;

    if (!entryId) {
        alert("Please enter an Entry ID to delete");
        return;
    }

    const confirmDelete = confirm(`Delete entry #${entryId}?`);
    if (!confirmDelete) return;

    try {
        const response = await axios.post(
            "../Backend/index.php?route=/entries/delete",
            { id: Number(entryId) },
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Delete entry response:', response.data);
        alert(response.data.message || 'Entry deleted');

        entryIdInput.value = '';
        entryUpdateText.value = '';
        await loadEntries();
    } catch (error) {
        console.error('Delete entry error:', error);
        alert('Error deleting entry');
    }
});

// ------------------ update entry by ID (raw_text) ------------------
updateEntryBtn.addEventListener('click', async () => {
    const entryId = entryIdInput.value;
    const newText = entryUpdateText.value;

    if (!entryId) {
        alert("Please enter an Entry ID to update");
        return;
    }
    if (!newText || newText.trim() === '') {
        alert("Please enter new text for the entry");
        return;
    }

    try {
        const response = await axios.post(
            "../Backend/index.php?route=/entries/update",
            {
                id: Number(entryId),
                raw_text: newText
            },
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );

        console.log('Update entry response:', response.data);
        alert(response.data.message || 'Entry updated');

        entryUpdateText.value = '';
        await loadEntries();
    } catch (error) {   
        console.error('Update entry error:', error);
        alert('Error updating entry');
    }
});
