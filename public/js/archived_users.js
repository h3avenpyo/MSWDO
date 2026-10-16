document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    const modal = document.getElementById('userDetailsModal');
    const closeModalBtns = document.querySelectorAll('.close-modal, .close-modal-btn');
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const header = document.querySelector('.dashboard-header');
    
    // View button click handler - using event delegation for dynamic content
    document.addEventListener('click', function(e) {
        // Check if the clicked element or its parent has the view-button class
        const viewButton = e.target.closest('.view-button, .view-button i');
        if (!viewButton) return;
        
        e.preventDefault();
        const button = viewButton.classList.contains('view-button') ? viewButton : viewButton.closest('.view-button');
        const row = button.closest('tr');
        
        // Get all cells in the row
        const cells = row.querySelectorAll('td');
        if (cells.length < 6) {
            console.error('Not enough cells in the row');
            return;
        }
        
        // Extract user data from the row
        const userData = {
            name: row.querySelector('.user-name-text') ? row.querySelector('.user-name-text').textContent : 'N/A',
            id: row.querySelector('.user-email') ? row.querySelector('.user-email').textContent.replace('ID: ', '') : 'N/A',
            email: cells[1] ? cells[1].textContent.trim() : 'N/A',
            role: cells[2] ? cells[2].textContent.trim() : 'N/A',
            barangay: cells[3] ? cells[3].textContent.trim() : 'N/A',
            reason: cells[4] ? cells[4].textContent.trim() : 'N/A',
            archivedDate: cells[5] ? cells[5].textContent.trim() : 'N/A'
        };
        
        console.log('User data extracted:', userData);
        
        // Populate modal with user data
        ['name', 'id', 'email', 'role', 'barangay', 'reason', 'archived-date'].forEach(field => {
            const element = document.getElementById(`detail-${field}`);
            const value = userData[field === 'archived-date' ? 'archivedDate' : field];
            if (element) element.textContent = value || 'N/A';
        });
        
        // Show the modal
        console.log('Showing modal');
        modal.style.display = 'flex';
        // Force reflow
        void modal.offsetHeight;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    });
    
    // Close modal handlers
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
    
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeModal();
        });
    });
    
    // Close modal when clicking outside the content
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
    
    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
    });
    
    // Scroll-aware header
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && scrollTop > 70) {
            // Scrolling down & past the header height
            header.classList.add('hidden');
        } else {
            // Scrolling up
            header.classList.remove('hidden');
        }
        
        lastScrollTop = scrollTop;
    });
    
    // User search functionality
    const userSearch = document.getElementById('user-search');
    const userRows = document.querySelectorAll('.users-table tbody tr');
    const emptyState = document.querySelector('.empty-state');
    const tableResponsive = document.querySelector('.table-responsive');
    const pagination = document.querySelector('.pagination');
    
    userSearch.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        let matchCount = 0;
        
        userRows.forEach(row => {
            const userName = row.querySelector('.user-name-text').textContent.toLowerCase();
            const userEmail = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const userId = row.querySelector('.user-email').textContent.toLowerCase();
            const reason = row.querySelector('.archive-reason').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || 
                userEmail.includes(searchTerm) || 
                userId.includes(searchTerm) ||
                reason.includes(searchTerm)) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show empty state if no matches
        if (matchCount === 0) {
            tableResponsive.style.display = 'none';
            pagination.style.display = 'none';
            emptyState.style.display = 'block';
            emptyState.querySelector('.empty-state-title').textContent = 'No Matching Results';
            emptyState.querySelector('.empty-state-text').textContent = 'No archived users match your search criteria. Try a different search term.';
        } else {
            tableResponsive.style.display = 'block';
            pagination.style.display = 'flex';
            emptyState.style.display = 'none';
        }
    });
    
    // Action button handlers
    const viewButtons = document.querySelectorAll('.view-button');
    const restoreButtons = document.querySelectorAll('.restore-button');
    const deleteButtons = document.querySelectorAll('.delete-button');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userName = this.closest('tr').querySelector('.user-name-text').textContent;
            alert(`Viewing details for ${userName}`);
        });
    });
    
    restoreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userName = this.closest('tr').querySelector('.user-name-text').textContent;
            if (confirm(`Are you sure you want to restore ${userName} to active users?`)) {
                alert(`${userName} has been restored to active users.`);
                this.closest('tr').remove();
                
                // Check if table is empty
                if (document.querySelectorAll('.users-table tbody tr').length === 0) {
                    tableResponsive.style.display = 'none';
                    pagination.style.display = 'none';
                    emptyState.style.display = 'block';
                    emptyState.querySelector('.empty-state-title').textContent = 'No Archived Users';
                    emptyState.querySelector('.empty-state-text').textContent = 'There are currently no archived users in the system. When users are archived, they will appear here.';
                }
            }
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userName = this.closest('tr').querySelector('.user-name-text').textContent;
            if (confirm(`WARNING: Are you sure you want to permanently delete ${userName}? This action cannot be undone.`)) {
                alert(`${userName} has been permanently deleted from the system.`);
                this.closest('tr').remove();
                
                // Check if table is empty
                if (document.querySelectorAll('.users-table tbody tr').length === 0) {
                    tableResponsive.style.display = 'none';
                    pagination.style.display = 'none';
                    emptyState.style.display = 'block';
                    emptyState.querySelector('.empty-state-title').textContent = 'No Archived Users';
                    emptyState.querySelector('.empty-state-text').textContent = 'There are currently no archived users in the system. When users are archived, they will appear here.';
                }
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Rotate the icon
            const icon = this.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                } else {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                }
            }
            
            // Save sidebar state in localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Load sidebar state from localStorage
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            const icon = toggleSidebar.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            }
        }
    }
    
    // Search functionality
    const userSearch = document.getElementById('user-search');
    if (userSearch) {
        userSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#users-table-body tr');
            let visibleRows = 0;
            
            rows.forEach(row => {
                const userName = row.querySelector('.user-name').textContent.toLowerCase();
                const userEmail = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const userRole = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (userName.includes(searchTerm) || 
                    userEmail.includes(searchTerm) || 
                    userRole.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update pagination info
            updatePaginationInfo(visibleRows);
        });
    }
    
    // Restore user button click handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-restore')) {
            const button = e.target.closest('.btn-restore');
            const row = button.closest('tr');
            const userName = row.querySelector('.user-name').textContent;
            
            if (confirm(`Are you sure you want to restore ${userName}?`)) {
                // Add loading state
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';
                
                // Simulate API call
                setTimeout(() => {
                    // Remove the row on success
                    row.remove();
                    
                    // Show success message
                    showToast('User restored successfully', 'success');
                    
                    // Update pagination info
                    updatePaginationInfo(document.querySelectorAll('#users-table-body tr:not([style*="display: none"])').length);
                }, 1000);
            }
        }
        
        // Delete user button click handler
        if (e.target.closest('.btn-delete')) {
            const button = e.target.closest('.btn-delete');
            const row = button.closest('tr');
            const userName = row.querySelector('.user-name').textContent;
            
            if (confirm(`WARNING: This will permanently delete ${userName}. This action cannot be undone.`)) {
                if (prompt(`Type "DELETE" to confirm permanent deletion of ${userName}`) === 'DELETE') {
                    // Add loading state
                    const originalText = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                    
                    // Simulate API call
                    setTimeout(() => {
                        // Remove the row on success
                        row.remove();
                        
                        // Show success message
                        showToast('User permanently deleted', 'success');
                        
                        // Update pagination info
                        updatePaginationInfo(document.querySelectorAll('#users-table-body tr:not([style*="display: none"])').length);
                    }, 1000);
                } else {
                    showToast('Deletion cancelled', 'error');
                }
            }
        }
        
        // Pagination button click handler
        if (e.target.closest('.page-button')) {
            const button = e.target.closest('.page-button');
            if (button.disabled) return;
            
            const currentActive = document.querySelector('.page-button.active');
            if (currentActive) {
                currentActive.classList.remove('active');
            }
            
            if (button.textContent.trim() !== '') {
                button.classList.add('active');
            } else if (button.querySelector('.fa-chevron-right')) {
                // Next page
                const activePage = document.querySelector('.page-button.active');
                if (activePage) {
                    const nextPage = activePage.nextElementSibling;
                    if (nextPage && !nextPage.querySelector('i')) {
                        nextPage.click();
                    }
                }
            } else if (button.querySelector('.fa-chevron-left')) {
                // Previous page
                const activePage = document.querySelector('.page-button.active');
                if (activePage) {
                    const prevPage = activePage.previousElementSibling;
                    if (prevPage && !prevPage.querySelector('i')) {
                        prevPage.click();
                    }
                }
            }
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Update pagination info
    function updatePaginationInfo(visibleCount) {
        const totalCount = document.querySelectorAll('#users-table-body tr').length;
        const paginationInfo = document.querySelector('.pagination-info');
        if (paginationInfo) {
            paginationInfo.textContent = `Showing 1 to ${visibleCount} of ${totalCount} entries`;
        }
    }
    
    // Show toast notification
    function showToast(message, type = 'info') {
        // You can implement a toast notification system here
        // For now, we'll use a simple alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
    
    // Initialize pagination info
    updatePaginationInfo(document.querySelectorAll('#users-table-body tr').length);
});

