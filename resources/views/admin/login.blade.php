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
            overflow-y: auto;
            padding: 1.5rem;
        }
        .portal-container {
            width: 100%;
            max-width: 72rem;
            transition: max-width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0 auto;
        }
        /* Default (mobile): login-mode stacks vertically */
        .portal-container.login-mode {
            max-width: 28rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            width: 100%;
        }
        .logo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 3rem;
            transition: margin-bottom 0.3s ease;
        }
        /* In login-mode (mobile/tablet): compact logo above the form */
        .portal-container.login-mode .logo-wrapper {
            margin-bottom: 0;
            flex: 0 0 auto;
            min-width: unset;
        }
        .portal-container.login-mode .logo-img {
            width: 5rem;
            height: 5rem;
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
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 1rem;
            width: 100%;
        }
        @media (max-width: 479px) {
            body { padding: 1rem; }
            body.login-mode-active { padding: 1rem; }
            .welcome-title { font-size: 1.5rem; }
            .welcome-subtitle { font-size: 0.875rem; }
            .login-panel { padding: 1.25rem !important; max-width: 100% !important; width: 100% !important; }
            .logo-img { width: 4rem; height: 4rem; }
            .portal-container.login-mode .logo-img { width: 3.5rem; height: 3.5rem; }
            .portal-container.login-mode { gap: 0.75rem; }
            .portal-container.login-mode .welcome-title { font-size: 1.25rem; }
        }
        @media (min-width: 480px) and (max-width: 767px) {
            .portal-container.login-mode { max-width: 26rem; gap: 1.25rem; }
            .login-panel { max-width: 100% !important; width: 100% !important; }
        }
        @media (min-width: 768px) {
            .roles-grid { grid-template-columns: repeat(2, 1fr); }
            .welcome-title { font-size: 2.25rem; }
            /* Tablet: still vertical stack but wider */
            .portal-container.login-mode { max-width: 32rem; gap: 1.5rem; }
            .login-panel { max-width: 100% !important; width: 100% !important; padding: 2rem; }
            .portal-container.login-mode .logo-img { width: 6rem; height: 6rem; }
        }
        @media (min-width: 992px) {
            .roles-grid { grid-template-columns: repeat(4, 1fr); }
            /* Desktop: go side-by-side */
            .portal-container.login-mode {
                max-width: 80rem;
                flex-direction: row;
                align-items: center;
                gap: 4rem;
            }
            .portal-container.login-mode .logo-wrapper {
                min-width: 15rem;
            }
            .portal-container.login-mode .logo-img { width: 8rem; height: 8rem; }
            .login-panel { max-width: 28rem !important; padding: 2.5rem; flex: 1; }
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
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
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
        .login-mode-toggle {
            display: flex;
            gap: 0.25rem;
            background-color: #F1F5F9;
            border-radius: 0.5rem;
            padding: 0.25rem;
            margin-bottom: 1.5rem;
        }
        .login-mode-btn {
            flex: 1;
            padding: 0.5rem 0.75rem;
            border: none;
            border-radius: 0.375rem;
            background: transparent;
            font-weight: 700;
            font-size: 0.875rem;
            color: #64748B;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .login-mode-btn.active {
            background: #FFFFFF;
            color: #1A237E;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }
        .code-sent-notice {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #EEF2FF;
            border: 1px solid #C7D2FE;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            color: #3730A3;
        }
        .resend-hint {
            text-align: center;
            font-size: 0.875rem;
            color: #64748B;
            margin-top: 1rem;
        }
        .resend-hint button {
            background: none;
            border: none;
            padding: 0;
            color: #1A237E;
            font-weight: 600;
            cursor: pointer;
        }
        .resend-hint button:hover {
            text-decoration: underline;
        }
        .form-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }
        .submit-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .portal-container.login-mode #backToHomepage {
            display: none !important;
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
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .form-input {
            padding-right: 2.75rem;
        }
        .password-toggle {
            position: absolute;
            right: 0.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            color: #94A3B8;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            transition: color 0.2s ease, background-color 0.2s ease;
        }
        .password-toggle:hover {
            color: #1A237E;
            background-color: #EEF2FF;
        }
        .password-toggle svg {
            width: 1.1rem;
            height: 1.1rem;
        }
        .password-wrapper input::-ms-reveal,
        .password-wrapper input::-ms-clear {
            display: none;
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

        /* ══════════════════════════════════════════════
           BREAKPOINTS (Mobile-First)
           xs: 0–767px (default), sm: 768+, md: 992+, lg: 1200+
           ══════════════════════════════════════════════ */
    </style>
</head>
<body>
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

            <div class="login-mode-toggle" id="loginModeToggle">
                <button type="button" class="login-mode-btn active" id="modePasswordBtn" onclick="setLoginMode('password')">Password</button>
                <button type="button" class="login-mode-btn" id="modeCodeBtn" onclick="setLoginMode('code')">Email Code</button>
            </div>

            <form id="loginForm" method="POST" action="{{ route('admin.login') }}">
                @csrf
                <input type="hidden" id="selectedRoleInput" name="role" value="">
                <input type="hidden" name="_debug" value="1">

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" required autocomplete="email" class="form-input" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password" class="form-input">
                        <button type="button" class="password-toggle" id="passwordToggle" onclick="togglePassword()" aria-label="Show password" title="Show password">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
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

            <div id="codeLoginSection" style="display: none;">
                <div id="codeSentNotice" class="code-sent-notice" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1.25rem;height:1.25rem;flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <span>A 6-digit code was sent to <strong id="codeSentEmail"></strong>. Enter it below.</span>
                </div>

                <div id="codeSendStep">
                    <form id="codeSendForm" method="POST" action="{{ route('admin.login.code.send') }}" novalidate>
                        @csrf
                        <input type="hidden" class="js-role-input" name="role" value="">

                        <div class="form-group">
                            <label for="codeEmail" class="form-label">Email Address</label>
                            <input type="email" id="codeEmail" name="email" required autocomplete="email" class="form-input" value="{{ old('email') }}">
                        </div>

                        <div id="codeSendError" class="form-error" style="display: none;"></div>

                        <button type="submit" id="codeSendBtn" class="submit-button">
                            Send Code
                        </button>
                    </form>
                </div>

                <div id="codeVerifyStep" style="display: none;">
                    <div style="margin-top: 1.5rem; border-top: 1px solid #E2E8F0; padding-top: 1.5rem;">
                        <form id="codeVerifyForm" method="POST" action="{{ route('admin.login.code.verify') }}" novalidate>
                            @csrf
                            <input type="hidden" class="js-role-input" name="role" value="">
                            <input type="hidden" id="codeVerifyEmail" name="email" value="">

                            <div class="form-group">
                                <label for="code" class="form-label">Login Code</label>
                                <input type="text" id="code" name="code" required maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="form-input" placeholder="6-digit code">
                            </div>

                            <div id="codeVerifyError" class="form-error" style="display: none;"></div>

                            <button type="submit" id="codeVerifyBtn" class="submit-button">
                                Verify & Sign In
                            </button>
                        </form>
                        <p class="resend-hint">Didn't get it? <button type="button" id="codeResendBtn">Send a new code</button></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let lastSelectedRole = @json(old('role', ''));

        // Restore the login form + role and show error popup when a login attempt failed
        @if ($errors->any() && old('role'))
            (function restoreFailedLogin() {
                function run() {
                    if (!document.getElementById('loginPanel')) return;
                    selectRole(@json(old('role')));
                    @if (session('code_sent'))
                        setLoginMode('code');
                    @endif
                    const messages = @json($errors->all());
                    if (typeof Swal !== 'undefined' && messages.length) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sign in failed',
                            html: '<div style="text-align:center;color:#475569;font-size:15px">' +
                                  messages.map(function(m) { return '<p style="margin:4px 0">' + m + '</p>'; }).join('') +
                                  '</div>',
                            confirmButtonColor: '#1A237E',
                            confirmButtonText: 'Try again',
                            background: '#ffffff',
                            allowOutsideClick: true
                        });
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', run);
                } else {
                    run();
                }
            })();
        @endif

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

        // Email-code login (AJAX — no page reload)
        function currentRole() {
            let role = '';
            document.querySelectorAll('.js-role-input').forEach(function (input) {
                if (input.value) role = input.value;
            });
            return role;
        }

        function showInlineError(elId, message) {
            const el = document.getElementById(elId);
            if (!el) return;
            if (message) {
                el.textContent = message;
                el.style.display = 'block';
            } else {
                el.textContent = '';
                el.style.display = 'none';
            }
        }

        function submitCodeSend(btn) {
            const form = document.getElementById('codeSendForm');
            const emailInput = document.getElementById('codeEmail');
            const role = currentRole();

            if (!role) {
                alert('Please select a role first.');
                return;
            }
            if (!emailInput.value || !emailInput.checkValidity()) {
                showInlineError('codeSendError', 'Please enter a valid email address.');
                return;
            }

            const original = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Sending...';
            showInlineError('codeSendError', '');

            fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: new FormData(form)
            })
            .then(function (res) {
                return res.json().then(function (json) {
                    return { ok: res.ok, json: json };
                });
            })
            .then(function (result) {
                if (result.ok) {
                    const email = emailInput.value.trim();
                    document.getElementById('codeSentEmail').textContent = email;
                    document.getElementById('codeVerifyEmail').value = email;
                    document.getElementById('codeSendStep').style.display = 'none';
                    document.getElementById('codeSentNotice').style.display = 'flex';
                    document.getElementById('codeVerifyStep').style.display = 'block';
                    showInlineError('codeVerifyError', '');
                    setTimeout(function () { document.getElementById('code').focus(); }, 100);
                } else {
                    const errors = result.json.errors || {};
                    const message = errors.email || errors.role || result.json.message || 'Something went wrong. Please try again.';
                    showInlineError('codeSendError', message);
                }
            })
            .catch(function () {
                showInlineError('codeSendError', 'Something went wrong. Please try again.');
            })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = original;
            });
        }

        const codeSendForm = document.getElementById('codeSendForm');
        if (codeSendForm) {
            codeSendForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitCodeSend(document.getElementById('codeSendBtn'));
            });
        }

        const codeResendBtn = document.getElementById('codeResendBtn');
        if (codeResendBtn) {
            codeResendBtn.addEventListener('click', function() {
                submitCodeSend(document.getElementById('codeSendBtn'));
            });
        }

        const codeVerifyForm = document.getElementById('codeVerifyForm');
        if (codeVerifyForm) {
            codeVerifyForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = document.getElementById('codeVerifyBtn');
                const codeInput = document.getElementById('code');
                const role = currentRole();

                if (!role) {
                    alert('Please select a role first.');
                    return;
                }
                if (!codeInput.value || codeInput.value.length !== 6) {
                    showInlineError('codeVerifyError', 'Please enter the 6-digit code.');
                    return;
                }

                const original = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Verifying...';
                showInlineError('codeVerifyError', '');

                fetch(this.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(this)
                })
                .then(function (res) {
                    return res.json().then(function (json) {
                        return { ok: res.ok, json: json };
                    });
                })
                .then(function (result) {
                    if (result.ok) {
                        window.location.href = result.json.redirect;
                    } else {
                        const errors = result.json.errors || {};
                        const message = errors.code || errors.email || errors.role || result.json.message || 'Invalid or expired code.';
                        showInlineError('codeVerifyError', message);
                        codeInput.value = '';
                        codeInput.focus();
                    }
                })
                .catch(function () {
                    showInlineError('codeVerifyError', 'Something went wrong. Please try again.');
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.textContent = original;
                });
            });
        }

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

            // Sync role to email-code forms
            document.querySelectorAll('.js-role-input').forEach(function (input) {
                input.value = roleName;
            });

            // Reset email-code flow so state from another role card doesn't carry over
            resetCodeFlow();
            setLoginMode('password');

            // Clear credentials only when switching to a different role card,
            // so the last used email/password doesn't show on other roles.
            if (roleName !== lastSelectedRole) {
                const emailField = document.getElementById('email');
                const passwordField = document.getElementById('password');
                if (emailField) emailField.value = '';
                if (passwordField) passwordField.value = '';
            }
            lastSelectedRole = roleName;

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

        function setLoginMode(mode) {
            const pwForm = document.getElementById('loginForm');
            const codeSection = document.getElementById('codeLoginSection');
            const pwBtn = document.getElementById('modePasswordBtn');
            const codeBtn = document.getElementById('modeCodeBtn');
            const toggle = document.getElementById('loginModeToggle');

            if (!toggle) return;

            if (mode === 'code') {
                resetCodeFlow();
                pwForm.style.display = 'none';
                codeSection.style.display = 'block';
                pwBtn.classList.remove('active');
                codeBtn.classList.add('active');
                setTimeout(() => {
                    const el = document.getElementById('codeEmail');
                    if (el && el.offsetParent) el.focus();
                }, 100);
            } else {
                pwForm.style.display = 'block';
                codeSection.style.display = 'none';
                codeBtn.classList.remove('active');
                pwBtn.classList.add('active');
            }
        }

        function resetCodeFlow() {
            const sendStep = document.getElementById('codeSendStep');
            const verifyStep = document.getElementById('codeVerifyStep');
            const notice = document.getElementById('codeSentNotice');
            const sendForm = document.getElementById('codeSendForm');
            const codeEmail = document.getElementById('codeEmail');
            if (sendStep) sendStep.style.display = 'block';
            if (verifyStep) verifyStep.style.display = 'none';
            if (notice) notice.style.display = 'none';
            if (sendForm) sendForm.reset();
            if (codeEmail) codeEmail.value = '';
            showInlineError('codeSendError', '');
            showInlineError('codeVerifyError', '');
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

        function togglePassword() {
            const input = document.getElementById('password');
            const btn = document.getElementById('passwordToggle');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/>';
                btn.setAttribute('aria-label', 'Hide password');
                btn.title = 'Hide password';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>';
                btn.setAttribute('aria-label', 'Show password');
                btn.title = 'Show password';
            }
        }
    </script>
</body>
</html>
