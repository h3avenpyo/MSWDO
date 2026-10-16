document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    
    // Password toggle functionality
    if (togglePassword && passwordInput) {
        const eyeIcon = togglePassword.querySelector('i');
        
        // Function to update the toggle state
        const updateToggleState = (isPassword) => {
            if (isPassword) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
                togglePassword.setAttribute('aria-label', 'Hide password');
                togglePassword.classList.add('active');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
                togglePassword.setAttribute('aria-label', 'Show password');
                togglePassword.classList.remove('active');
            }
        };
        
        // Initialize the toggle state
        updateToggleState(passwordInput.type === 'password');
        
        // Toggle password visibility
        const togglePasswordVisibility = (e) => {
            if (e) e.preventDefault();
            
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            updateToggleState(!isPassword);
            
            // Keep focus on the input for better UX
            passwordInput.focus();
        };
        
        // Add click event
        togglePassword.addEventListener('mousedown', function(e) {
            e.preventDefault();
            togglePasswordVisibility(e);
        });
        
        // Add keyboard support (space or enter)
        togglePassword.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                togglePasswordVisibility(e);
            }
        });
        
        // Ensure proper tab order
        togglePassword.setAttribute('tabindex', '0');
    }
    
    // User type selection
    const adminOption = document.getElementById('admin-option');
    const deskOfficerOption = document.getElementById('desk-officer-option');
    const userTypeInput = document.getElementById('user_type');
    
    if (adminOption && deskOfficerOption && userTypeInput) {
        adminOption.addEventListener('click', function() {
            adminOption.classList.add('active');
            deskOfficerOption.classList.remove('active');
            userTypeInput.value = 'admin';
        });

        deskOfficerOption.addEventListener('click', function() {
            deskOfficerOption.classList.add('active');
            adminOption.classList.remove('active');
            userTypeInput.value = 'desk-officer';
        });
    }
    
    // Forgot password functionality
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    const forgotPasswordModal = document.getElementById('forgot-password-modal');
    const closeModal = document.querySelector('.close-modal');
    const resetForm = document.getElementById('reset-password-form');
    const resetMessage = document.getElementById('reset-message');
    
    let resetAcknowledgmentPending = false;
    const noticeDialog = document.getElementById('login-message-dialog');
    const noticeText = document.getElementById('login-message-text');
    const noticeOk = document.getElementById('login-message-ok');
    function showLoginNotice(message) {
        noticeText.textContent = message;
        noticeDialog.showModal();
        noticeOk.focus();
    }
    noticeDialog?.addEventListener('cancel', event => event.preventDefault());
    noticeOk?.addEventListener('click', () => {
        noticeDialog.close();
        if (resetAcknowledgmentPending) {
            resetAcknowledgmentPending = false;
            closeForgotPasswordModal();
        }
    });
    const inactivityMessage = document.getElementById('inactivity-login-message')?.textContent.trim();
    const passwordResetSuccessMessage = document.getElementById('password-reset-success-message')?.textContent.trim();
    if (passwordResetSuccessMessage) {
        showLoginNotice(passwordResetSuccessMessage);
    } else if (inactivityMessage) {
        showLoginNotice(inactivityMessage);
    }
    function closeForgotPasswordModal() {
        if (resetAcknowledgmentPending) return;
        if (forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
            document.body.style.overflow = 'auto';
            if (resetMessage) {
                resetMessage.className = 'reset-message';
                resetMessage.textContent = '';
            }
            if (resetForm) resetForm.reset();
        }
    }
    
    if (forgotPasswordLink && forgotPasswordModal) {
        // Open modal
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            forgotPasswordModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
        
        // Close modal
        if (closeModal) {
            closeModal.addEventListener('click', closeForgotPasswordModal);
        }
        
        // Close when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === forgotPasswordModal) {
                closeForgotPasswordModal();
            }
        });
        
        // Handle reset form
        if (resetForm && resetMessage) {
            resetForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const identifier = document.getElementById('reset-identifier')?.value;
                const submitButton = this.querySelector('button[type="submit"]');
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
                
                console.log('Form submitted with identifier:', identifier);
                console.log('CSRF Token:', csrfToken ? 'Found' : 'Not Found');
                
                if (!identifier) {
                    resetMessage.textContent = 'Please enter your email or username.';
                    resetMessage.className = 'p-3 rounded-md bg-red-50 text-red-700 border border-red-200';
                    resetMessage.classList.remove('hidden');
                    return;
                }
                
                if (!csrfToken) {
                    resetMessage.textContent = 'Security token missing. Please refresh the page and try again.';
                    resetMessage.className = 'p-3 rounded-md bg-red-50 text-red-700 border border-red-200';
                    resetMessage.classList.remove('hidden');
                    return;
                }
                
                if (submitButton) {
                    const originalText = submitButton.innerHTML;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    
                    resetMessage.textContent = 'Processing your request...';
                    resetMessage.className = 'p-3 rounded-md bg-blue-50 text-blue-700 border border-blue-200';
                    resetMessage.classList.remove('hidden');
                    
                    // Send AJAX request
                    fetch('/password-reset-request', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            'reset-identifier': identifier
                        })
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        // Parse JSON regardless of status code
                        return response.json().then(data => ({
                            status: response.status,
                            ok: response.ok,
                            data: data
                        }));
                    })
                    .then(result => {
                        console.log('Response data:', result.data);
                        if (result.ok && result.data.success) {
                            resetMessage.textContent = result.data.message;
                            resetMessage.className = 'p-3 rounded-md bg-green-50 text-green-700 border border-green-200 flex items-start';
                            resetMessage.innerHTML = '<i class="fas fa-check-circle mt-0.5 mr-2"></i><span>' + result.data.message + '</span>';
                            resetForm.reset();
                            
                            resetAcknowledgmentPending = true;
                            resetMessage.classList.add('hidden');
                            showLoginNotice('Your password reset request has been submitted. An administrator will assist you shortly.');
                        } else {
                            resetMessage.textContent = result.data.message || 'An error occurred. Please try again.';
                            resetMessage.className = 'p-3 rounded-md bg-red-50 text-red-700 border border-red-200 flex items-start';
                            resetMessage.innerHTML = '<i class="fas fa-exclamation-circle mt-0.5 mr-2"></i><span>' + (result.data.message || 'An error occurred. Please try again.') + '</span>';
                        }
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resetMessage.textContent = 'An error occurred: ' + error.message + '. Please try again later.';
                        resetMessage.className = 'p-3 rounded-md bg-red-50 text-red-700 border border-red-200 flex items-start';
                        resetMessage.innerHTML = '<i class="fas fa-exclamation-circle mt-0.5 mr-2"></i><span>An error occurred. Please try again later.</span>';
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
                }
            });
        }
    }

    // Handle login form submission
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            }
        });
    }

    // Handle lockout countdown
    const lockoutMessage = document.getElementById('lockout-message');
    if (lockoutMessage) {
        const lockedUntil = parseInt(lockoutMessage.getAttribute('data-locked-until'));
        const loginFormElement = document.getElementById('login-form');
        const submitButton = loginFormElement?.querySelector('button[type="submit"]');
        const inputs = loginFormElement?.querySelectorAll('input:not([type="hidden"])');
        
        // Disable form while locked
        if (submitButton) submitButton.disabled = true;
        if (inputs) inputs.forEach(input => input.disabled = true);
        
        function updateLockoutCountdown() {
            const now = Math.floor(Date.now() / 1000);
            const remaining = lockedUntil - now;
            
            if (remaining <= 0) {
                // Lockout expired, re-enable form without refreshing
                if (submitButton) submitButton.disabled = false;
                if (inputs) inputs.forEach(input => input.disabled = false);
                
                // Hide the lockout message
                const lockoutContainer = lockoutMessage.closest('.p-3');
                if (lockoutContainer) {
                    lockoutContainer.style.display = 'none';
                }
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            
            const timeText = minutes > 0 
                ? `${minutes} minute(s) and ${seconds} second(s)`
                : `${seconds} second(s)`;
            
            lockoutMessage.textContent = `Too many login attempts. Please try again in ${timeText}.`;
            
            setTimeout(updateLockoutCountdown, 1000);
        }
        
        updateLockoutCountdown();
    }

    // Close forgot password modal function
    function closeForgotPasswordModal() {
        if (resetAcknowledgmentPending) return;
        if (forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
            document.body.style.overflow = 'auto';
            
            if (resetForm) resetForm.reset();
            if (resetMessage) {
                resetMessage.className = 'reset-message';
                resetMessage.textContent = '';
            }
        }
    }

    // Add loaded class to body when everything is ready
    document.body.classList.add('loaded');
});
