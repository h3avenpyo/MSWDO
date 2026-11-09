<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Financial Assistance Module - MSWDO Admin')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/IserveIcon.png') }}">

    <!-- Google Fonts: Public Sans & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- External Module Stylesheet -->
    <link href="{{ asset('css/financialstep1.css') }}?v={{ filemtime(public_path('css/financialstep1.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    @yield('page-styles')
    @php
        $isFinancialStep1 = !request()->is('admin/financial/financialstep2*')
            && session('admin_user_role') !== 'financialstep2';
    @endphp
    @if($isFinancialStep1)
    <link href="{{ asset('css/financial-step1-theme.css') }}?v={{ filemtime(public_path('css/financial-step1-theme.css')) }}" rel="stylesheet">
    @else
    <link href="{{ asset('css/financial-step2-theme.css') }}?v={{ filemtime(public_path('css/financial-step2-theme.css')) }}" rel="stylesheet">
    @endif

    <!-- Anti-flicker pre-render script for collapsed sidebar -->
    <script>
        (function() {
            try {
                const stored = localStorage.getItem('mswdo_financial_sidebar_collapsed');
                const tabletLayout = window.innerWidth >= 768 && window.innerWidth < 1200;
                if (window.innerWidth >= 768 && (stored === 'true' || (stored === null && tabletLayout))) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/logout-modal.css') }}?v={{ filemtime(public_path('css/logout-modal.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/financial-sidebar-desktop-theme.css') }}?v={{ filemtime(public_path('css/financial-sidebar-desktop-theme.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/financial-step-header-desktop.css') }}?v={{ filemtime(public_path('css/financial-step-header-desktop.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/financial-action-desktop.css') }}?v={{ filemtime(public_path('css/financial-action-desktop.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/financial-sidebar-responsive.css') }}?v={{ filemtime(public_path('css/financial-sidebar-responsive.css')) }}">
    @if($isFinancialStep1)
    <link rel="stylesheet" href="{{ asset('css/financial-step1-responsive.css') }}?v={{ filemtime(public_path('css/financial-step1-responsive.css')) }}">
    @endif
</head>
<body class="financial-module{{ $isFinancialStep1 ? ' financial-step1' : ' financial-step2' }}">
    <!-- Reusable Sidebar Component -->
    @include('partials.financial-sidebar')

    <!-- Main Workspace -->
    <div class="main-content">
        <!-- Top Sticky Header -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn financial-menu-toggle d-lg-none me-3 p-0" onclick="toggleSidebar()" aria-label="Open sidebar" aria-controls="sidebar" aria-expanded="false">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="navbar-title">@yield('page-title', 'Financial Assistance Module')</h1>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-muted small d-none d-sm-block fw-medium" id="currentDateTime"></div>
                    @php
                    $userName = session('admin_user_name') ?? 'Officer';
                    $words = explode(' ', $userName);
                    $initials = count($words) >= 2
                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                    : strtoupper(substr($userName, 0, 2));
                    @endphp
                    <div class="navbar-avatar" title="{{ $userName }}">
                        {{ $initials }}
                    </div>
                </div>
            </div>
        </nav>

        <!-- View Body Content -->
        <div class="financial-content-wrapper p-4" style="flex: 1;">
            @yield('content')
        </div>
    </div>

    <!-- Hidden form for secure POST logout -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- External Vendor Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- External Module Script -->
    <script src="{{ asset('js/financialstep1.js') }}?v={{ filemtime(public_path('js/financialstep1.js')) }}"></script>
    @yield('page-scripts')

    @include('admin.partials.account-status', [
        'successConfirmationColor' => '#b11116',
    ])
<script src="{{ asset('js/contact-number-validation.js') }}?v={{ filemtime(public_path('js/contact-number-validation.js')) }}"></script>
</body>
</html>
