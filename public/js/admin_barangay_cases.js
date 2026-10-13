document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('barangay-table-body');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const searchInput = document.getElementById('barangay-search');
    const filterToggle = document.getElementById('filter-toggle');
    const filterDropdown = document.getElementById('filter-dropdown');
    const sortToggle = document.getElementById('sort-toggle');
    const sortDropdown = document.getElementById('sort-dropdown');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const sortBySelect = document.getElementById('sort-by');
    const sortButtons = document.querySelectorAll('.sort-btn');
    
    if (!tableBody || !loadMoreBtn || !searchInput || !filterToggle || !sortToggle) {
        console.error('Required elements not found');
        return;
    }
    
    // Pagination and filter variables
    const itemsPerPage = 15;
    let currentPage = 1;
    let allBarangays = [];
    let filteredBarangays = [];
    let currentFilters = {
        status: '',
        dateFrom: '',
        dateTo: ''
    };
    let currentSort = {
        field: 'name',
        order: 'asc'
    };
    
    // Toggle filter dropdown
    filterToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
        sortDropdown.style.display = 'none';
    });
    
    // Toggle sort dropdown
    sortToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        sortDropdown.style.display = sortDropdown.style.display === 'block' ? 'none' : 'block';
        filterDropdown.style.display = 'none';
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        filterDropdown.style.display = 'none';
        sortDropdown.style.display = 'none';
    });
    
    // Prevent dropdown from closing when clicking inside
    [filterDropdown, sortDropdown].forEach(dropdown => {
        if (dropdown) {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    
    // Apply filters
    applyFiltersBtn.addEventListener('click', function() {
        currentFilters = {
            status: document.getElementById('status-filter').value,
            dateFrom: document.getElementById('date-from').value,
            dateTo: document.getElementById('date-to').value
        };
        filterBarangays();
        filterDropdown.style.display = 'none';
    });
    
    // Reset filters
    resetFiltersBtn.addEventListener('click', function() {
        document.getElementById('status-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        currentFilters = { status: '', dateFrom: '', dateTo: '' };
        filterBarangays();
    });
    
    // Handle sort field selection
    sortBySelect.addEventListener('change', function() {
        currentSort.field = this.value;
        sortBarangays();
    });
    
    // Handle sort order buttons
    sortButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentSort.order = this.getAttribute('data-order');
            sortBarangays();
            
            // Update active state
            sortButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Filter barangays based on current filters
    function filterBarangays() {
        filteredBarangays = [...allBarangays];
        
        // Apply status filter
        if (currentFilters.status) {
            filteredBarangays = filteredBarangays.filter(barangay => {
                if (currentFilters.status === 'Active') {
                    return barangay.active_cases > 0;
                } else if (currentFilters.status === 'Resolved') {
                    return barangay.resolved_cases > 0;
                }
                return true;
            });
        }
        
        // Apply date range filter (if we have case dates in the barangay data)
        if (currentFilters.dateFrom || currentFilters.dateTo) {
            // This would require fetching the cases for each barangay with the date filter
            // For now, we'll just show a message that date filtering is not fully implemented
            console.log('Date range filtering would be applied here');
        }
        
        // Reset pagination
        currentPage = 1;
        
        // Re-apply search and sort
        applySearch();
        sortBarangays();
    }
    
    // Show loading state
    function showLoading() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p style="margin-top: 10px; color: #6c757d;">Loading barangay data...</p>
                </td>
            </tr>`;
    }
    
    // Show error state
    function showError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #dc3545;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>${message || 'Failed to load barangay data. Please try again.'}</p>
                    <button onclick="window.location.reload()" class="btn btn-primary mt-2">
                        <i class="fas fa-sync-alt"></i> Retry
                    </button>
                </td>
            </tr>`;
    }
    
    // Function to render barangay rows
    function renderBarangays() {
        const startIndex = 0; // Always start from the beginning
        const endIndex = currentPage * itemsPerPage;
        const paginatedItems = filteredBarangays.slice(startIndex, endIndex);
        
        // Clear and rebuild the table body
        tableBody.innerHTML = '';
        
        if (paginatedItems.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 30px; color: #6c757d;">
                    <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: 10px;"></i>
                    <p>No barangays found</p>
                </td>
            `;
            tableBody.appendChild(row);
            loadMoreBtn.style.display = 'none';
            return;
        }
        
        console.log('About to render', paginatedItems.length, 'items');

        paginatedItems.forEach(item => {
            console.log('Rendering barangay item:', item); // Debug log
            const row = document.createElement('tr');
            row.className = 'barangay-row';
            row.style.cursor = 'pointer';
            row.style.borderBottom = '1px solid #f0f0f0';
            row.style.transition = 'background-color 0.2s';
            
            row.innerHTML = `
                <td style="padding: 15px 20px;">
                    <span style="font-weight:600; color:#4a148c;">${item.name}</span>
                </td>
                <td style="padding: 15px 20px; text-align: center; color: #ffc107; font-weight: 600;">${item.case_reported || 0}</td>
                <td style="padding: 15px 20px; text-align: center; color: #fd7e14; font-weight: 600;">${item.case_referred || 0}</td>
                <td style="padding: 15px 20px; text-align: center; color: #28a745; font-weight: 600;">${item.case_filed || 0}</td>
                <td style="padding: 15px 20px; text-align: center; font-weight: 700; color: #6a1b9a;">${item.total_cases || 0}</td>
                <td style="padding: 15px 20px; text-align: center;">
                    <button class="btn-view" data-barangay-id="${item.id}" style="background-color: #6a1b9a; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; display: inline-block; min-width: 100px;">
                        VIEW CASES
                    </button>
                </td>
            `;
            
            // Add hover effect
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f9f9f9';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
            
            // Add click handler for the View Cases button
            const viewButtons = row.querySelectorAll('.btn-view');
            viewButtons.forEach(btn => {
                // Add hover effects
                btn.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#5a1585';
                    this.style.transform = 'translateY(-1px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '#6a1b9a';
                    this.style.transform = 'translateY(0)';
                });
                
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const barangayId = item.id;
                    try {
                        const tmpl = (window && window.BarangayShowUrlTemplate) ? window.BarangayShowUrlTemplate : null;
                        const url = tmpl ? tmpl.replace('__ID__', encodeURIComponent(barangayId)) : `/admin-barangay-cases/${barangayId}`;
                        window.location.href = url;
                    } catch (_) {
                        window.location.href = `/admin-barangay-cases/${barangayId}`;
                    }
                });
            });
            
            console.log('Adding row to table for:', item.name);
            tableBody.appendChild(row);
            console.log('Row added successfully');
        });
        
        // Show/hide load more button
        if (endIndex >= filteredBarangays.length) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'flex';
        }
    }
    
    // Fetch barangay cases data from API with filters
    async function fetchBarangayCases() {
        showLoading();
        
        try {
            // Build query parameters from current filters
            const params = new URLSearchParams();
            
            if (currentFilters.status) params.append('status', currentFilters.status);
            if (currentFilters.dateFrom) params.append('date_from', currentFilters.dateFrom);
            if (currentFilters.dateTo) params.append('date_to', currentFilters.dateTo);
            
            const url = `/api/barangay-cases?${params.toString()}`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Fetched barangay cases:', data);
            console.log('Response status:', response.status);
            console.log('Data type:', typeof data);
            console.log('Is array:', Array.isArray(data));
            
            allBarangays = Array.isArray(data) ? data : [];
            console.log('All barangays count:', allBarangays.length);
            
            // Apply search and sort
            applySearch();
            sortBarangays();
            
        } catch (error) {
            console.error('Error fetching barangay cases:', error);
            showError('Failed to load barangay data. Please check your connection and try again.');
        }
    }
    
    // Apply search filter
    function applySearch() {
        const searchTerm = searchInput.value.toLowerCase();
        
        if (searchTerm.trim() === '') {
            filteredBarangays = [...allBarangays];
        } else {
            filteredBarangays = allBarangays.filter(barangay => 
                (barangay.name && barangay.name.toLowerCase().includes(searchTerm)) ||
                (barangay.code && barangay.code.toLowerCase().includes(searchTerm))
            );
        }
        
        // Apply current sort
        sortBarangays();
    }
    
    // Sort barangays based on current sort settings
    function sortBarangays() {
        const field = currentSort.field;
        const order = currentSort.order;
        
        filteredBarangays.sort((a, b) => {
            let comparison = 0;
            
            // Handle different field types
            if (field === 'name' || field === 'code') {
                // String comparison
                const aValue = a[field] ? a[field].toLowerCase() : '';
                const bValue = b[field] ? b[field].toLowerCase() : '';
                comparison = aValue.localeCompare(bValue);
            } else {
                // Numeric comparison
                comparison = (a[field] || 0) - (b[field] || 0);
            }
            
            // Apply sort order
            return order === 'asc' ? comparison : -comparison;
        });
        
        // Reset pagination
        currentPage = 1;
        
        // Re-render the table
        renderBarangays();
    }
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applySearch();
        });
    }
    
    // Load more button click handler
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            renderBarangays();
        });
    }
    
    // Initial data fetch
    if (typeof fetchBarangayCases === 'function') {
        fetchBarangayCases();
    }
});