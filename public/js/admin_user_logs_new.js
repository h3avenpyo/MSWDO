document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary DOM elements
    const searchInput = document.getElementById('log-search');
    const searchButton = document.getElementById('search-button');
    const clearButton = document.querySelector('.clear-search');
    
    // Initialize the table to show all rows
    function initializeTable() {
        const tableRows = document.querySelectorAll('.logs-table tbody tr:not(.no-results-row)');
        tableRows.forEach(row => {
            row.style.display = '';
        });
        
        // Remove any existing no-results rows
        document.querySelectorAll('.no-results-row').forEach(row => row.remove());
    }
    
    // Filter logs based on search term only
    function filterLogs() {
        const tableRows = document.querySelectorAll('.logs-table tbody tr:not(.no-results-row)');
        const searchTerm = searchInput.value.trim().toLowerCase();
        
        let hasVisibleRows = false;
        
        // Remove any existing no-results rows
        document.querySelectorAll('.no-results-row').forEach(row => row.remove());
        
        tableRows.forEach(row => {
            // Get all text content from the row
            const rowText = row.textContent.toLowerCase();
            
            // Check if the row matches the search term
            const shouldShow = searchTerm === '' || rowText.includes(searchTerm);
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
    
    // Event Listeners
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            filterLogs();
        });
    }
    
    // Also filter when pressing Enter in the search input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterLogs();
            }
        });
        
        // Dynamic search - filter as user types
        searchInput.addEventListener('input', function() {
            if (clearButton) {
                clearButton.style.display = this.value ? 'block' : 'none';
            }
            // Trigger filtering on input for instant results
            // If search is empty, reset to show all logs
            if (this.value.trim() === '') {
                initializeTable();
            } else {
                filterLogs();
            }
        });
    }
    
    // Clear search
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                this.style.display = 'none';
                initializeTable();
                searchInput.focus();
            }
        });
    }
    
    // Initialize the table
    initializeTable();
});