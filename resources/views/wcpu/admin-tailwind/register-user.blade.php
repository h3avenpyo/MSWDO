@extends('layouts.admin-tailwind')

@section('title', 'Register User - iSERVE SILANG Admin')

@section('content')

<!-- Top Header -->
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Register New User</h2>
            <p class="text-sm text-gray-500 hidden sm:block">Create a new user account</p>
        </div>
        <div class="flex items-center gap-4">
            @include('admin-tailwind.components.notifications')
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="px-4 sm:px-6 lg:px-8 pt-4 pb-4 sm:pt-6 sm:pb-6 lg:pt-8 lg:pb-8 max-w-3xl mx-auto">
    
    <!-- Alert Messages / Feedback Design -->
    @if(session('success'))
    <div id="success-feedback-alert" class="mb-6 rounded-lg border border-emerald-200 bg-gradient-to-r from-green-100 via-emerald-100 to-lime-100 px-4 py-3 text-sm font-medium text-emerald-800">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div id="error-feedback-alert" class="mb-8 overflow-hidden rounded-2xl border border-rose-200/90 bg-gradient-to-br from-rose-50 via-red-50/60 to-white shadow-lg shadow-rose-500/5 transition-all duration-300">
        <!-- Top Gradient Accent Bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-rose-500 via-red-500 to-amber-500"></div>

        <div class="p-5 sm:p-6">
            <div class="flex items-start gap-4 sm:gap-5">
                <!-- Icon with Ring -->
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-rose-600 to-red-500 text-white shadow-md shadow-rose-500/30 ring-4 ring-rose-100">
                    <i class="fas fa-triangle-exclamation text-2xl"></i>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                            <i class="fas fa-circle-exclamation text-[10px]"></i> Registration Incomplete
                        </span>
                    </div>

                    <h4 class="text-base sm:text-lg font-bold leading-tight" style="color: #ffffff !important;">
                        {{ session('error') ?? 'Please correct the following issue' . ($errors->count() > 1 ? 's' : '') . ' to continue:' }}
                    </h4>

                    @if ($errors->any())
                    <ul class="mt-3 space-y-1.5 text-xs sm:text-sm text-rose-800 bg-white/90 backdrop-blur-xs border border-rose-100 rounded-xl p-3.5">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle-xmark text-rose-500 text-xs mt-0.5 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

                <!-- Dismiss Button -->
                <button type="button" onclick="document.getElementById('error-feedback-alert').remove()" 
                        class="text-rose-500 hover:text-rose-700 hover:bg-rose-100/80 p-2 rounded-xl transition-colors"
                        title="Dismiss notification">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Registration Form -->
    <form action="{{ route('api.users.store') }}" method="POST" id="register-form" class="space-y-6">
        @csrf
        
        <!-- Personal Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#C41E3A] px-6 py-4" style="background: linear-gradient(90deg, #C41E3A 0%, #A01830 100%);">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-user-circle"></i>
                    Personal Information
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                        <label for="first_name" class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-700">
                            <i class="fas fa-user text-sm"></i> First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                            value="{{ old('first_name') }}" placeholder="Enter first name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                        <label for="last_name" class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-700">
                            <i class="fas fa-user-tag text-sm"></i> Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required
                            value="{{ old('last_name') }}" placeholder="Enter last name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#C41E3A] px-6 py-4" style="background: linear-gradient(90deg, #C41E3A 0%, #A01830 100%);">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-shield-alt"></i>
                    Account Information
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Position Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Position <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="position" value="Administrator" required
                                {{ old('position') == 'Administrator' ? 'checked' : '' }}
                                class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800 group-hover:text-purple-700 transition-colors">Administrator</span>
                                <p class="text-xs text-gray-500">Full system access</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="position" value="Desk Officer" required
                                {{ old('position') == 'Desk Officer' ? 'checked' : '' }}
                                class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800 group-hover:text-purple-700 transition-colors">Desk Officer</span>
                                <p class="text-xs text-gray-500">Barangay-level access</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="username" name="username" required
                        value="{{ old('username') }}" placeholder="Enter username"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    <p class="mt-2 text-xs text-gray-500">
                        This will be used as the login username.
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                        value="{{ old('email') }}" placeholder="Enter email address"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    <p class="mt-2 text-xs text-gray-500">
                        A valid email address is required for account notifications.
                    </p>
                </div>
                
                <!-- Department -->
                <div id="department-group" class="hidden">
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <select id="department" name="department"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">Select Department</option>
                        <option value="{{ auth()->user()->isVawcAdmin() ? 'VAWC' : 'BCPC' }}" data-administrator-department
                            {{ old('department') === (auth()->user()->isVawcAdmin() ? 'VAWC' : 'BCPC') ? 'selected' : '' }}>
                            {{ auth()->user()->isVawcAdmin() ? 'SWD' : 'BCPC' }}
                        </option>
                        <option value="BCPC/VAWC" data-desk-officer-department {{ old('department') === 'BCPC/VAWC' ? 'selected' : '' }}>{{ auth()->user()->isVawcAdmin() ? 'SWD/BCPC' : 'BCPC/SWD' }}</option>
                    </select>
                </div>
                
                <!-- Barangay (Desk Officer only) -->
                <div id="barangay-group" class="hidden">
                    <label for="barangay" class="block text-sm font-medium text-gray-700 mb-2">
                        Barangay <span class="text-red-500">*</span>
                    </label>
                    <select id="barangay" name="barangay"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">Select Barangay</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay->id }}" {{ old('barangay') == $barangay->id ? 'selected' : '' }}>
                                {{ $barangay->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Password Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#C41E3A] px-6 py-4" style="background: linear-gradient(90deg, #C41E3A 0%, #A01830 100%);">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-lock"></i>
                    Security
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                placeholder="Enter password" minlength="8"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <button type="button" id="toggle-password" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Password Requirements -->
                        <div id="password-requirements" class="mt-3 p-3 bg-gray-50 rounded-lg text-xs space-y-1.5 hidden">
                            <p class="font-medium text-gray-600 mb-2">Password must contain:</p>
                            <div id="req-length" class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i>
                                <span>At least 8 characters</span>
                            </div>
                            <div id="req-upper" class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i>
                                <span>One uppercase letter</span>
                            </div>
                            <div id="req-lower" class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i>
                                <span>One lowercase letter</span>
                            </div>
                            <div id="req-number" class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i>
                                <span>One number</span>
                            </div>
                            <div id="req-special" class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-circle text-[8px]"></i>
                                <span>One special character (!@#$%^&*)</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                placeholder="Confirm password"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <button type="button" id="toggle-confirm-password" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Password Match Indicator -->
                        <div id="password-match" class="mt-3 p-2 rounded-lg text-xs hidden">
                            <span id="match-text" class="flex items-center gap-2"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex flex-row justify-between sm:justify-end gap-4">
            <a href="{{ route('admin.users.index') }}" 
                class="flex-1 sm:flex-none px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-center">
                Cancel
            </a>
            <button type="submit" id="submit-btn" disabled
                class="flex-1 sm:flex-none px-6 py-2.5 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium shadow-sm disabled:bg-red-100 disabled:text-red-800 disabled:border disabled:border-red-200 disabled:cursor-not-allowed">
                <i class="fas fa-user-plus mr-2"></i>Create User
            </button>
        </div>
    </form>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const positionRadios = document.querySelectorAll('input[name="position"]');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const departmentGroup = document.getElementById('department-group');
    const barangayGroup = document.getElementById('barangay-group');
    const departmentInput = document.getElementById('department');
    const barangayInput = document.getElementById('barangay');
    
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordRequirements = document.getElementById('password-requirements');
    const passwordMatch = document.getElementById('password-match');
    const matchText = document.getElementById('match-text');
    const submitBtn = document.getElementById('submit-btn');
    
    const togglePassword = document.getElementById('toggle-password');
    const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
    
    // Toggle password visibility
    [togglePassword, toggleConfirmPassword].forEach((btn, index) => {
        btn.addEventListener('click', function() {
            const input = index === 0 ? passwordInput : confirmPasswordInput;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
    
    // Position change handler
    function handlePositionChange() {
        const selectedPosition = document.querySelector('input[name="position"]:checked');
        
        if (!selectedPosition) {
            departmentGroup.classList.add('hidden');
            departmentInput.required = false;
            departmentInput.value = '';
            barangayGroup.classList.add('hidden');
            barangayInput.required = false;
            barangayInput.value = '';
            return;
        }

        departmentGroup.classList.remove('hidden');
        departmentInput.required = true;
        const isAdministrator = selectedPosition.value === 'Administrator';
        const administratorDepartmentOption = departmentInput.querySelector('[data-administrator-department]');
        const deskOfficerDepartmentOptions = departmentInput.querySelectorAll('[data-desk-officer-department]');

        deskOfficerDepartmentOptions.forEach((option) => {
            option.hidden = isAdministrator;
            option.disabled = isAdministrator;
        });
        if (isAdministrator && administratorDepartmentOption) {
            departmentInput.value = administratorDepartmentOption.value;
        }

        const hideBarangayForAdministrator = @json(auth()->user()->isDepartmentAdmin()) && isAdministrator;
        barangayGroup.classList.toggle('hidden', hideBarangayForAdministrator);
        barangayInput.required = !hideBarangayForAdministrator;
        if (hideBarangayForAdministrator) {
            barangayInput.value = '';
        }

        validateForm();
    }
    
    positionRadios.forEach(radio => {
        radio.addEventListener('change', handlePositionChange);
    });
    
    // Password validation
    const requirements = {
        length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
        upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
        lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
        number: { el: document.getElementById('req-number'), regex: /[0-9]/ },
        special: { el: document.getElementById('req-special'), regex: /[!@#$%^&*]/ }
    };
    
    function checkPasswordRequirements() {
        const password = passwordInput.value;
        let allValid = true;
        
        if (password) {
            passwordRequirements.classList.remove('hidden');
        }
        
        for (const [key, req] of Object.entries(requirements)) {
            const isValid = req.regex.test(password);
            const icon = req.el.querySelector('i');
            
            if (isValid) {
                req.el.classList.remove('text-gray-500');
                req.el.classList.add('text-green-600');
                icon.classList.remove('fa-circle');
                icon.classList.add('fa-check-circle');
            } else {
                req.el.classList.add('text-gray-500');
                req.el.classList.remove('text-green-600');
                icon.classList.add('fa-circle');
                icon.classList.remove('fa-check-circle');
                allValid = false;
            }
        }
        
        return allValid;
    }
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (!confirmPassword) {
            passwordMatch.classList.add('hidden');
            return false;
        }
        
        passwordMatch.classList.remove('hidden');
        
        if (password === confirmPassword && password) {
            passwordMatch.classList.remove('bg-red-50');
            passwordMatch.classList.add('bg-green-50');
            matchText.innerHTML = '<i class="fas fa-check-circle text-green-600"></i><span class="text-green-600">Passwords match</span>';
            return true;
        } else {
            passwordMatch.classList.add('bg-red-50');
            passwordMatch.classList.remove('bg-green-50');
            matchText.innerHTML = '<i class="fas fa-times-circle text-red-600"></i><span class="text-red-600">Passwords do not match</span>';
            return false;
        }
    }
    
    function validateForm() {
        const form = document.getElementById('register-form');
        const selectedPosition = document.querySelector('input[name="position"]:checked');
        
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const username = usernameInput.value.trim();
        const email = emailInput.value.trim();
        
        let positionFieldsValid = true;
        if (selectedPosition) {
            positionFieldsValid = departmentInput.value.trim() !== ''
                && (!barangayInput.required || barangayInput.value !== '');
        } else {
            positionFieldsValid = false;
        }
        
        const passwordValid = checkPasswordRequirements();
        const passwordsMatch = checkPasswordMatch();
        
        const allValid = firstName && lastName && username && email && selectedPosition && positionFieldsValid && passwordValid && passwordsMatch;
        
        submitBtn.disabled = !allValid;
    }
    
    // Event listeners for validation
    passwordInput.addEventListener('input', validateForm);
    passwordInput.addEventListener('focus', () => passwordRequirements.classList.remove('hidden'));
    confirmPasswordInput.addEventListener('input', validateForm);
    document.getElementById('first_name').addEventListener('input', validateForm);
    document.getElementById('last_name').addEventListener('input', validateForm);
    usernameInput.addEventListener('input', validateForm);
    emailInput.addEventListener('input', validateForm);
    departmentInput.addEventListener('change', validateForm);
    barangayInput.addEventListener('change', validateForm);
    
    // Initialize
    handlePositionChange();
    
    // Form submit confirmation & loading state feedback
    const registerForm = document.getElementById('register-form');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const position = document.querySelector('input[name="position"]:checked')?.value;
        
        Swal.fire({
            title: 'Confirm User Registration',
            html: `
                <div class="text-left py-4">
                    <p class="text-gray-600 mb-4">Are you sure you want to create this account?</p>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm space-y-2">
                        <p><strong>Name:</strong> ${firstName} ${lastName}</p>
                        <p><strong>Position:</strong> <span class="px-2 py-0.5 rounded text-xs font-semibold ${position === 'Administrator' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'}">${position}</span></p>
                        <p><strong>Username:</strong> <code class="px-1.5 py-0.5 rounded bg-gray-200 text-xs font-mono">${usernameInput.value}</code></p>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-user-plus mr-1.5"></i> Create User',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#9ca3af',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Creating Account...';
                registerForm.submit();
            }
        });
    });


    @if(session('error') || $errors->any())
    const errorAlert = document.getElementById('error-feedback-alert');
    if (errorAlert) {
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    @endif
});
</script>
@endpush

@endsection
