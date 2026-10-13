// Simple modal functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    // Initialize filter dropdown
    const filterDropdownButton = document.getElementById('filter-dropdown-button');
    const filterDropdownMenu = document.getElementById('filter-dropdown-menu');
    const roleFilter = document.getElementById('role-filter');
    const statusFilter = document.getElementById('status-filter');
    const departmentFilter = document.getElementById('department-filter');
    const searchForm = document.getElementById('searchForm');
    
    // Toggle dropdown menu
    if (filterDropdownButton && filterDropdownMenu) {
        // Function to toggle dropdown
        function toggleDropdown(show = null) {
            const isOpen = show !== null ? show : filterDropdownMenu.style.display !== 'block';
            
            if (isOpen) {
                filterDropdownMenu.style.display = 'block';
                setTimeout(() => {
                    filterDropdownMenu.style.opacity = '1';
                    filterDropdownMenu.style.transform = 'translateY(0)';
                }, 10);
            } else {
                filterDropdownMenu.style.opacity = '0';
                filterDropdownMenu.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    filterDropdownMenu.style.display = 'none';
                }, 200);
            }
            
            // Rotate chevron icon
            const chevron = filterDropdownButton.querySelector('.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            }
            
            return isOpen;
        }
        
        // Toggle on button click
        filterDropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!filterDropdownMenu.contains(e.target) && !filterDropdownButton.contains(e.target)) {
                toggleDropdown(false);
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        filterDropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Auto-submit form when filters change
    const filterInputs = [roleFilter, statusFilter, departmentFilter].filter(Boolean);
    filterInputs.forEach(input => {
        if (input) {
            input.addEventListener('change', function() {
                searchForm.submit();
            });
        }
    });
    
    // Initialize modal elements
    const modal = document.getElementById('userInfoModal');
    const modalContent = document.getElementById('modalContent');
    const closeBtn = document.querySelector('.close-modal');
    const viewBtns = document.querySelectorAll('.view-btn');
    
    // Add animation for modal
    if (modal) {
        const style = document.createElement('style');
        style.textContent = `
            #userInfoModal {
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            #userInfoModal.show {
                display: flex !important;
                opacity: 1;
                animation: modalFadeIn 0.3s ease-out;
            }
            @keyframes modalFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Debug: Log modal elements
    console.log('Modal elements found:', {
        modal: modal ? 'Found' : 'Not found',
        modalContent: modalContent ? 'Found' : 'Not found',
        closeBtn: closeBtn ? 'Found' : 'Not found',
        viewButtons: viewBtns.length
    });
    
    // Debug: Log the first view button's HTML if it exists
    if (viewBtns.length > 0) {
        console.log('First view button HTML:', viewBtns[0].outerHTML);
    } else {
        console.error('No view buttons found with class .view-btn');
    }

    // Function to open modal
    function openModal(userId) {
        console.log('Opening modal for user:', userId);
        console.log('Modal element:', modal);
        if (!modal) {
            console.error('Modal element not found!');
            return;
        }
        
        // Show loading state
        const modalLoading = document.getElementById('modalLoading');
        const modalContent = document.getElementById('modalContent');
        
        if (modalLoading) modalLoading.style.display = 'block';
        if (modalContent) modalContent.style.display = 'none';
        
        // Show the modal with animation
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Fetch user details from the backend
        fetch(`/admin/users/api/${userId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            
            // If response is not JSON, throw an error with the response text
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Received non-JSON response from server');
            }
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to fetch user details');
            }
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch user details');
            }
            
            console.log('User data received:', data.user);
            
            // Store original values for cancel
            window.currentUserData = data.user;
            
            // Set the user ID in the hidden input field
            const userIdInput = document.getElementById('editUserId');
            if (userIdInput) {
                userIdInput.value = data.user.id;
            } else {
                console.error('Could not find editUserId input field');
            }
            
            // Return the user data for further processing
            return data.user;
        })
        .then(userData => {
            // Populate the modal with user data
            const safeSetValue = (id, value, isHtml = false) => {
                    const element = document.getElementById(id);
                    if (element) {
                        if (isHtml) {
                            element.innerHTML = value || '-';
                        } else if (element.tagName === 'INPUT' || element.tagName === 'SELECT') {
                            element.value = value || '';
                        } else {
                            element.textContent = value || '-';
                        }
                    }
                };
                
                // Update form fields with user data
                const updateUserFormFields = (user) => {
                    console.log('Updating form fields with user data:', user);
                    
                    // Use phone_number instead of phone to match the API response
                    const phoneNumber = user.phone_number || user.phone || '';
                    
                    // Update display fields
                    safeSetValue('modalFirstName', user.first_name || 'N/A');
                    safeSetValue('modalLastName', user.last_name || 'N/A');
                    safeSetValue('modalEmail', user.email || 'N/A');
                    safeSetValue('modalPhone', phoneNumber || 'Not provided');
                    safeSetValue('modalPosition', user.position || 'Not specified');
                    safeSetValue('modalDepartment', user.department || 'Not specified');
                    
                    // Update input fields
                    safeSetValue('editFirstName', user.first_name || '');
                    safeSetValue('editLastName', user.last_name || '');
                    safeSetValue('editEmail', user.email || '');
                    safeSetValue('editPhone', phoneNumber);
                    safeSetValue('editPosition', user.position || '');
                    safeSetValue('editDepartment', user.department || '');
                    
                    // Debug: Log the phone number specifically
                    console.log('Phone number being set:', phoneNumber);
                    console.log('Phone input element value:', document.getElementById('editPhone')?.value);
                };
                
                updateUserFormFields(userData);

                // Fix timezone offset issue for created_at and last_login_at
                function formatDate(dateStr) {
                    if (!dateStr) return '-';
                    // Parse as UTC to avoid timezone offset
                    const dateObj = new Date(dateStr);
                    // Get UTC values
                    const month = String(dateObj.getUTCMonth() + 1).padStart(2, '0');
                    const day = String(dateObj.getUTCDate()).padStart(2, '0');
                    const year = dateObj.getUTCFullYear();
                    return `${month}/${day}/${year}`;
                }

                if (document.getElementById('modalLastLogin')) {
                    let lastLogin = userData.last_login_at;
                    document.getElementById('modalLastLogin').textContent = lastLogin ? formatDate(lastLogin) : 'Never';
                }
                if (document.getElementById('modalCreatedAt')) {
                    let createdAt = userData.created_at;
                    document.getElementById('modalCreatedAt').textContent = createdAt ? formatDate(createdAt) : '-';
                }
                if (document.getElementById('modalUserId')) {
                    document.getElementById('modalUserId').textContent = userData.id || 'N/A';
                }
                
                // Hide loading, show content
                if (modalLoading) modalLoading.style.display = 'none';
                if (modalContent) modalContent.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
                
                // Show error message
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                            Error loading user details. Please try again later.
                        </div>
                        <button class="close-btn" style="margin-top: 10px;">Close</button>
                    `;
                    modalContent.style.display = 'block';
                    
                    // Re-bind close button
                    const closeBtn = modalContent.querySelector('.close-btn');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', closeModal);
                    }
                }
                
                if (modalLoading) modalLoading.style.display = 'none';
            });
    }

    // Toggle edit mode
    function toggleEditMode(edit) {
        const formInputs = document.querySelectorAll('#userEditForm input');
        const displayFields = ['modalFirstName', 'modalLastName', 'modalEmail', 'modalPhone', 'modalPosition', 'modalDepartment'];
        const editForm = document.getElementById('userEditForm');
        const editBtn = document.getElementById('editUserBtn');
        const saveBtn = document.getElementById('saveUserBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        
        formInputs.forEach(input => {
            // Skip the hidden user ID input
            if (input.id !== 'editUserId') {
                input.disabled = !edit;
                input.style.display = edit ? 'block' : 'none';
            }
        });
        
        displayFields.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = edit ? 'none' : 'block';
            }
        });
        
        // Reset save button state when toggling edit mode
        if (saveBtn) {
            const originalText = saveBtn.getAttribute('data-original-text');
            if (originalText) {
                saveBtn.innerHTML = originalText;
            } else {
                saveBtn.innerHTML = 'Save Changes';
                saveBtn.setAttribute('data-original-text', 'Save Changes');
            }
            saveBtn.disabled = false;
        }
        
        if (editBtn) editBtn.style.display = edit ? 'none' : 'block';
        if (saveBtn) saveBtn.style.display = edit ? 'block' : 'none';
        if (cancelBtn) cancelBtn.style.display = edit ? 'block' : 'none';
    }
    
    // Handle form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const userIdInput = document.getElementById('editUserId');
        
        if (!userIdInput || !userIdInput.value) {
            // User ID input not found or empty
            // Prevent further processing and avoid error/warning
            // Optionally, you can log or silently return
            // console.warn('User ID input not found or empty');
            return;
        }
        
        const userId = userIdInput.value;
        
        // Show loading state
        const saveBtn = document.getElementById('saveUserBtn');
        if (saveBtn) {
            // Store the current button text if not already stored
            if (!saveBtn.getAttribute('data-original-text')) {
                saveBtn.setAttribute('data-original-text', saveBtn.textContent.trim());
            }
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        }
        
        // Send the form data to the server
        fetch(`/admin/users/api/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update displayed values
                const fields = {
                    'modalPhone': formData.get('phone'),
                    'modalPosition': formData.get('position'),
                    'modalDepartment': formData.get('department')
                };
                
                Object.entries(fields).forEach(([id, value]) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = value || 'N/A';
                    }
                });
                
                // Update the current user data
                if (window.currentUserData) {
                    Object.keys(fields).forEach(key => {
                        const fieldName = key.replace('modal', '').toLowerCase();
                        window.currentUserData[fieldName] = formData.get(fieldName);
                    });
                }
                
                // Reset save button state before toggling view
                const saveBtn = document.getElementById('saveUserBtn');
                if (saveBtn) {
                    const originalText = saveBtn.getAttribute('data-original-text') || 'Save Changes';
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                }
                
                // Toggle back to view mode
                toggleEditMode(false);
                
                // Show success message using the notification system
                if (window.notify) {
                    notify.success('User updated successfully. Refreshing page...');
                } else {
                    alert('User updated successfully. Refreshing page...');
                }
                
                // Refresh the page after a short delay to show the success message
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to update user');
            }
        })
        .catch(error => {
            console.error('Error updating user:', error);
            
            // Show error message using the notification system
            if (window.notify) {
                notify.error(error.message || 'Failed to update user');
            } else {
                alert('Error: ' + (error.message || 'Failed to update user'));
            }
            
            // Reset button state on error
            const saveBtn = document.getElementById('saveUserBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                const originalText = saveBtn.getAttribute('data-original-text');
                saveBtn.innerHTML = originalText || 'Save Changes';
            }
        });
    }
    
    // Function to close modal
    function closeModal() {
        if (!modal) return;
        
        console.log('Closing modal');
        
        // Add fade-out effect
        modal.style.opacity = '0';
        modal.style.transition = 'opacity 0.3s ease';
        
        // After animation completes, hide and clean up
        setTimeout(() => {
            if (!modal) return;
            
            // Hide the modal
            modal.style.display = 'none';
            modal.style.opacity = '1';
            modal.style.transition = '';
            
            // Re-enable body scrolling
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '';
            
            // Remove any active classes
            modal.classList.remove('show');
            
            console.log('Modal closed');
        }, 300);
        
        // Prevent event bubbling
        return false;
    }
    
    // Add click event for close button in the modal content
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('close-modal') || e.target.closest('.close-btn') || e.target === modal) {
            closeModal();
        }
    });
    
    // Add click events to all view buttons using event delegation
    document.addEventListener('click', function(e) {
        // Check if the clicked element or its parent has the view-btn class
        const viewBtn = e.target.closest('.view-btn');
        if (viewBtn) {
            e.preventDefault();
            const userId = viewBtn.getAttribute('data-user-id');
            console.log('View button clicked for user:', userId);
            openModal(userId);
        }
    });

    console.log('View buttons found:', viewBtns.length);

    // Close modal when clicking the X button or close button
    document.addEventListener('click', function(e) {
        // Check for X button click
        if (e.target.classList.contains('close-modal') || e.target.closest('.close-btn')) {
            e.preventDefault();
            closeModal();
        }
        
        // Check for click outside modal content
        if (modal && modal.style.display === 'flex' && e.target === modal) {
            closeModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeModal();
        }
    });
    
    // Prevent modal content clicks from closing the modal
    const modalContentElement = document.querySelector('.modal-content');
    if (modalContentElement) {
        modalContentElement.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Close modal when clicking the X button or close button
    document.addEventListener('click', function(e) {
        // Check for X button click
        if (e.target.classList.contains('close-modal') || e.target.closest('.close-btn')) {
            e.preventDefault();
            closeModal();
            return;
        }
        
        // Check for click outside modal content
        if (modal && modal.style.display === 'flex' && e.target === modal) {
            closeModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeModal();
        }
    });

    // Handle archive user
    async function handleArchiveUser(userId, skipConfirmation = false) {
        if (!skipConfirmation) {
            const confirmation = await Swal.fire({
                title: 'Archive user?',
                text: 'This user will no longer be able to log in.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Archive',
                confirmButtonColor: '#C41E3A',
                width: 'min(28rem, calc(100vw - 2rem))'
            });
            if (!confirmation.isConfirmed) return;
        }

        fetch(`/admin/users/api/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message using notification system
                if (window.notify) {
                    notify.success('User archived successfully');
                } else {
                    alert('User archived successfully');
                }
                
                // Close the modal
                closeModal();
                
                // Reload the page to update the user list after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to archive user');
            }
        })
        .catch(error => {
            console.error('Error archiving user:', error);
            if (window.notify) {
                notify.error(error.message || 'Failed to archive user');
            } else {
                alert('Error: ' + (error.message || 'Failed to archive user'));
            }
        });
    }

    // Initialize modal buttons and form
    function initModal() {
        // Close button in the modal header (X)
        const closeModalBtn = document.querySelector('.close-modal');
        if (closeModalBtn) {
            closeModalBtn.onclick = function() {
                closeModal();
            };
        }
        
        // Close button at the bottom
        const closeBtn = document.querySelector('.close-btn');
        if (closeBtn) {
            closeBtn.onclick = function() {
                closeModal();
            };
        }
        
        // Edit button
        const editBtn = document.getElementById('editUserBtn');
        if (editBtn) {
            editBtn.onclick = function() {
                toggleEditMode(true);
            };
        }
        
        // Archive button
        const archiveBtn = document.getElementById('archiveUserBtn');
        if (archiveBtn) {
            archiveBtn.onclick = function() {
                const userId = document.getElementById('editUserId').value;
                if (userId) {
                    handleArchiveUser(userId);
                }
            };
        }
        
        // Cancel edit button
        const cancelBtn = document.getElementById('cancelEditBtn');
        if (cancelBtn) {
            cancelBtn.onclick = function() {
                // Restore original values
                if (window.currentUserData) {
                    document.getElementById('editPhone').value = window.currentUserData.phone || window.currentUserData.phone_number || '';
                    document.getElementById('editPosition').value = window.currentUserData.position || '';
                    document.getElementById('editDepartment').value = window.currentUserData.department || '';
                }
                toggleEditMode(false);
            };
        }
        
        // Form submission
        const form = document.getElementById('userEditForm');
        if (form) {
            form.onsubmit = handleFormSubmit;
        }
    }
    
    // Initialize modal when DOM is loaded
    initModal();
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeModal();
        }
    });

    // Handle archive button clicks in the users table
    document.addEventListener('click', function(e) {
        const archiveBtn = e.target.closest('.archive-user');
        if (archiveBtn) {
            e.preventDefault();
            const userId = archiveBtn.closest('button').dataset.userId;
            
            if (window.Swal) {
                Swal.fire({ title: 'Archive user?', text: 'This user will no longer be able to log in.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Archive', confirmButtonColor: '#C41E3A', width: 'min(28rem, calc(100vw - 2rem))' }).then(result => {
                    if (result.isConfirmed) handleArchiveUser(userId, true);
                });
            } else if (window.notify) {
                // If we don't have confirm but have notifications, use a custom approach
                const notification = notify.warning('Are you sure you want to archive this user?', 'warning', 0);
                
                // Create a container for the confirmation buttons
                const buttonContainer = document.createElement('div');
                buttonContainer.style.marginTop = '10px';
                buttonContainer.style.display = 'flex';
                buttonContainer.style.gap = '10px';
                
                // Create Confirm button
                const confirmBtn = document.createElement('button');
                confirmBtn.textContent = 'Yes, Archive';
                confirmBtn.style.padding = '5px 10px';
                confirmBtn.style.border = 'none';
                confirmBtn.style.borderRadius = '4px';
                confirmBtn.style.background = '#dc3545';
                confirmBtn.style.color = 'white';
                confirmBtn.style.cursor = 'pointer';
                confirmBtn.onclick = function() {
                    notification.close();
                    handleArchiveUser(userId);
                };
                
                // Create Cancel button
                const cancelBtn = document.createElement('button');
                cancelBtn.textContent = 'Cancel';
                cancelBtn.style.padding = '5px 10px';
                cancelBtn.style.border = '1px solid #6c757d';
                cancelBtn.style.borderRadius = '4px';
                cancelBtn.style.background = 'transparent';
                cancelBtn.style.color = '#6c757d';
                cancelBtn.style.cursor = 'pointer';
                cancelBtn.onclick = function() {
                    notification.close();
                };
                
                // Add buttons to container
                buttonContainer.appendChild(confirmBtn);
                buttonContainer.appendChild(cancelBtn);
                
                // Add container to the notification
                const notificationElement = document.querySelector('.notification:last-child');
                if (notificationElement) {
                    const messageContainer = notificationElement.querySelector('.notification-message');
                    if (messageContainer) {
                        messageContainer.appendChild(document.createElement('br'));
                        messageContainer.appendChild(buttonContainer);
                    }
                }
            } else {
                // Fallback to default behavior if neither confirm nor notifications are available
                handleArchiveUser(userId);
            }
        }
    });
});
