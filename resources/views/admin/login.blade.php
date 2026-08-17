<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Gateway - MSWDO Silang</title>
     <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ══════════════════════════════════════════════
           MSWDO iServe Silang — Login Portal
           Mobile-first, CSS Grid + Flexbox, Poppins
           ══════════════════════════════════════════════ */

        /* ---------- Reset & base ---------- */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            color: #1F2937;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            /* Subtle light blue gradient page background */
            background: linear-gradient(160deg, #F0F7FF 0%, #E3EEFA 55%, #DCE9F7 100%);
            /* Entrance animation */
            animation: pageFade .5s ease both;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes pageFade {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Soft radial blur floating behind the card */
        .bg-blur {
            position: fixed;
            z-index: 0;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .45;
            pointer-events: none;
        }
        .bg-blur--one {
            width: 420px; height: 420px;
            top: -80px; left: -80px;
            background: radial-gradient(circle, #BFDBFE 0%, transparent 70%);
        }
        .bg-blur--two {
            width: 480px; height: 480px;
            bottom: -120px; right: -80px;
            background: radial-gradient(circle, #93C5FD 0%, transparent 70%);
        }

        /* ---------- Layout shell ---------- */
        .page-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 960px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: calc(100vh - 4rem);
        }

        .auth-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .12);
            overflow: hidden;
            width: 100%;
            display: grid;
            /* Mobile-first: stacked; later becomes two equal columns */
            grid-template-columns: 1fr;
            animation: cardUp .6s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes cardUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ══════════════════════════════════════════════
           LEFT — Brand panel (blue gradient)
           ══════════════════════════════════════════════ */
        .brand-panel {
            position: relative;
            overflow: hidden;
            color: #FFFFFF;
            padding: 1.75rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
            background: linear-gradient(155deg, #1E3A8A 0%, #1D4ED8 100%);
        }

        /* Quiet radial washes — no outlines, no rings */
        .brand-panel::before,
        .brand-panel::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 60%);
            pointer-events: none;
        }
        .brand-panel::before { width: 420px; height: 420px; top: -160px; right: -140px; }
        .brand-panel::after  { width: 360px; height: 360px; bottom: -160px; left: -140px; }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,.14);
        }

        /* Official-style seal */
        .brand-logo-badge {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            border-radius: 50%;
            padding: 6px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.38);
            box-shadow: 0 6px 18px rgba(0,0,0,.18);
        }
        .brand-logo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: contain;
            background: #FFFFFF;
            padding: 4px;
        }
        .brand-org {
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }
        .brand-label {
            font-size: .6875rem;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            line-height: 1.5;
            color: #FFFFFF;
        }
        .brand-loc {
            font-size: .6563rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.55);
        }

        .brand-hero {
            position: relative;
            z-index: 1;
        }
        .brand-title {
            margin: 0 0 .7rem;
            font-size: clamp(1.6rem, 3.2vw, 2.1rem);
            font-weight: 700;
            letter-spacing: -.01em;
            line-height: 1.12;
            color: #FFFFFF;
        }
        .brand-accent {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin: 0 0 .7rem;
            font-size: .875rem;
            font-weight: 600;
            color: #A7F3D0;
        }
        .brand-desc {
            margin: 0;
            font-size: .8438rem;
            font-weight: 400;
            line-height: 1.7;
            color: rgba(255,255,255,.82);
            max-width: 31rem;
        }

        /* System modules — ledger-style list */
        .module-list {
            position: relative;
            z-index: 1;
            margin-top: .25rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,.14);
        }
        .module-list-label {
            display: flex;
            align-items: center;
            font-size: .6875rem;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(255,255,255,.65);
            margin-bottom: .25rem;
        }
        .module-list-items {
            list-style: none;
            margin: 0;
            padding: 0;
            counter-reset: module;
            display: grid;
            grid-template-columns: 1fr;
        }
        .module-list-items li {
            counter-increment: module;
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: .5rem 0;
            border-bottom: 1px solid rgba(255,255,255,.10);
            font-size: .875rem;
            font-weight: 500;
            color: #FFFFFF;
            line-height: 1.4;
            transition: padding-left .2s ease;
        }
        .module-list-items li:last-child { border-bottom: none; }
        .module-list-items li:hover { padding-left: .45rem; }
        .module-list-items li::before {
            content: counter(module, decimal-leading-zero);
            font-size: .6563rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #6EE7B7;
            min-width: 1.3rem;
            flex-shrink: 0;
        }

        /* ══════════════════════════════════════════════
           RIGHT — Login panel (white)
           ══════════════════════════════════════════════ */
        .login-panel {
            background: #FFFFFF;
            padding: 1.75rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Both login modes share the same grid cell, so the card keeps
           the height of the tallest form and never jumps on mode switch. */
        .login-body {
            display: grid;
        }
        .login-body > .login-mode-form {
            grid-area: 1 / 1;
        }
        .login-mode-form.is-hidden {
            visibility: hidden;
            pointer-events: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            align-self: flex-start;
            color: #64748B;
            font-size: .8438rem;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s ease;
            border-radius: 8px;
            padding: .25rem .35rem;
            margin: -.25rem 0 1rem -.35rem;
        }
        .back-link:hover { color: #1D4ED8; }
        .back-link:focus-visible { outline: 3px solid #93C5FD; outline-offset: 2px; }
        .back-link svg { width: 1rem; height: 1rem; }

        .login-header { margin-bottom: 1rem; }
        .login-title {
            margin: 0 0 .35rem;
            font-size: clamp(1.6rem, 4vw, 2rem);
            font-weight: 700;
            color: #0F172A;
            letter-spacing: -.02em;
        }
        .login-subtitle {
            margin: 0;
            font-size: .9375rem;
            color: #64748B;
        }

        /* Mode toggle (Password / Email Code) */
        .mode-toggle {
            display: flex;
            gap: .25rem;
            background: #EFF6FF;
            border-radius: 14px;
            padding: .3rem;
            margin-bottom: 1rem;
        }
        .mode-btn {
            flex: 1;
            padding: .6rem .75rem;
            border: none;
            border-radius: 11px;
            background: transparent;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: .8438rem;
            color: #64748B;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, box-shadow .2s ease;
        }
        .mode-btn.active {
            background: #FFFFFF;
            color: #1D4ED8;
            box-shadow: 0 2px 6px rgba(30, 58, 138, .12);
        }
        .mode-btn:focus-visible { outline: 3px solid #93C5FD; outline-offset: 2px; }

        /* Form elements */
        .form-group { margin-bottom: 1rem; }
        .form-label {
            display: block;
            font-size: .7813rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: .5rem;
        }
        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 1.05rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.15rem;
            height: 1.15rem;
            color: #94A3B8;
            pointer-events: none;
            transition: color .2s ease;
        }
        .form-input {
            width: 100%;
            height: 56px;
            padding: .9rem 1rem .9rem 3.1rem;
            font-family: 'Poppins', sans-serif;
            font-size: .9375rem;
            color: #1F2937;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            background: #F8FAFC;
            outline: none;
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
        }
        .form-input::placeholder { color: #94A3B8; }
        /* Blue glow on focus */
        .form-input:focus {
            border-color: #1D4ED8;
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(29, 78, 216, .12);
        }
        .form-input:focus-visible { outline: none; }
        .form-input:focus ~ .input-icon { color: #1D4ED8; }

        /* Password reveal button */
        .password-toggle {
            position: absolute;
            right: .4rem;
            top: 50%;
            transform: translateY(-50%);
            width: 44px; height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            color: #94A3B8;
            transition: color .2s ease, background-color .2s ease;
        }
        .password-toggle:hover { color: #1D4ED8; background: #EFF6FF; }
        .password-toggle:focus-visible { outline: 3px solid #93C5FD; outline-offset: 1px; }
        .password-toggle svg { width: 1.15rem; height: 1.15rem; }
        .password-toggle .has-eyes { display: none; }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: .8438rem;
        }
        .remember-me {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: #475569;
            cursor: pointer;
            font-weight: 500;
        }
        .remember-checkbox {
            width: 1.05rem;
            height: 1.05rem;
            accent-color: #1D4ED8;
            cursor: pointer;
        }
        .remember-checkbox:focus-visible { outline: 3px solid #93C5FD; outline-offset: 1px; }
        .forgot-password {
            color: #475569;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s ease;
        }
        .forgot-password:hover { color: #1D4ED8; }
        .forgot-password:focus-visible { outline: 3px solid #93C5FD; outline-offset: 2px; border-radius: 4px; }

        /* Primary CTA */
        .submit-button {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 14px;
            font-family: 'Poppins', sans-serif;
            font-size: .9375rem;
            font-weight: 600;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #FFFFFF;
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 100%);
            box-shadow: 0 10px 20px rgba(29, 78, 216, .25);
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }
        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(29, 78, 216, .32);
            filter: brightness(1.06);
        }
        .submit-button:active { transform: translateY(0); }
        .submit-button:disabled { opacity: .6; cursor: not-allowed; transform: none; }
        .submit-button:focus-visible { outline: 3px solid #93C5FD; outline-offset: 2px; }

        .support-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            margin-top: 1rem;
            color: #64748B;
            font-size: .8438rem;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s ease;
            border-radius: 8px;
            padding: .25rem .35rem;
        }
        .support-link:hover { color: #1D4ED8; }
        .support-link:focus-visible { outline: 3px solid #93C5FD; outline-offset: 2px; }
        .support-link svg { width: 1rem; height: 1rem; }

        /* Errors + email-code notices */
        .form-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            border-radius: 12px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .8438rem;
        }
        .form-error p { margin: 4px 0; }
        .code-sent-notice {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: #1E3A8A;
            border-radius: 12px;
            padding: .8rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .8438rem;
            line-height: 1.5;
        }
        .code-sent-notice svg {
            width: 1.15rem; height: 1.15rem;
            flex-shrink: 0;
            margin-top: .1rem;
        }
        .resend-hint {
            text-align: center;
            font-size: .8438rem;
            color: #64748B;
            margin-top: 1rem;
        }
        .resend-hint button {
            background: none;
            border: none;
            padding: 0;
            color: #1D4ED8;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }
        .resend-hint button:hover { text-decoration: underline; }

        .divider {
            height: 1px;
            background: #E2E8F0;
            margin: 1.1rem 0 0;
        }

        /* ---------- Footer ---------- */
        .footer {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
            font-size: .7813rem;
            font-weight: 500;
            color: #64748B;
            letter-spacing: .02em;
        }

        /* ══════════════════════════════════════════════
           BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* Tablet: 45% / 55% split */
        @media (min-width: 768px) {
            .auth-card { grid-template-columns: 45fr 55fr; }
            .brand-panel { padding: 2rem 1.75rem; }
            .login-panel { padding: 2rem 2rem; }
            .brand-title { font-size: 2.1rem; }
            .module-list-items { grid-template-columns: repeat(2, 1fr); }
        }

        /* Desktop: two equal columns */
        @media (min-width: 1024px) {
            .auth-card { grid-template-columns: 1fr 1fr; }
            .brand-panel { padding: 2.25rem 2.25rem; }
            .login-panel { padding: 2.25rem 2.75rem; }
        }
    </style>
</head>
<body>
    <!-- Decorative background blurs -->
    <div class="bg-blur bg-blur--one" aria-hidden="true"></div>
    <div class="bg-blur bg-blur--two" aria-hidden="true"></div>

    <div class="page-wrapper">
        <div class="auth-card">

            <!-- ═══════════ LEFT: Brand panel ═══════════ -->
            <aside class="brand-panel" aria-label="Platform information">
                <div class="brand-top">
                    @php
                        $logo = null;
                        if (file_exists(public_path('images/mswdo-logo.png'))) {
                            $logo = 'mswdo-logo.png';
                        } else {
                            $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
                            if (!empty($files)) {
                                $logo = basename($files[0]);
                            }
                        }
                    @endphp

                    <div class="brand-logo-badge">
                        @if($logo)
                            <img src="{{ asset('images/'.$logo) }}" class="brand-logo" alt="MSWDO Logo">
                        @else
                            <div class="brand-logo" style="display:flex;align-items:center;justify-content:center;color:#1E3A8A;font-weight:700;font-size:1.4rem;">M</div>
                        @endif
                    </div>
                    <div class="brand-org">
                        <span class="brand-label">Municipal Social Welfare &amp; Development Office</span>
                        <span class="brand-loc">Municipality of Silang, Cavite</span>
                    </div>
                </div>

                <div class="brand-hero">
                    <h1 class="brand-title">Welcome to iServe Silang</h1>
                    <p class="brand-accent">
                        Integrated Municipal Social Welfare Management Platform
                    </p>
                    <p class="brand-desc">
                        A secure, centralized portal that empowers the MSWDO Silang to manage
                        social services, financial assistance, and citizen welfare programs —
                        all in one modern, easy-to-use workspace.
                    </p>
                </div>

<<<<<<< HEAD
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
                <p class="role-description">Process emergency cash grants, medical aid, financial step 1 & 2 reviews.</p>
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
=======
                <div class="module-list">
                    <span class="module-list-label">System Modules</span>
                    <ul class="module-list-items">
                        <li>Senior Citizen Management</li>
                        <li>Financial Assistance</li>
                        <li>Social Case Study</li>
                        <li>VAWC Case Management</li>
                        <li>BCPC Monitoring</li>
>>>>>>> 5c79a03401b44599faa0ee97242d93d2ff55b903
                    </ul>
                </div>
            </aside>

            <!-- ═══════════ RIGHT: Login form ═══════════ -->
            <section class="login-panel" aria-label="Login form">
                <a href="/" class="back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back to Homepage
                </a>

                <div class="login-header">
                    <h2 class="login-title">Sign In</h2>
                    <p class="login-subtitle">Please enter your credentials below.</p>
                </div>

                @if ($errors->any())
                    <div class="form-error" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mode-toggle" id="loginModeToggle">
                    <button type="button" class="mode-btn active" id="modePasswordBtn" onclick="setLoginMode('password')">Password</button>
                    <button type="button" class="mode-btn" id="modeCodeBtn" onclick="setLoginMode('code')">Email Code</button>
                </div>

                <!-- Password login -->
                <div class="login-body">
                    <form id="loginForm" class="login-mode-form" method="POST" action="{{ route('admin.login') }}" novalidate>
                        @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <input type="email" id="email" name="email" required autocomplete="email" class="form-input" placeholder="admin@mswdo.gov.ph" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrap">
                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <input type="password" id="password" name="password" required autocomplete="current-password" class="form-input" placeholder="Enter your password">
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

                    <!-- Email-code login -->
                    <div id="codeLoginSection" class="login-mode-form is-hidden">
                    <div id="codeSentNotice" class="code-sent-notice" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <span>A 6-digit code was sent to <strong id="codeSentEmail"></strong>. Enter it below.</span>
                    </div>

                    <div id="codeSendStep">
                        <form id="codeSendForm" method="POST" action="{{ route('admin.login.code.send') }}" novalidate>
                            @csrf
                            <div class="form-group">
                                <label for="codeEmail" class="form-label">Email Address</label>
                                <div class="input-wrap">
                                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                    <input type="email" id="codeEmail" name="email" required autocomplete="email" class="form-input" placeholder="admin@mswdo.gov.ph" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div id="codeSendError" class="form-error" style="display: none;" role="alert"></div>

                            <button type="submit" id="codeSendBtn" class="submit-button">
                                Send Code
                            </button>
                        </form>
                    </div>

                    <div id="codeVerifyStep" style="display: none;">
                        <div style="border-top: 1px solid #E2E8F0; padding-top: 1.25rem;">
                            <form id="codeVerifyForm" method="POST" action="{{ route('admin.login.code.verify') }}" novalidate>
                                @csrf
                                <input type="hidden" id="codeVerifyEmail" name="email" value="">

                                <div class="form-group">
                                    <label for="code" class="form-label">Login Code</label>
                                    <div class="input-wrap">
                                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>
                                        <input type="text" id="code" name="code" required maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="form-input" placeholder="6-digit code">
                                    </div>
                                </div>

                                <div id="codeVerifyError" class="form-error" style="display: none;" role="alert"></div>

                                <button type="submit" id="codeVerifyBtn" class="submit-button">
                                    Verify &amp; Sign In
                                </button>
                            </form>
                            <p class="resend-hint">Didn't get it? <button type="button" id="codeResendBtn">Send a new code</button></p>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="divider"></div>

                <a href="#" class="support-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 9 16.5M12 21a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 15 16.5m-3-8.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Having trouble? Contact Support
                </a>
            </section>
        </div>

        <!-- Footer -->
        <footer class="footer">
            MSWDO Silang &middot; Municipal Social Welfare and Development Office
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ══════════════════════════════════════════════
        //  Welcome popup — shown once per browser
        // ══════════════════════════════════════════════
        (function showWelcomeOnce() {
            if (localStorage.getItem('mswdo_welcome_seen')) return;
            function tryPopup() {
                if (typeof Swal === 'undefined') { setTimeout(tryPopup, 100); return; }
                localStorage.setItem('mswdo_welcome_seen', '1');
                Swal.fire({
                    title: 'Welcome to MSWDO Silang Portal!',
                    html: '<div style="text-align:center;line-height:1.7;color:#475569;font-size:15px">' +
                          '<p style="margin:0 0 8px">Your centralized platform for social welfare management.</p>' +
                          '<p style="margin:0;font-size:13px;color:#94A3B8">Sign in below with your account to continue.</p>' +
                          '</div>',
                    icon: 'info',
                    confirmButtonColor: '#1D4ED8',
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

        // ══════════════════════════════════════════════
        //  Inline error helper
        // ══════════════════════════════════════════════
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

        // ══════════════════════════════════════════════
        //  Email-code login (AJAX — no page reload)
        // ══════════════════════════════════════════════
        function submitCodeSend(btn) {
            const form = document.getElementById('codeSendForm');
            const emailInput = document.getElementById('codeEmail');

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

        // ══════════════════════════════════════════════
        //  Mode switching: Password / Email Code
        // ══════════════════════════════════════════════
        function setLoginMode(mode) {
            const pwForm = document.getElementById('loginForm');
            const codeSection = document.getElementById('codeLoginSection');
            const pwBtn = document.getElementById('modePasswordBtn');
            const codeBtn = document.getElementById('modeCodeBtn');
            const toggle = document.getElementById('loginModeToggle');

            if (!toggle) return;

            if (mode === 'code') {
                resetCodeFlow();
                pwForm.classList.add('is-hidden');
                codeSection.classList.remove('is-hidden');
                pwBtn.classList.remove('active');
                codeBtn.classList.add('active');
                setTimeout(() => {
                    const el = document.getElementById('codeEmail');
                    if (el && el.offsetParent) el.focus();
                }, 100);
            } else {
                pwForm.classList.remove('is-hidden');
                codeSection.classList.add('is-hidden');
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

        // ══════════════════════════════════════════════
        //  Password visibility toggle
        // ══════════════════════════════════════════════
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
