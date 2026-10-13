document.addEventListener('DOMContentLoaded', function() {
    // Password requirements
    const requirements = {
        length: { regex: /.{8,}/, message: 'At least 8 characters' },
        lowercase: { regex: /[a-z]/, message: 'At least 1 lowercase letter' },
        uppercase: { regex: /[A-Z]/, message: 'At least 1 uppercase letter' },
        number: { regex: /[0-9]/, message: 'At least 1 number' },
        special: { regex: /[^A-Za-z0-9]/, message: 'At least 1 special character' }
    };

    // Check password against requirements
    function checkPassword(password) {
        const results = {};
        Object.keys(requirements).forEach(key => {
            results[key] = requirements[key].regex.test(password);
        });
        return results;
    }

    // Update requirement list
    function updateRequirements(password) {
        const requirementsDiv = document.getElementById('password-requirements');
        if (password.length > 0) {
            requirementsDiv.style.display = 'block';
            const results = checkPassword(password);
            Object.entries(results).forEach(([key, isValid]) => {
                const requirement = document.getElementById(`req-${key}`);
                if (requirement) {
                    requirement.className = isValid ? 'valid' : 'invalid';
                    requirement.innerHTML = `
                        <i class="fas ${isValid ? 'fa-check' : 'fa-times'}"></i>
                        ${requirements[key].message}
                    `;
                }
            });
        } else {
            requirementsDiv.style.display = 'none';
        }
    }

    // Check if password matches confirmation
    function checkPasswordMatch() {
        const password = document.getElementById('password')?.value;
        const confirmPassword = document.getElementById('password_confirmation')?.value;
        const matchMessage = document.getElementById('password-match-message');
        
        if (!password || !confirmPassword) {
            matchMessage.style.display = 'none';
            return false;
        }

        const isMatch = password === confirmPassword;
        matchMessage.style.display = 'block';
        matchMessage.innerHTML = `
            <i class="fas ${isMatch ? 'fa-check' : 'fa-times'}"></i>
            ${isMatch ? 'Passwords match' : 'Passwords do not match'}
        `;
        matchMessage.className = isMatch ? 'valid' : 'invalid';
        return isMatch;
    }

    // Handle form submission
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        // Password input event listeners
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', (e) => {
                updateRequirements(e.target.value);
                checkPasswordMatch();
            });
        }

        const confirmPasswordInput = document.getElementById('password_confirmation');
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        }

        registrationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password')?.value;
            const confirmPassword = document.getElementById('password_confirmation')?.value;
            
            // Check if all requirements are met
            const results = checkPassword(password);
            const allValid = Object.values(results).every(valid => valid);
            
            if (!allValid) {
                showAlert('error', 'Please meet all password requirements');
                return false;
            }
            
            if (!checkPasswordMatch()) {
                showAlert('error', 'Passwords do not match!');
                return false;
            }
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Log form data for debugging
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            console.log('Form data being submitted:', formDataObj);
            
            try {
                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                let result;
                try {
                    result = await response.json();
                    console.log('Response data:', result);
                } catch (e) {
                    console.error('Error parsing response:', e);
                    throw new Error('Invalid response from server');
                }
                
                if (response.ok) {
                    showAlert('success', 'User registered successfully!');
                    // Redirect to users list after a short delay
                    setTimeout(() => {
                        const usersLink = document.querySelector('a[href$="users"]');
                        window.location.href = usersLink ? usersLink.href : '/admin/users';
                    }, 1500);
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        const errorMessages = [];
                        for (const [field, errors] of Object.entries(result.errors)) {
                            const fieldName = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            errorMessages.push(...errors.map(error => `${fieldName}: ${error}`));
                        }
                        showAlert('error', errorMessages.join('<br>'));
                    } else {
                        showAlert('error', result.message || 'Failed to register user');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while processing your request. Please try again.');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }
    
    // Helper function to show alerts
    function showAlert(type, message) {
        const existingAlert = document.querySelector('.alert-message');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert-message alert-${type}`;
        alertDiv.innerHTML = `
            <div class="alert-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="alert-close">&times;</button>
        `;
        
        alertDiv.querySelector('.alert-close').addEventListener('click', () => alertDiv.remove());
        
        setTimeout(() => {
            if (document.body.contains(alertDiv)) {
                alertDiv.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => alertDiv.remove(), 300);
            }
        }, 5000);
        
        const form = document.getElementById('registrationForm');
        if (form) {
            form.parentNode.insertBefore(alertDiv, form);
        } else {
            document.body.prepend(alertDiv);
        }
    }
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        .select-animate {
            animation: fadeIn 0.2s ease-out forwards;
            will-change: transform, opacity;
        }
    `;
    document.head.appendChild(style);

    // Initialize select elements
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('mousedown', function() {
            this.classList.add('select-animate');
        });
        select.addEventListener('animationend', function() {
            this.classList.remove('select-animate');
        });
    });

    // Password toggle functionality
    function togglePassword(button) {
        const targetInput = document.querySelector(button.getAttribute('data-target'));
        if (targetInput) {
            const isPassword = targetInput.type === 'password';
            targetInput.type = isPassword ? 'text' : 'password';
            button.innerHTML = `<i class="far fa-eye${isPassword ? '-slash' : ''}"></i> ${isPassword ? 'Hide' : 'Show'}`;
        }
    }

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            togglePassword(button);
        });
    });

    // File input handling
    const profilePicInput = document.getElementById('profile_picture');
    if (profilePicInput) {
        profilePicInput.addEventListener('change', function() {
            const fileInfo = this.closest('.form-group')?.querySelector('.file-info');
            if (fileInfo) {
                const fileName = this.value.split('\\').pop();
                fileInfo.textContent = fileName || 'No file chosen';
                fileInfo.style.color = fileName ? '#333' : '#777';
            }
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }
            
            fetch(registerForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'User registered successfully!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        
                        setTimeout(() => {
                            window.location.href = data.redirect || '/admin/users';
                        }, 2000);
                    } else {
                        window.location.href = data.redirect || '/admin/users';
                    }
                } else {
                    throw new Error(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'An error occurred. Please try again.'
                    });
                } else {
                    alert(error.message || 'An error occurred. Please try again.');
                }
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        });
    }
});