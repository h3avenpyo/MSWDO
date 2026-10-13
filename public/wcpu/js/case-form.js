document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('new-case-form');
    
    if (form) {
        console.log('Form found, setting up event listeners...');
        // Set default date values
        setDefaultDates();
        
        // Add form submission handler
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Form submit event triggered');
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Saving...';
            
            // Validate form
            if (!validateForm()) {
                console.log('Form validation failed');
                if (window.notify) { notify.error('Please fill in all required fields correctly.'); }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            
            try {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Please refresh the page and try again.');
                }
                
                console.log('Preparing form data...');
                const formData = new FormData(form);
                
                // Log form data for debugging
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                
                // Get the form action URL
                const actionUrl = form.action || '/admin/cases/store';
                console.log('Sending form data to:', actionUrl);
                
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Server responded with an error');
                }
                
                const result = await response.json();
                console.log('Form submission response:', result);
                
                if (result.success) {
                    // Show success message and redirect
                    if (window.notify) { notify.success('Case created successfully!'); }
                    window.location.href = result.redirect;
                } else {
                    throw new Error(result.message || 'Form submission failed');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                if (window.notify) { notify.error('Error creating case.'); }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    function setDefaultDates() {
        const today = new Date().toISOString().split('T')[0];
        
        // Set default date reported to today if empty
        const dateReported = document.getElementById('date_reported');
        if (dateReported && !dateReported.value) {
            dateReported.value = today;
        }
        
        // Set default incident date to today if empty
        const incidentDate = document.getElementById('incident_date');
        if (incidentDate && !incidentDate.value) {
            incidentDate.value = today;
        }
        
        // Set default incident time to current time if empty
        const incidentTime = document.getElementById('incident_time');
        if (incidentTime && !incidentTime.value) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            incidentTime.value = `${hours}:${minutes}`;
        }
    }
    
    function validateForm() {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Check required fields
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                showError(field, 'This field is required');
                isValid = false;
            }
        });
        
        // Validate email if present
        const emailField = form.querySelector('input[type="email"]');
        if (emailField && emailField.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value)) {
                showError(emailField, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Validate phone number if present
        const phoneField = form.querySelector('input[type="tel"]');
        if (phoneField && phoneField.value) {
            const phoneRegex = /^[0-9\-\+\(\)\s]*$/;
            if (!phoneRegex.test(phoneField.value)) {
                showError(phoneField, 'Please enter a valid phone number');
                isValid = false;
            }
        }
        
        // Validate date fields
        const dateFields = form.querySelectorAll('input[type="date"]');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        dateFields.forEach(field => {
            if (field.value) {
                const fieldDate = new Date(field.value);
                fieldDate.setHours(0, 0, 0, 0);
                
                // Ensure date is not in the future
                if (fieldDate > today) {
                    showError(field, 'Date cannot be in the future');
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
    
    function showError(field, message) {
        // Add error class to field
        field.classList.add('is-invalid');
        
        // Create error message element
        const error = document.createElement('div');
        error.className = 'error-message';
        error.style.color = '#dc3545';
        error.style.fontSize = '0.875rem';
        error.style.marginTop = '0.25rem';
        error.textContent = message;
        
        // Insert error message after the field
        field.parentNode.insertBefore(error, field.nextSibling);
    }
});
