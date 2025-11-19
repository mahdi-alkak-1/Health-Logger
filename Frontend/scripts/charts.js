// charts.js

const weeklyCanvas  = document.getElementById('weeklyChart');
const monthlyCanvas = document.getElementById('monthlyChart');

let weeklyChartInstance  = null;
let monthlyChartInstance = null;

// Load stats for 'week' or 'month'
async function loadStats(period) {
    const response = await axios.post(
        "../Backend/index.php?route=/entries/stats",
        { period },
        {
            headers: { 'X-Auth-Token': localStorage.getItem('token') }
        }
    );

    if (!response.data || response.data.status !== 200) {
        console.error('Stats error:', response.data);
        return [];
    }
    return response.data.data || [];
}

// Define all metrics we want to display
// axis: 'yLeft' or 'yRight'
const METRICS = [
    { field: 'sleep_hours',      label: 'Sleep (h)',          axis: 'yLeft',  color: 'rgba(75, 192, 192, 1)' },
    { field: 'water_liters',     label: 'Water (L)',          axis: 'yLeft',  color: 'rgba(54, 162, 235, 1)' },
    { field: 'mood_score',       label: 'Mood (1–10)',        axis: 'yLeft',  color: 'rgba(255, 206, 86, 1)' },
    { field: 'steps_count',      label: 'Steps',              axis: 'yRight', color: 'rgba(153, 102, 255, 1)' },
    { field: 'exercise_minutes', label: 'Exercise (min)',     axis: 'yRight', color: 'rgba(255, 99, 132, 1)' },
    { field: 'caffeine_cups',    label: 'Coffee (cups)',      axis: 'yRight', color: 'rgba(255, 159, 64, 1)' },
];

// Build Chart.js datasets from stats
function buildDatasets(stats) {
    const labels = stats.map(s => s.day); // e.g. '2025-11-18'

    const datasets = METRICS.map(metric => {
        const data = stats.map(s => {
            const value = s[metric.field];
            return value !== null && value !== undefined ? Number(value) : null;
        });

        return {
            label: metric.label,
            data,
            borderColor: metric.color,
            backgroundColor: metric.color,
            tension: 0.3,
            spanGaps: true,
            pointRadius: 3,
            yAxisID: metric.axis,
        };
    });

    return { labels, datasets };
}

function createLineChart(ctx, labels, datasets, titleText) {
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets,
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            stacked: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: titleText,
                },
            },
            scales: {
                yLeft: {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sleep / Water / Mood',
                    },
                    beginAtZero: true,
                },
                yRight: {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Steps / Exercise / Coffee',
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false, // don’t double the grid
                    },
                },
            },
        },
    });
}

async function renderCharts() {
    try {
        const weeklyStats  = await loadStats('week');   // last 7 days
        const monthlyStats = await loadStats('month');  // last 30 days

        // Destroy old charts if this is ever called again
        if (weeklyChartInstance) {
            weeklyChartInstance.destroy();
        }
        if (monthlyChartInstance) {
            monthlyChartInstance.destroy();
        }

        // Weekly chart – all metrics
        if (weeklyCanvas && weeklyStats.length > 0) {
            const { labels, datasets } = buildDatasets(weeklyStats);
            weeklyChartInstance = createLineChart(
                weeklyCanvas.getContext('2d'),
                labels,
                datasets,
                'Weekly Progress (last 7 days)'
            );
        }

        // Monthly chart – all metrics
        if (monthlyCanvas && monthlyStats.length > 0) {
            const { labels, datasets } = buildDatasets(monthlyStats);
            monthlyChartInstance = createLineChart(
                monthlyCanvas.getContext('2d'),
                labels,
                datasets,
                'Monthly Progress (last 30 days)'
            );
        }
    } catch (err) {
        console.error('Chart render error:', err);
    }
}

renderCharts();
