document.addEventListener('DOMContentLoaded', function() {
    // Filter dropdown functionality
    const filterButton = document.getElementById('filterButton');
    const filterDropdown = document.getElementById('filterDropdown');
    const applyFiltersButton = document.getElementById('applyFilters');
    const resetFiltersButton = document.getElementById('resetFilters');
    const roleCheckboxes = document.querySelectorAll('input[name="role"]');
    const barangayCheckboxes = document.querySelectorAll('input[name="barangay"]');
    const userRows = document.querySelectorAll('.users-table tbody tr');
    const userSearch = document.getElementById('user-search');

    // Toggle filter dropdown
    if (filterButton && filterDropdown) {
        filterButton.addEventListener('click', function(e) {
            e.stopPropagation();
            filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
            filterDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!filterDropdown.contains(e.target) && e.target !== filterButton) {
                filterDropdown.style.display = 'none';
                filterDropdown.classList.remove('show');
            }
        });
    }

    // Handle role checkboxes (only one can be selected at a time)
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && this.value !== 'All') {
                roleCheckboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            } else if (this.value === 'All' && this.checked) {
                roleCheckboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            }
        });
    });

    // Handle barangay checkboxes
    barangayCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.value === 'All' && this.checked) {
                // If 'All' is checked, uncheck other barangays
                barangayCheckboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            } else if (this.checked) {
                // If a specific barangay is checked, uncheck 'All'
                document.querySelector('input[name="barangay"][value="All"]').checked = false;
            } else if (!document.querySelector('input[name="barangay"]:checked')) {
                // If no barangay is selected, check 'All'
                document.querySelector('input[name="barangay"][value="All"]').checked = true;
            }
        });
    });

    // Apply filters
    function applyFilters() {
        const selectedRoles = Array.from(roleCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
            
        const selectedBarangays = Array.from(barangayCheckboxes)
            .filter(cb => cb.checked && cb.value !== 'All')
            .map(cb => cb.value);
            
        const searchTerm = userSearch ? userSearch.value.trim().toLowerCase() : '';
        
        userRows.forEach(row => {
            const roleCell = row.cells[2]; // Role is in the 3rd column (index 2)
            const barangayCell = row.cells[3]; // Barangay is in the 4th column (index 3)
            const nameCell = row.cells[0]; // Name is in the 1st column (index 0)
            const emailCell = row.cells[1]; // Email is in the 2nd column (index 1)
            
            const role = roleCell ? roleCell.textContent.trim() : '';
            const barangay = barangayCell ? barangayCell.textContent.trim() : '';
            const name = nameCell ? nameCell.textContent.trim().toLowerCase() : '';
            const email = emailCell ? emailCell.textContent.trim().toLowerCase() : '';
            
            // Check if row matches all filters
            const matchesRole = selectedRoles.length === 0 || selectedRoles.includes('All') || selectedRoles.some(r => role.includes(r));
            const matchesBarangay = selectedBarangays.length === 0 || selectedBarangays.some(b => barangay.includes(b));
            const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
            
            // Show/hide row based on filters
            row.style.display = (matchesRole && matchesBarangay && matchesSearch) ? '' : 'none';
        });
        
        // Close dropdown after applying filters
        if (filterDropdown) {
            filterDropdown.style.display = 'none';
            filterDropdown.classList.remove('show');
        }
        
        // Update pagination info if the function exists
        if (typeof updatePaginationInfo === 'function') {
            const visibleCount = document.querySelectorAll('.users-table tbody tr:not([style*="display: none"])').length;
            updatePaginationInfo(visibleCount);
        }
    }

    // Apply filters when clicking the apply button
    if (applyFiltersButton) {
        applyFiltersButton.addEventListener('click', applyFilters);
    }

    // Reset filters
    if (resetFiltersButton) {
        resetFiltersButton.addEventListener('click', function() {
            // Reset checkboxes
            roleCheckboxes.forEach(cb => {
                if (cb.value === 'All') cb.checked = true;
                else cb.checked = false;
            });
            
            barangayCheckboxes.forEach(cb => {
                if (cb.value === 'All') cb.checked = true;
                else cb.checked = false;
            });
            
            // Reset search
            if (userSearch) userSearch.value = '';
            
            // Apply reset filters
            applyFilters();
        });
    }
    
    // Apply filters when pressing Enter in search
    if (userSearch) {
        userSearch.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }

    // Print functionality for export list
    const exportButton = document.querySelector('.export-button');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            // Create a print-friendly version of the table
            const printWindow = window.open('', '_blank');
            
            // Get the current date and time for the print header
            const now = new Date();
            const formattedDate = now.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Clone the table and clean it up for printing
            const table = document.querySelector('.users-table').cloneNode(true);
            
            // Remove action buttons from the print version
            const actionCells = table.querySelectorAll('td:last-child, th:last-child');
            actionCells.forEach(cell => cell.remove());
            
            // Create print content
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Archived Users - ${formattedDate}</title>
                    <style>
                        @page { size: landscape; margin: 1cm; }
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                        h1 { color: #6a1b9a; margin: 0 0 10px 0; }
                        .print-header { margin-bottom: 20px; }
                        .print-date { color: #666; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th { background-color: #6a1b9a; color: white; text-align: left; padding: 10px; }
                        td { padding: 8px; border-bottom: 1px solid #ddd; }
                        tr:nth-child(even) { background-color: #f9f9f9; }
                        @media print {
                            .no-print { display: none; }
                            button { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h1>Archived Users</h1>
                        <div class="print-date">Generated on: ${formattedDate}</div>
                    </div>
                    ${table.outerHTML}
                    <div class="no-print" style="margin-top: 20px; text-align: center;">
                        <button onclick="window.print()" style="padding: 8px 16px; background-color: #6a1b9a; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
                            Print
                        </button>
                        <button onclick="window.close()" style="padding: 8px 16px; background-color: #666; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Close
                        </button>
                    </div>
                </body>
                </html>
            `;
            
            // Write the content and open print dialog
            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Auto-print when the window loads
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                }, 250);
            };
        });
    }
});
