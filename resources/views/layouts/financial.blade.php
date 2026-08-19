<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Financial Assistance Module - MSWDO Admin')</title>

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
    <link href="{{ asset('css/financialstep1.css') }}" rel="stylesheet">
    @yield('page-styles')
</head>
<body>
    <!-- Reusable Sidebar Component -->
    @include('partials.financial-sidebar')

    <!-- Main Workspace -->
    <div class="main-content">
        <!-- Top Sticky Header -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-lg-none me-3 p-0 text-dark" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                        <i class="fas fa-bars fa-lg"></i>
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
        <div class="p-4" style="flex: 1;">
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
    <script src="{{ asset('js/financialstep1.js') }}"></script>
    @yield('page-scripts')

    @include('admin.partials.account-status')
</body>
</html>
