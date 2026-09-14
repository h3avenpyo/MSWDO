/**
 * Financial Assistance Step 2 Statistics & Analytics Charts
 * Powered by Chart.js
 * Optimized for high-volume datasets, dynamic filtering, and responsive viewports.
 */

document.addEventListener('DOMContentLoaded', function () {
    if (!window.statisticsData) {
        console.warn('Step 2 statisticsData is not defined.');
        return;
    }

    const data = window.statisticsData;

    // Common Chart.js typography and default styling
    Chart.defaults.font.family = "'Plus Jakarta Sans', 'Public Sans', -apple-system, BlinkMacSystemFont, sans-serif";
    Chart.defaults.color = '#64748B';

    // Curated high-contrast color palette for clear category discrimination
    const colorPalette = [
        '#1A237E', '#059669', '#2563EB', '#D97706',
        '#E11D48', '#7C3AED', '#0284C7', '#EA580C',
        '#0D9488', '#DC2626', '#4F46E5', '#9333EA',
        '#16A34A', '#CA8A04', '#475569', '#0891B2',
        '#BE185D', '#6366F1', '#15803D', '#B45309'
    ];

    // Currency Formatter Helper
    const formatPHP = (val) => new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    }).format(val || 0);

    // Debounce Utility for fast typing
    function debounce(func, wait = 150) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    /* ==========================================================
       1. Beneficiaries per Barangay Bar Chart (Scalable & Responsive)
       ========================================================== */
    const barangayCanvas = document.getElementById('barangayBarChart');
    if (barangayCanvas) {
        const rawBarangays = Array.isArray(data.barangayStats) ? data.barangayStats : [];
        let currentLimit = 10; // 10, 25, or 'all'
        let currentSort = 'desc'; // 'desc' (highest) or 'alpha' (A-Z)
        let searchQuery = '';

        const chartInner = document.getElementById('barangayChartInner');
        const emptyState = document.getElementById('barangayChartEmpty');
        const showingText = document.getElementById('barangayChartShowingText');
        const activeCountBadge = document.getElementById('barangayActiveCountBadge');
        const listCountText = document.getElementById('barangayListCountText');
        const listEmpty = document.getElementById('barangayListEmpty');
        const listContainer = document.getElementById('barangayListContainer');

        function getFilteredBarangays() {
            let list = rawBarangays.slice();

            // 1. Text Search Filter
            if (searchQuery.trim().length > 0) {
                const q = searchQuery.trim().toLowerCase();
                list = list.filter(b => (b.name || '').toLowerCase().includes(q));
            }

            // 2. Sort
            if (currentSort === 'desc') {
                list.sort((a, b) => (b.beneficiaries || 0) - (a.beneficiaries || 0));
            } else if (currentSort === 'alpha') {
                list.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
            }

            const totalMatches = list.length;

            // 3. Slice Limit
            let sliced = list;
            if (currentLimit !== 'all') {
                const limitNum = parseInt(currentLimit, 10) || 10;
                sliced = list.slice(0, limitNum);
            }

            return {
                visible: sliced,
                totalMatches: totalMatches
            };
        }

        // Initialize Chart
        const initialFiltered = getFilteredBarangays();
        const initialLabels = initialFiltered.visible.map(b => b.name);
        const initialCounts = initialFiltered.visible.map(b => b.beneficiaries);
        const initialAmounts = initialFiltered.visible.map(b => b.amount);

        const barangayChart = new Chart(barangayCanvas, {
            type: 'bar',
            data: {
                labels: initialLabels.length ? initialLabels : ['No Data'],
                datasets: [{
                    label: 'Beneficiaries',
                    data: initialCounts.length ? initialCounts : [0],
                    backgroundColor: initialLabels.map((_, i) => colorPalette[i % colorPalette.length]),
                    borderColor: 'transparent',
                    borderWidth: 0,
                    borderRadius: 6,
                    maxBarThickness: 24,
                    minBarLength: 3
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal orientation ensures labels are never tilted or squashed
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 250 // Snappy update duration for smooth filtering
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const count = context.parsed.x;
                                const currentAmt = barangayChart._cachedAmounts && barangayChart._cachedAmounts[idx] !== undefined
                                    ? barangayChart._cachedAmounts[idx]
                                    : (initialAmounts[idx] || 0);
                                return [
                                    ` Beneficiaries: ${count.toLocaleString()} cases`,
                                    ` Assistance Granted: ${formatPHP(currentAmt)}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: '600' },
                            color: '#1E293B',
                            autoSkip: false // Never skip or drop barangay names
                        }
                    }
                }
            }
        });

        barangayChart._cachedAmounts = initialAmounts;

        function updateBarangayChart() {
            const { visible, totalMatches } = getFilteredBarangays();

            if (visible.length === 0) {
                if (emptyState) emptyState.classList.remove('d-none');
                if (barangayCanvas) barangayCanvas.style.display = 'none';
                if (showingText) showingText.textContent = 'No matching barangays found';
                if (activeCountBadge) activeCountBadge.textContent = '0 Matches';
                return;
            }

            if (emptyState) emptyState.classList.add('d-none');
            if (barangayCanvas) barangayCanvas.style.display = 'block';

            // Dynamic height allocation prevents overcrowding
            const optimalHeight = Math.max(340, visible.length * 30);
            if (chartInner) {
                chartInner.style.height = `${optimalHeight}px`;
            }

            const labels = visible.map(b => b.name);
            const counts = visible.map(b => b.beneficiaries);
            const amounts = visible.map(b => b.amount);

            barangayChart.data.labels = labels;
            barangayChart.data.datasets[0].data = counts;
            barangayChart.data.datasets[0].backgroundColor = labels.map((_, i) => colorPalette[i % colorPalette.length]);
            barangayChart._cachedAmounts = amounts;
            barangayChart.update('none');

            // Update UI status labels
            if (showingText) {
                if (currentLimit === 'all' || visible.length === totalMatches) {
                    showingText.textContent = `Showing all ${visible.length} matching barangays`;
                } else {
                    showingText.textContent = `Showing top ${visible.length} of ${totalMatches} barangays`;
                }
            }
            if (activeCountBadge) {
                activeCountBadge.textContent = `${totalMatches} Active`;
            }

            // Sync Leaderboard List
            if (listContainer) {
                const items = listContainer.querySelectorAll('.barangay-card-item');
                let visibleCount = 0;
                const q = searchQuery.trim().toLowerCase();

                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (!q || name.includes(q)) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (listEmpty) {
                    if (visibleCount === 0) {
                        listEmpty.classList.remove('d-none');
                    } else {
                        listEmpty.classList.add('d-none');
                    }
                }
                if (listCountText) {
                    listCountText.textContent = `${visibleCount} records`;
                }
            }
        }

        // Limit Toggle Buttons
        const limitButtons = document.querySelectorAll('.btn-barangay-limit');
        limitButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                limitButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentLimit = this.getAttribute('data-limit');
                updateBarangayChart();
            });
        });

        // Sort Button Toggle (Highest vs Alphabetical)
        const sortBtn = document.getElementById('btnSortBarangays');
        const sortLabel = document.getElementById('barangaySortLabel');
        if (sortBtn) {
            sortBtn.addEventListener('click', function () {
                if (currentSort === 'desc') {
                    currentSort = 'alpha';
                    if (sortLabel) sortLabel.textContent = 'A-Z';
                    sortBtn.querySelector('i').className = 'fas fa-sort-alpha-down me-1 text-success';
                } else {
                    currentSort = 'desc';
                    if (sortLabel) sortLabel.textContent = 'Highest';
                    sortBtn.querySelector('i').className = 'fas fa-sort-amount-down me-1 text-success';
                }
                updateBarangayChart();
            });
        }

        // Live Search Input
        const searchInput = document.getElementById('barangaySearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function (e) {
                searchQuery = e.target.value;
                updateBarangayChart();
            }, 150));
        }
    }

    /* ==========================================================
       2. Financial Assistance by Sector Chart (Highest to Lowest)
       ========================================================= */
    const sectorCanvas = document.getElementById('sectorBarChart');
    if (sectorCanvas) {
        const rawSectors = Array.isArray(data.sectorRanked) ? data.sectorRanked : [];
        let sectorLimit = 10; // 5, 10, or 'all'
        let sectorMetric = 'cases'; // 'cases' or 'amount'
        let sectorSearchQuery = '';

        const chartInner = document.getElementById('sectorChartInner');
        const emptyState = document.getElementById('sectorChartEmpty');
        const showingText = document.getElementById('sectorChartShowingText');
        const metricBadge = document.getElementById('sectorChartMetricBadge');
        const activeCountBadge = document.getElementById('sectorActiveCountBadge');
        const listEmpty = document.getElementById('sectorListEmpty');
        const listContainer = document.getElementById('sectorListContainer');

        function getFilteredSectors() {
            let list = rawSectors.slice();

            // 1. Search Query Filter
            if (sectorSearchQuery.trim().length > 0) {
                const q = sectorSearchQuery.trim().toLowerCase();
                list = list.filter(s => (s.sector || '').toLowerCase().includes(q));
            }

            // 2. Sort strictly highest to lowest according to selected metric
            if (sectorMetric === 'cases') {
                list.sort((a, b) => (b.beneficiaries || 0) - (a.beneficiaries || 0));
            } else {
                list.sort((a, b) => (b.amount || 0) - (a.amount || 0));
            }

            const totalMatches = list.length;

            // 3. Slice Limit
            let sliced = list;
            if (sectorLimit !== 'all') {
                const limitNum = parseInt(sectorLimit, 10) || 10;
                sliced = list.slice(0, limitNum);
            }

            return {
                visible: sliced,
                totalMatches: totalMatches
            };
        }

        // Initial Values
        const initialFiltered = getFilteredSectors();
        const initialLabels = initialFiltered.visible.map(s => s.sector);
        const initialValues = initialFiltered.visible.map(s => s.beneficiaries);
        const initialAmounts = initialFiltered.visible.map(s => s.amount);
        const initialCounts = initialFiltered.visible.map(s => s.beneficiaries);

        const sectorChart = new Chart(sectorCanvas, {
            type: 'bar',
            data: {
                labels: initialLabels.length ? initialLabels : ['No Data'],
                datasets: [{
                    label: 'Recipients',
                    data: initialValues.length ? initialValues : [0],
                    backgroundColor: initialLabels.map((_, i) => colorPalette[i % colorPalette.length]),
                    borderRadius: 6,
                    maxBarThickness: 24,
                    minBarLength: 3
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bars prevent crowded or overlapping sector names
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 250
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const isAmount = sectorMetric === 'amount';
                                const currentAmounts = sectorChart._cachedAmounts || initialAmounts;
                                const currentCounts = sectorChart._cachedCounts || initialCounts;

                                const amt = currentAmounts[idx] !== undefined ? currentAmounts[idx] : 0;
                                const count = currentCounts[idx] !== undefined ? currentCounts[idx] : 0;

                                return [
                                    ` Beneficiaries: ${count.toLocaleString()} cases`,
                                    ` Total Assistance: ${formatPHP(amt)}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: {
                            precision: 0,
                            font: { size: 11 },
                            callback: function (val) {
                                if (sectorMetric === 'amount') {
                                    if (val >= 1000000) return '₱' + (val / 1000000).toFixed(1) + 'M';
                                    if (val >= 1000) return '₱' + (val / 1000).toFixed(0) + 'k';
                                    return '₱' + val;
                                }
                                return val.toLocaleString();
                            }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: '600' },
                            color: '#1E293B',
                            autoSkip: false // Never skip or drop sector labels
                        }
                    }
                }
            }
        });

        sectorChart._cachedAmounts = initialAmounts;
        sectorChart._cachedCounts = initialCounts;

        function updateSectorChart() {
            const { visible, totalMatches } = getFilteredSectors();

            if (visible.length === 0) {
                if (emptyState) emptyState.classList.remove('d-none');
                if (sectorCanvas) sectorCanvas.style.display = 'none';
                if (showingText) showingText.textContent = 'No matching sectors found';
                if (activeCountBadge) activeCountBadge.textContent = '0 Matches';
                return;
            }

            if (emptyState) emptyState.classList.add('d-none');
            if (sectorCanvas) sectorCanvas.style.display = 'block';

            // Dynamic height allocation prevents overcrowding
            const optimalHeight = Math.max(260, visible.length * 32);
            if (chartInner) {
                chartInner.style.height = `${optimalHeight}px`;
            }

            const labels = visible.map(s => s.sector);
            const counts = visible.map(s => s.beneficiaries);
            const amounts = visible.map(s => s.amount);
            const values = sectorMetric === 'cases' ? counts : amounts;

            sectorChart.data.labels = labels;
            sectorChart.data.datasets[0].data = values;
            sectorChart.data.datasets[0].label = sectorMetric === 'cases' ? 'Recipients' : 'Grant Amount';
            sectorChart.data.datasets[0].backgroundColor = labels.map((_, i) => colorPalette[i % colorPalette.length]);
            sectorChart._cachedAmounts = amounts;
            sectorChart._cachedCounts = counts;
            sectorChart.update('none');

            // Update UI status labels
            if (showingText) {
                const metricName = sectorMetric === 'cases' ? 'cases' : 'grant amount';
                if (sectorLimit === 'all' || visible.length === totalMatches) {
                    showingText.textContent = `Showing all ${visible.length} sectors by ${metricName}`;
                } else {
                    showingText.textContent = `Showing top ${visible.length} of ${totalMatches} sectors by ${metricName}`;
                }
            }
            if (metricBadge) {
                metricBadge.textContent = sectorMetric === 'cases' ? 'Beneficiary Count' : 'Grant Amount (₱)';
            }
            if (activeCountBadge) {
                activeCountBadge.textContent = `${totalMatches} Sectors`;
            }

            // Sync Leaderboard List
            if (listContainer) {
                const items = listContainer.querySelectorAll('.sector-card-item');
                let visibleCount = 0;
                const q = sectorSearchQuery.trim().toLowerCase();

                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (!q || name.includes(q)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (listEmpty) {
                    if (visibleCount === 0) {
                        listEmpty.classList.remove('d-none');
                    } else {
                        listEmpty.classList.add('d-none');
                    }
                }
            }
        }

        // Limit Toggle Buttons
        const limitButtons = document.querySelectorAll('.btn-sector-limit');
        limitButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                limitButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                sectorLimit = this.getAttribute('data-limit');
                updateSectorChart();
            });
        });

        // Metric Toggle Buttons (Cases vs Grants)
        const metricButtons = document.querySelectorAll('.btn-sector-metric');
        metricButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                metricButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                sectorMetric = this.getAttribute('data-metric');
                updateSectorChart();
            });
        });

        // Live Search Input
        const searchInput = document.getElementById('sectorSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function (e) {
                sectorSearchQuery = e.target.value;
                updateSectorChart();
            }, 150));
        }
    }

    /* ==========================================================
       3. Male vs Female Beneficiaries Donut Chart
       ========================================================== */
    const genderCanvas = document.getElementById('genderDonutChart');
    if (genderCanvas) {
        const genderLabels = Array.isArray(data.genderLabels) && data.genderLabels.length ? data.genderLabels : ['Male', 'Female'];
        const genderCounts = Array.isArray(data.genderCounts) && data.genderCounts.length ? data.genderCounts : [0, 0];
        const totalGender = genderCounts.reduce((acc, curr) => acc + curr, 0);

        new Chart(genderCanvas, {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderCounts,
                    backgroundColor: [
                        '#2563EB', // Blue for Male
                        '#E11D48', // Coral / Rose for Female
                        '#94A3B8'  // Slate for Other / Unspecified
                    ],
                    borderColor: '#FFFFFF',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            padding: 14,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const val = context.parsed;
                                const pct = totalGender > 0 ? ((val / totalGender) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val.toLocaleString()} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    /* ==========================================================
       4. Most Common Medical Concerns & Reasons (Scalable & Responsive)
       ========================================================== */
    const medicalCanvas = document.getElementById('medicalBarChart');
    if (medicalCanvas) {
        const rawMedical = Array.isArray(data.medicalRanked) ? data.medicalRanked : [];
        let medicalLimit = 10; // 5, 10, 20, or 'all'
        let medicalMetric = 'cases'; // 'cases' or 'amount'
        let medicalSearchQuery = '';

        const chartInner = document.getElementById('medicalChartInner');
        const emptyState = document.getElementById('medicalChartEmpty');
        const showingText = document.getElementById('medicalChartShowingText');
        const activeCountBadge = document.getElementById('medicalActiveCountBadge');
        const listCountText = document.getElementById('medicalListCountText');
        const listEmpty = document.getElementById('medicalListEmpty');
        const listContainer = document.getElementById('medicalListContainer');

        // Standard institutional blue for consistent, professional government visualization
        const medicalBarColor = '#1E40AF';
        const medicalBarHoverColor = '#1D4ED8';

        function getFilteredMedical() {
            let list = rawMedical.slice();

            // 1. Live Search Query Filter
            if (medicalSearchQuery.trim().length > 0) {
                const q = medicalSearchQuery.trim().toLowerCase();
                list = list.filter(m => (m.concern || '').toLowerCase().includes(q));
            }

            // 2. Sort strictly highest to lowest according to selected metric
            if (medicalMetric === 'cases') {
                list.sort((a, b) => (b.beneficiaries || 0) - (a.beneficiaries || 0));
            } else {
                list.sort((a, b) => (b.amount || 0) - (a.amount || 0));
            }

            const totalMatches = list.length;

            // 3. Slice Limit
            let sliced = list;
            if (medicalLimit !== 'all') {
                const limitNum = parseInt(medicalLimit, 10) || 10;
                sliced = list.slice(0, limitNum);
            }

            return {
                visible: sliced,
                totalMatches: totalMatches
            };
        }

        // Initial Values
        const initialFiltered = getFilteredMedical();
        const initialLabels = initialFiltered.visible.map(m => m.concern);
        const initialValues = initialFiltered.visible.map(m => m.beneficiaries);
        const initialAmounts = initialFiltered.visible.map(m => m.amount);
        const initialCounts = initialFiltered.visible.map(m => m.beneficiaries);

        const medicalChart = new Chart(medicalCanvas, {
            type: 'bar',
            data: {
                labels: initialLabels.length ? initialLabels : ['No Data'],
                datasets: [{
                    label: 'Beneficiaries',
                    data: initialValues.length ? initialValues : [0],
                    backgroundColor: medicalBarColor,
                    hoverBackgroundColor: medicalBarHoverColor,
                    borderRadius: 4,
                    maxBarThickness: 22,
                    minBarLength: 3
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bars prevent crowded or overlapping labels
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 250
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const currentAmounts = medicalChart._cachedAmounts || initialAmounts;
                                const currentCounts = medicalChart._cachedCounts || initialCounts;

                                const amt = currentAmounts[idx] !== undefined ? currentAmounts[idx] : 0;
                                const count = currentCounts[idx] !== undefined ? currentCounts[idx] : 0;

                                return [
                                    ` Beneficiaries: ${count.toLocaleString()} cases`,
                                    ` Total Assistance: ${formatPHP(amt)}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: {
                            precision: 0,
                            font: { size: 11 },
                            callback: function (val) {
                                if (medicalMetric === 'amount') {
                                    if (val >= 1000000) return '₱' + (val / 1000000).toFixed(1) + 'M';
                                    if (val >= 1000) return '₱' + (val / 1000).toFixed(0) + 'k';
                                    return '₱' + val;
                                }
                                return val.toLocaleString();
                            }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: '600' },
                            color: '#1E293B',
                            autoSkip: false // Never skip or drop concern labels
                        }
                    }
                }
            }
        });

        medicalChart._cachedAmounts = initialAmounts;
        medicalChart._cachedCounts = initialCounts;

        function updateMedicalChart() {
            const { visible, totalMatches } = getFilteredMedical();

            if (visible.length === 0) {
                if (emptyState) emptyState.classList.remove('d-none');
                if (medicalCanvas) medicalCanvas.style.display = 'none';
                if (showingText) showingText.textContent = 'No matching medical concerns found';
                if (activeCountBadge) activeCountBadge.textContent = '0 Matches';
            } else {
                if (emptyState) emptyState.classList.add('d-none');
                if (medicalCanvas) medicalCanvas.style.display = 'block';

                // Dynamic height allocation prevents overcrowding
                const optimalHeight = Math.max(320, visible.length * 32);
                if (chartInner) {
                    chartInner.style.height = `${optimalHeight}px`;
                }

                const labels = visible.map(m => m.concern);
                const counts = visible.map(m => m.beneficiaries);
                const amounts = visible.map(m => m.amount);
                const values = medicalMetric === 'cases' ? counts : amounts;

                medicalChart.data.labels = labels;
                medicalChart.data.datasets[0].data = values;
                medicalChart.data.datasets[0].label = medicalMetric === 'cases' ? 'Beneficiaries' : 'Grant Amount';
                medicalChart.data.datasets[0].backgroundColor = medicalBarColor;
                medicalChart.data.datasets[0].hoverBackgroundColor = medicalBarHoverColor;
                medicalChart._cachedAmounts = amounts;
                medicalChart._cachedCounts = counts;
                medicalChart.update('none');

                // Update UI status labels
                if (showingText) {
                    const metricName = medicalMetric === 'cases' ? 'cases' : 'grant amount';
                    if (medicalLimit === 'all' || visible.length === totalMatches) {
                        showingText.textContent = `Showing all ${visible.length} concerns by ${metricName}`;
                    } else {
                        showingText.textContent = `Showing top ${visible.length} of ${totalMatches} concerns by ${metricName}`;
                    }
                }
                if (activeCountBadge) {
                    activeCountBadge.textContent = `${totalMatches} Concerns`;
                }
            }

            // Sync Table List
            if (listContainer) {
                const items = listContainer.querySelectorAll('.medical-card-item');
                let visibleCount = 0;
                const q = medicalSearchQuery.trim().toLowerCase();

                items.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (!q || name.includes(q)) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (listEmpty) {
                    if (visibleCount === 0) {
                        listEmpty.classList.remove('d-none');
                    } else {
                        listEmpty.classList.add('d-none');
                    }
                }
                if (listCountText) {
                    listCountText.textContent = `${visibleCount} records`;
                }
            }
        }

        // Limit Toggle Buttons (Top 5 / Top 10 / Top 20 / All)
        const limitButtons = document.querySelectorAll('.btn-medical-limit');
        limitButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                limitButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                medicalLimit = this.getAttribute('data-limit');
                updateMedicalChart();
            });
        });

        // Metric Toggle Buttons (Cases vs Grants)
        const metricButtons = document.querySelectorAll('.btn-medical-metric');
        metricButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                metricButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                medicalMetric = this.getAttribute('data-metric');
                updateMedicalChart();
            });
        });

        // Live Search Input
        const searchInput = document.getElementById('medicalSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function (e) {
                medicalSearchQuery = e.target.value;
                updateMedicalChart();
            }, 150));
        }
    }
});
