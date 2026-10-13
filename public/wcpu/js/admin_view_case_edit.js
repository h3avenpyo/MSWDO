document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-case-btn');
    const saveBtn = document.getElementById('save-case-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    
    function toggleEditMode(editing) {
        // Toggle disabled state of all inputs and textareas
        document.querySelectorAll('input, textarea').forEach(el => {
            el.disabled = !editing;
            el.classList.toggle('editing', editing);
        });
        
        // Toggle button visibility
        editBtn.classList.toggle('d-none', editing);
        saveBtn.classList.toggle('d-none', !editing);
        cancelBtn.classList.toggle('d-none', !editing);
    }
    
    // Initialize all inputs as disabled
    document.querySelectorAll('input, textarea').forEach(el => {
        el.disabled = true;
    });
    
    // Edit button click handler
    editBtn?.addEventListener('click', () => toggleEditMode(true));
    
    // Cancel button click handler
    cancelBtn?.addEventListener('click', () => {
        if (confirm('Discard all changes?')) {
            window.location.reload();
        }
    });
    
    // Save button click handler
    saveBtn?.addEventListener('click', async () => {
        const formData = new FormData(document.getElementById('case-form'));
        const caseId = window.location.pathname.split('/').pop();
        
        try {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
            
            const response = await fetch(`/cases/${caseId}/update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData.entries()))
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Case updated successfully!');
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to update case');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
        }
    });
});
