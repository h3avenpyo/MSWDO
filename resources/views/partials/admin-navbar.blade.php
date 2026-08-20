<!-- Top Navigation -->
<nav class="top-navbar">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link d-lg-none d-xl-none" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="navbar-title">@yield('navbar-title', 'Dashboard')</h1>
        </div>
        <div class="navbar-right">
            <div class="navbar-datetime" id="currentDateTime"></div>
            <div class="navbar-avatar">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
        </div>
    </div>
</nav>
