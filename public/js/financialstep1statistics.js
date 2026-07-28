/**
 * Financial Assistance Step 1 Statistics Module JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {
    if (!window.statisticsData) return;

    const data = window.statisticsData;

    // Chart Color Palette Tokens
    const brandPrimary = '#1A237E';
    const accentAmber = '#F59E0B';
    const infoBlue = '#2563EB';
    const purpleAccent = '#8B5CF6';
    const successGreen = '#10B981';
    const dangerRed = '#EF4444';

    // 1. Monthly Intakes Chart
    const monthlyIntakesEl = document.getElementById('monthlyIntakesChart');
    if (monthlyIntakesEl) {
        new Chart(monthlyIntakesEl, {
            type: 'line',
            data: {
                labels: data.monthlyLabels || [],
                datasets: [{
                    label: 'Intake Cases',
                    data: data.monthlyData || [],
                    borderColor: brandPrimary,
                    backgroundColor: 'rgba(26, 35, 126, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: accentAmber,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0F1746',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Beneficiaries by Barangay Chart (Horizontal Bar with Multi-Colors)
    const barangayEl = document.getElementById('barangayChart');
    if (barangayEl) {
        const barangayLabels = data.barangayLabels || [];
        const barangayData = data.barangayData || [];
        const barangayColors = [
            '#1A237E', '#10B981', '#2563EB', '#F59E0B',
            '#EC4899', '#8B5CF6', '#0EA5E9', '#F97316',
            '#14B8A6', '#EF4444', '#6366F1', '#A855F7'
        ];

        new Chart(barangayEl, {
            type: 'bar',
            data: {
                labels: barangayLabels.length ? barangayLabels : ['No Data'],
                datasets: [{
                    label: 'Beneficiaries',
                    data: barangayData.length ? barangayData : [0],
                    backgroundColor: barangayColors.slice(0, barangayLabels.length || 1),
                    borderColor: barangayColors.slice(0, barangayLabels.length || 1),
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#F1F5F9' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 3. Gender Comparison Chart (Doughnut)
    const genderEl = document.getElementById('genderChart');
    if (genderEl) {
        new Chart(genderEl, {
            type: 'doughnut',
            data: {
                labels: data.genderLabels || [],
                datasets: [{
                    data: data.genderData || [],
                    backgroundColor: [infoBlue, '#EC4899', purpleAccent],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 14, font: { size: 12, weight: '600' } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 4. Dahilan ng Paghingi ng Tulong (Doughnut Chart for Tagalog Section)
    const dahilanEl = document.getElementById('dahilanChart');
    if (dahilanEl) {
        const medicalLabels = data.medicalLabels || [];
        const medicalData = data.medicalData || [];

        new Chart(dahilanEl, {
            type: 'doughnut',
            data: {
                labels: medicalLabels.length ? medicalLabels : ['Walang Datos'],
                datasets: [{
                    data: medicalData.length ? medicalData : [1],
                    backgroundColor: [
                        accentAmber, brandPrimary, successGreen,
                        infoBlue, purpleAccent, dangerRed
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                },
                cutout: '55%'
            }
        });
    }
});
