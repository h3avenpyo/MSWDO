<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Gateway - MSWDO Silang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #F8FAFC; /* Clean off-white */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 2rem 1.5rem;
            box-sizing: border-box;
            transition: padding 0.3s ease;
        }
        body.login-mode-active {
            height: 100vh;
            overflow: hidden;
            padding: 0 1.5rem;
        }
        .portal-container {
            width: 100%;
            max-width: 72rem;
            transition: max-width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0 auto;
        }
        .portal-container.login-mode {
            max-width: 80rem;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 4rem;
        }
        .logo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 3rem;
            transition: margin-bottom 0.3s ease;
        }
        .portal-container.login-mode .logo-wrapper {
            margin-bottom: 0;
            flex: 0 0 auto;
            min-width: 15rem;
        }
        .portal-container.login-mode .logo-img {
            width: 8rem;
            height: 8rem;
        }
        .logo-img {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            object-fit: contain;
            border: none;
            padding: 0;
            margin-bottom: 1rem;
            background-color: transparent;
        }
        .logo-placeholder {
            width: 4.5rem;
            height: 4.5rem;
            border-radius: 50%;
            background-color: #1A237E;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .welcome-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1A237E;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.03em;
        }
        .welcome-subtitle {
            font-size: 1.0625rem;
            color: #475569;
            margin: 0;
            max-width: 32rem;
            line-height: 1.5;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 1rem;
            width: 100%;
        }
        @media (max-width: 1024px) {
            .roles-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .portal-container.login-mode {
                gap: 2rem;
            }
        }
        @media (max-width: 640px) {
            .roles-grid {
                grid-template-columns: 1fr;
            }
            .portal-container.login-mode {
                flex-direction: column;
            }
        }
        .role-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1 / 1;
            min-width: 0;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .role-card:hover {
            transform: translateY(-4px);
            border-color: #1A237E;
            box-shadow: 0 10px 20px -5px rgba(26, 35, 126, 0.08);
        }
        .role-icon-wrapper {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.75rem;
            background-color: #F1F5F9;
            color: #1A237E;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: background-color 0.25s ease, color 0.25s ease;
        }
        .role-card:hover .role-icon-wrapper {
            background-color: #1A237E;
            color: #FFFFFF;
        }
        .role-icon {
            width: 1.75rem;
            height: 1.75rem;
        }
        .role-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1F2937;
            margin: 0 0 0.5rem 0;
            line-height: 1.4;
        }
        .role-description {
            font-size: 0.875rem;
            color: #64748B;
            margin: 0;
            line-height: 1.5;
        }

        /* Login Card Panel */
        .login-panel {
            display: none;
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            flex: 1;
            max-width: 28rem;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login-header {
            margin-bottom: 2rem;
        }
        .back-button {
            background: none;
            border: none;
            color: #475569;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0;
            margin-bottom: 1.5rem;
            transition: color 0.2s ease;
        }
        .back-button:hover {
            color: #1A237E;
        }
        .login-title {
            font-size: 1.375rem;
            font-weight: 800;
            color: #1A237E;
            margin: 0 0 0.25rem 0;
        }
        .login-subtitle {
            font-size: 0.875rem;
            color: #64748B;
            margin: 0;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
        }
        .form-input {
            width: 100%;
            box-sizing: border-box;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #CBD5E1;
            border-radius: 0.5rem;
            outline: none;
            background-color: #F8FAFC;
            color: #1F2937;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }
        .form-input:focus {
            border-color: #1A237E;
            background-color: #FFFFFF;
        }
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #334155;
            cursor: pointer;
        }
        .remember-checkbox {
            accent-color: #1A237E;
            width: 1rem;
            height: 1rem;
            cursor: pointer;
        }
        .forgot-password {
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .forgot-password:hover {
            color: #1A237E;
        }
        .submit-button {
            width: 100%;
            background-color: #1A237E;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
            box-sizing: border-box;
        }
        .submit-button:hover {
            background-color: #111827;
        }
        .submit-button:active {
            transform: translateY(1px);
        }


        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #F8FAFC;
            z-index: 9999;
            opacity: 1;
            transition: opacity 1s ease-in-out;
        }
        .loading-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* Skeleton Loading */
        .skeleton {
            background: linear-gradient(90deg, #E2E8F0 25%, #F1F5F9 50%, #E2E8F0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 0.5rem;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .skeleton-wrapper {
            width: 100%;
            max-width: 72rem;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .skeleton-logo {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .skeleton-title {
            width: 24rem;
            max-width: 80%;
            height: 2.25rem;
            margin-bottom: 0.5rem;
        }
        .skeleton-subtitle {
            width: 30rem;
            max-width: 60%;
            height: 1rem;
            margin-bottom: 3rem;
        }
        .skeleton-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            width: 100%;
        }
        @media (max-width: 1024px) {
            .skeleton-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 640px) {
            .skeleton-grid {
                grid-template-columns: 1fr;
            }
        }
        .skeleton-card-container {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1 / 1;
            min-width: 0;
        }
        .skeleton-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .skeleton-text-1 {
            width: 80%;
            height: 1.125rem;
            margin-bottom: 0.75rem;
        }
        .skeleton-text-2 {
            width: 95%;
            height: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .skeleton-text-3 {
            width: 70%;
            height: 0.875rem;
        }
        .portal-container.login-mode #backToHomepage {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="skeleton-wrapper">
            <div class="skeleton skeleton-logo"></div>
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-subtitle"></div>
            <div class="skeleton-grid">
                <div class="skeleton-card-container">
                    <div class="skeleton skeleton-icon"></div>
                    <div class="skeleton skeleton-text-1"></div>
                    <div class="skeleton skeleton-text-2"></div>
                    <div class="skeleton skeleton-text-3"></div>
                </div>
                <div class="skeleton-card-container">
                    <div class="skeleton skeleton-icon"></div>
                    <div class="skeleton skeleton-text-1"></div>
                    <div class="skeleton skeleton-text-2"></div>
                    <div class="skeleton skeleton-text-3"></div>
                </div>
                <div class="skeleton-card-container">
                    <div class="skeleton skeleton-icon"></div>
                    <div class="skeleton skeleton-text-1"></div>
                    <div class="skeleton skeleton-text-2"></div>
                    <div class="skeleton skeleton-text-3"></div>
                </div>
                <div class="skeleton-card-container">
                    <div class="skeleton skeleton-icon"></div>
                    <div class="skeleton skeleton-text-1"></div>
                    <div class="skeleton skeleton-text-2"></div>
                    <div class="skeleton skeleton-text-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="portalContainer" class="portal-container">
        <!-- Header -->
        <div class="logo-wrapper">
            @php
                $logo = null;
                if(file_exists(public_path('images/mswdo-logo.png'))){
                    $logo = 'mswdo-logo.png';
                } else {
                    $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
                    if(!empty($files)) {
                        $logo = basename($files[0]);
                    }
                }
            @endphp

            @if($logo)
                <img src="{{ asset('images/'.$logo) }}" class="logo-img" alt="MSWDO Logo">
            @else
                <div class="logo-placeholder">M</div>
            @endif

            <h1 class="welcome-title">MSWDO Silang Portal</h1>
            <p class="welcome-subtitle" id="portalSubtitle">Please select your administrative role to sign in and access the dashboard.</p>
        </div>

        <!-- Role Selection Panel -->
        <div id="roleSelectionPanel" class="roles-grid">
            <!-- Card 1: Social Case Study -->
            <div class="role-card" onclick="selectRole('Social Case Study')">
                <div class="role-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="role-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-1.5 3h1.5m-7.5-3h7.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                    </svg>
                </div>
                <h3 class="role-title">Social Case Study</h3>
                <p class="role-description">Manage resident assessments, case files, and community welfare study plans.</p>
            </div>

            <!-- Card 2: Senior Citizen -->
            <div class="role-card" onclick="selectRole('Senior Citizen')">
                <div class="role-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="role-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m0 0-.003-.034A3.003 3.003 0 0 1 10.5 16.5a12.002 12.002 0 0 1 3 0 3.003 3.003 0 0 1 4.5 2.188M11.611 3.116a3 3 0 1 1-2.122 2.122 3 3 0 0 1 2.122-2.122ZM18.75 8.25a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0ZM5.25 8.25a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <h3 class="role-title">Senior Citizen</h3>
                <p class="role-description">Manage senior programs, pension distributions, registration, and benefits tracking.</p>
            </div>

            <!-- Card 3: Financial Assistance Officer -->
            <div class="role-card" onclick="selectRole('Financial Assistance Officer')">
                <div class="role-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="role-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5m-16.5 3h16.5M3.75 14.25h16.5M5.25 18.75h9m-9 0a2.25 2.25 0 0 1-2.25-2.25V6.75m2.25 12a2.25 2.25 0 0 0 2.25-2.25V6.75m8.25 12a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0ZM15 7.5v6.75m0-6.75a2.25 2.25 0 1 1 4.5 0 2.25 2.25 0 0 1-4.5 0Zm0 6.75a2.25 2.25 0 1 1 4.5 0 2.25 2.25 0 0 1-4.5 0Z" />
                    </svg>
                </div>
                <h3 class="role-title">Financial Assistance Officer</h3>
                <p class="role-description">Process emergency cash grants, medical aid, and AICS distribution reviews.</p>
            </div>

            <!-- Card 4: Admin -->
            <div class="role-card" onclick="selectRole('Admin')">
                <div class="role-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="role-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="role-title">Admin</h3>
                <p class="role-description">Full system access, user management, and administrative controls.</p>
            </div>
        </div>

        <div id="backToHomepage" style="text-align: center; margin-top: 1.5rem;">
            <a href="/" style="color: #64748B; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s ease; display: inline-flex; align-items: center; gap: 0.375rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Back to Homepage
            </a>
        </div>

        <!-- Login Form Panel (Hidden initially) -->
        <div id="loginPanel" class="login-panel">
            <button class="back-button" onclick="showRoles()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Roles
            </button>
            
            <div class="login-header">
                <h2 class="login-title" id="loginTitle">Sign In</h2>
                <p class="login-subtitle">Please enter your credentials below.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 0.5rem; background-color: #FEE2E2; border: 1px solid #FECACA; color: #991B1B;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('admin.login') }}">
                @csrf
                <input type="hidden" id="selectedRoleInput" name="role" value="">
                <input type="hidden" name="_debug" value="1">

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" required autocomplete="email" class="form-input">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" class="form-input">
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" class="remember-checkbox">
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="submit-button">
                    Sign In
                </button>


            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Hide loading overlay when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loadingOverlay').classList.add('hidden');
            }, 1000);
        });

        // Welcome popup — shows once ever per browser
        (function showWelcomeOnce() {
            if (localStorage.getItem('mswdo_welcome_seen')) return;
            function tryPopup() {
                if (typeof Swal === 'undefined') { setTimeout(tryPopup, 100); return; }
                localStorage.setItem('mswdo_welcome_seen', '1');
                Swal.fire({
                    title: 'Welcome to MSWDO Silang Portal!',
                    html: '<div style="text-align:center;line-height:1.7;color:#475569;font-size:15px">' +
                          '<p style="margin:0 0 8px">Your centralized platform for social welfare management.</p>' +
                          '<p style="margin:0;font-size:13px;color:#94A3B8">Select your role below to get started.</p>' +
                          '</div>',
                    icon: 'info',
                    confirmButtonColor: '#1A237E',
                    confirmButtonText: 'Get Started',
                    background: '#ffffff',
                    customClass: { popup: 'rounded-4 shadow-lg' },
                    allowOutsideClick: false
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() { setTimeout(tryPopup, 800); });
            } else {
                setTimeout(tryPopup, 800);
            }
        })();

        // Add form submission debugging
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const role = document.getElementById('selectedRoleInput').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            console.log('Form submitting...');
            console.log('Role:', role);
            console.log('Email:', email);
            console.log('Password length:', password.length);
            
            if (!role) {
                e.preventDefault();
                alert('Please select a role first.');
                return false;
            }
            
            return true;
        });

        function selectRole(roleName) {
            const container = document.getElementById('portalContainer');
            const rolePanel = document.getElementById('roleSelectionPanel');
            const loginPanel = document.getElementById('loginPanel');
            const subtitle = document.getElementById('portalSubtitle');
            const roleInput = document.getElementById('selectedRoleInput');
            const loginTitle = document.getElementById('loginTitle');

            // Set role in input and title
            roleInput.value = roleName;
            loginTitle.innerText = roleName + " Login";

            // Add class for layout transition
            container.classList.add('login-mode');
            document.body.classList.add('login-mode-active');
            
            // Hide role selection, show login form
            rolePanel.style.display = 'none';
            loginPanel.style.display = 'block';
            subtitle.style.visibility = 'hidden';

            // Focus email field
            setTimeout(() => {
                document.getElementById('email').focus();
            }, 100);
        }

        function showRoles() {
            const container = document.getElementById('portalContainer');
            const rolePanel = document.getElementById('roleSelectionPanel');
            const loginPanel = document.getElementById('loginPanel');
            const subtitle = document.getElementById('portalSubtitle');

            // Remove class for layout transition
            container.classList.remove('login-mode');
            document.body.classList.remove('login-mode-active');

            // Hide login form, show role selection
            loginPanel.style.display = 'none';
            rolePanel.style.display = 'grid';
            subtitle.style.visibility = 'visible';
        }
    </script>
</body>
</html>
