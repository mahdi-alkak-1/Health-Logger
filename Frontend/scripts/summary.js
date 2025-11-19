// summary.js

const summaryTextEl   = document.getElementById('summaryText');
const nutritionTextEl = document.getElementById('nutritionCoachText');

async function loadWeeklySummary() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/ai/weekly-summary",
            {},
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );
        console.log('Weekly summary response:', response.data);

        if (!response.data || response.data.status !== 200) {
            summaryTextEl.textContent = response.data?.message || 'Failed to load summary.';
            return;
        }

        summaryTextEl.textContent = response.data.data.summary;
    } catch (err) {
        console.error('Weekly summary error:', err);
        summaryTextEl.textContent = 'Error loading summary.';
    }
}

async function loadNutritionCoach() {
    try {
        const response = await axios.post(
            "../Backend/index.php?route=/ai/nutrition-coach",
            {},
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') }
            }
        );
        console.log('Nutrition coach response:', response.data);

        if (!response.data || response.data.status !== 200) {
            nutritionTextEl.textContent = response.data?.message || 'Failed to load nutrition advice.';
            return;
        }

        nutritionTextEl.textContent = response.data.data.advice;
    } catch (err) {
        console.error('Nutrition coach error:', err);
        nutritionTextEl.textContent = 'Error loading nutrition advice.';
    }
}

// Call on page load
loadWeeklySummary();
loadNutritionCoach();
