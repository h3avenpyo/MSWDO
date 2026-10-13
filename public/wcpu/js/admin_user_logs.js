document.addEventListener('DOMContentLoaded', function() {

    // Toggle filter dropdown
    const actionFilterBtn = document.getElementById('actionFilterBtn');
    const actionFilterDropdown = document.getElementById('actionFilterDropdown');
    
    if (actionFilterBtn && actionFilterDropdown) {
        actionFilterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            actionFilterDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            actionFilterDropdown.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside it
        actionFilterDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    
    // Search and filter functionality
    const searchInput = document.getElementById('log-search');
    const actionCheckboxes = document.querySelectorAll('input[name="action"]');
    const tableRows = document.querySelectorAll('.logs-table tbody tr');
    const clearButton = document.querySelector('.clear-search');
    
    function filterLogs() {
        const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const selectedActions = Array.from(actionCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value.toLowerCase());
        
        let hasVisibleRows = false;
        
        // Remove any existing no-results rows
        document.querySelectorAll('.no-results-row').forEach(row => row.remove());
        
        tableRows.forEach(row => {
            // Skip if it's a no-results row
            if (row.classList.contains('no-results-row')) {
                return;
            }

            // Get user name from the user cell (4th cell - index 3)
            const userCell = row.cells[3];
            const userName = userCell?.textContent.trim().toLowerCase() || '';
            
            // Get date from the date cell (1st cell - index 0)
            const dateCell = row.cells[0];
            const dateText = dateCell?.textContent.trim().toLowerCase() || '';
            
            // Get action from the action cell (3rd cell - index 2)
            const actionCell = row.cells[2];
            const action = actionCell?.textContent.trim().toLowerCase() || '';
            
            // Check if the row matches the search term (either in username or date)
            const matchesSearch = searchTerm === '' || 
                userName.includes(searchTerm) || 
                dateText.includes(searchTerm);
                
            // Check if the row matches the selected action filters
            const matchesAction = selectedActions.length === 0 || 
                selectedActions.some(selectedAction => action.includes(selectedAction));
                
            // Show/hide row based on search and filter
            const shouldShow = matchesSearch && matchesAction;
            row.style.display = shouldShow ? '' : 'none';
            
            if (shouldShow) {
                hasVisibleRows = true;
            }
        });
        
        // Show no results message if no rows are visible
        if (!hasVisibleRows) {
            const tbody = document.querySelector('.logs-table tbody');
            if (tbody) {
                const newRow = document.createElement('tr');
                newRow.className = 'no-results-row';
                newRow.innerHTML = `
                    <td colspan="5" class="text-center">
                        <i class="fas fa-search" style="font-size: 24px; margin-bottom: 10px; display: block; color: #6a1b9a; opacity: 0.7;"></i>
                        <p>No matching logs found. Try adjusting your search or filters.</p>
                    </td>
                `;
                tbody.appendChild(newRow);
            }
        }
    }
    
    // Initialize event listeners
    if (searchInput) {
        // Handle search input
        searchInput.addEventListener('input', function() {
            filterLogs();
            // Show/hide clear button based on input
            if (clearButton) {
                clearButton.style.display = this.value ? 'block' : 'none';
            }
        });
        
        // Handle clear button click
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                filterLogs();
                searchInput.focus();
            });
        }
    }
    
    // Handle action filter changes
    actionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterLogs);
    });
    
    // Export button functionality
    const exportButton = document.getElementById('exportButton');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            // In a real app, this would trigger a file download
            alert('Exporting logs... This would download a CSV file in a real application.');
        });
    }
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }
    
    // Initial filter to handle any pre-filled values
    filterLogs();
});
