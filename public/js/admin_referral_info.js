document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const addRecipientBtn = document.getElementById('addRecipientBtn');
    const recipientModal = document.getElementById('recipientModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const recipientForm = document.getElementById('recipientForm');
    const modalTitle = document.getElementById('modalTitle');
    const saveBtn = document.getElementById('saveBtn');
    const searchInput = document.getElementById('searchInput');
    const recipientContactEl = document.getElementById('recipientContact');
    
    let currentRecipientId = null;
    let isEditMode = false;

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Open modal
    function openModal(editMode = false, recipientData = null) {
        isEditMode = editMode;
        currentRecipientId = recipientData ? recipientData.id : null;
        
        modalTitle.textContent = editMode ? 'Edit Recipient' : 'Add New Recipient';
        saveBtn.textContent = editMode ? 'Update Recipient' : 'Save Recipient';
        
        if (editMode && recipientData) {
            document.getElementById('recipientName').value = recipientData.name || '';
            document.getElementById('recipientEmail').value = recipientData.email || '';
            document.getElementById('recipientContact').value = recipientData.contact_number || '';
            document.getElementById('recipientRole').value = recipientData.role || '';
        } else {
            recipientForm.reset();
        }
        
        recipientModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    // Close modal
    function closeModalFunc() {
        recipientModal.classList.remove('show');
        document.body.style.overflow = '';
        recipientForm.reset();
        currentRecipientId = null;
        isEditMode = false;
    }

    // Event Listeners
    addRecipientBtn.addEventListener('click', () => openModal(false));
    closeModal.addEventListener('click', closeModalFunc);
    cancelBtn.addEventListener('click', closeModalFunc);

    // Close modal on outside click
    recipientModal.addEventListener('click', (e) => {
        if (e.target === recipientModal) {
            closeModalFunc();
        }
    });

    // Form submission
    recipientForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Sanitize contact: only digits, max 11
        const contactRaw = (recipientContactEl && recipientContactEl.value) || '';
        const contactDigits = contactRaw.replace(/\D/g, '').slice(0,11);
        const formData = new FormData(recipientForm);
        const data = Object.fromEntries(formData.entries());
        if (contactRaw !== '') data.contact_number = contactDigits;
        
        const url = isEditMode 
            ? `/admin/referral-info/${currentRecipientId}`
            : '/admin/referral-info';
        
        const method = isEditMode ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                notify.success(result.message);
                closeModalFunc();
                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                if (result.errors) {
                    let errorMessage = '';
                    Object.values(result.errors).forEach(error => {
                        errorMessage += error.join(', ') + ' ';
                    });
                    notify.error(errorMessage.trim());
                } else {
                    notify.error('An error occurred. Please try again.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            notify.error('An error occurred. Please try again.');
        }
    });

    // Edit button click
    document.addEventListener('click', (e) => {
        if (e.target.closest('.edit-btn')) {
            const btn = e.target.closest('.edit-btn');
            const recipientId = btn.getAttribute('data-id');
            const row = btn.closest('tr');
            
            const recipientData = {
                id: recipientId,
                name: row.cells[0].textContent.trim(),
                email: row.cells[1].textContent.trim(),
                contact_number: (row.cells[2].textContent.trim() === '—' ? '' : row.cells[2].textContent.trim()),
                role: row.cells[3].textContent.trim() === 'N/A' ? '' : row.cells[3].textContent.trim()
            };
            
            openModal(true, recipientData);
        }
    });

    // Enforce numeric-only and max 11 digits while typing/pasting
    if (recipientContactEl) {
        recipientContactEl.addEventListener('input', function (e) {
            const sanitized = this.value.replace(/\D/g, '').slice(0, 11);
            if (this.value !== sanitized) this.value = sanitized;
        });
        recipientContactEl.addEventListener('paste', function (e) {
            // sanitize pasted content
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0,11);
            document.execCommand('insertText', false, paste);
        });
    }

    // Delete button click
    document.addEventListener('click', async (e) => {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const recipientId = btn.getAttribute('data-id');
            const row = btn.closest('tr');
            const recipientName = row.cells[0].textContent.trim();
            
            const result = await Swal.fire({
                title: 'Delete Recipient',
                html: `<div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-trash-alt text-4xl text-red-500 mb-3"></i>
                    </div>
                    <p class="text-gray-700">Are you sure you want to delete <strong>"${recipientName}"</strong>?</p>
                    <p class="text-gray-500 text-sm mt-2">This action cannot be undone.</p>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'shadow-lg',
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                },
                reverseButtons: true
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/referral-info/${recipientId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        notify.success(result.message);
                        // Remove row from table
                        row.remove();
                        
                        // Check if table is empty
                        const tbody = document.querySelector('#recipientsTable tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <i class="fas fa-inbox"></i>
                                        <p>No recipients found</p>
                                    </td>
                                </tr>
                            `;
                        }
                    } else {
                        notify.error('Failed to delete recipient. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    notify.error('An error occurred. Please try again.');
                }
            }
        }
    });

    // Toggle status button click
    document.addEventListener('click', async (e) => {
        if (e.target.closest('.toggle-btn')) {
            const btn = e.target.closest('.toggle-btn');
            const recipientId = btn.getAttribute('data-id');
            const row = btn.closest('tr');
            
            try {
                const response = await fetch(`/admin/referral-info/${recipientId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    notify.success(result.message);
                    
                    // Update status badge
                    const statusBadge = row.querySelector('.status-badge');
                    const icon = btn.querySelector('i');
                    
                    if (result.recipient.active) {
                        statusBadge.className = 'status-badge active';
                        statusBadge.textContent = 'Active';
                        icon.className = 'fas fa-eye-slash';
                        btn.title = 'Deactivate';
                    } else {
                        statusBadge.className = 'status-badge inactive';
                        statusBadge.textContent = 'Inactive';
                        icon.className = 'fas fa-eye';
                        btn.title = 'Activate';
                    }
                } else {
                    notify.error('Failed to update status. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                notify.error('An error occurred. Please try again.');
            }
        }
    });

    // Search functionality
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#recipientsTable tbody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.no-data')) return;
            
            const name = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            const contact = (row.cells[2] ? row.cells[2].textContent.toLowerCase() : '');
            const role = (row.cells[3] ? row.cells[3].textContent.toLowerCase() : '');

            const matches = name.includes(searchTerm) || 
                          email.includes(searchTerm) || 
                          contact.includes(searchTerm) || 
                          role.includes(searchTerm);
            
            row.style.display = matches ? '' : 'none';
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Escape key to close modal
        if (e.key === 'Escape' && recipientModal.classList.contains('show')) {
            closeModalFunc();
        }
        
        // Ctrl+N to add new recipient
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            openModal(false);
        }
    });
});
