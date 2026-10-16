<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'iSERVE SILANG Admin')</title>
    <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table-action-controls.css') }}?v={{ filemtime(public_path('css/table-action-controls.css')) }}">
    @stack('styles')
    <script>
        document.documentElement.classList.add('js');
    </script>
    <link rel="stylesheet" href="{{ asset('css/logout-modal.css') }}?v={{ filemtime(public_path('css/logout-modal.css')) }}">
</head>
<body>
    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="{{ asset('images/white-ribbon.png') }}" alt="WCPU Logo" class="logo-img" width="35" height="35">
                <h1 class="logo-text">WCPU</h1>
                <button class="toggle-sidebar" aria-label="Toggle sidebar" title="Toggle sidebar">
                    <i class="fas fa-angles-left" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('admin-dashboard') }}" class="menu-item {{ request()->routeIs('admin-dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span class="menu-text">Dashboard</span>
            </a>
            <a href="{{ route('admin-barangay-cases') }}" class="menu-item {{ request()->routeIs('admin-barangay-cases') ? 'active' : '' }}" data-tooltip="Barangay Cases">
                <i class="fas fa-map-marked-alt"></i>
                <span class="menu-text">Barangay Cases</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" data-tooltip="Users Table">
                <i class="fas fa-users"></i>
                <span class="menu-text">Users Table</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="menu-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" data-tooltip="Register User">
                <i class="fas fa-user-plus"></i>
                <span class="menu-text">Register User</span>
            </a>
            <a href="{{ route('admin.users.logs', ['user' => auth()->user() ? auth()->user()->id : 1]) }}" class="menu-item {{ request()->routeIs('admin.users.logs') ? 'active' : '' }}" data-tooltip="User Logs">
                <i class="fas fa-history"></i>
                <span class="menu-text">User Logs</span>
            </a>
            <a href="{{ route('admin.users.archived') }}" class="menu-item {{ request()->routeIs('admin.users.archived') ? 'active' : '' }}" data-tooltip="Archived Users">
                <i class="fas fa-archive"></i>
                <span class="menu-text">Archived Users</span>
            </a>
            <a href="{{ route('admin-add-new-case') }}" class="menu-item {{ request()->routeIs('admin-add-new-case') ? 'active' : '' }}" data-tooltip="Add New Case">
                <i class="fas fa-plus-circle"></i>
                <span class="menu-text">Add New Case</span>
            </a>
            <div class="menu-footer">
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                    @csrf
                </form>
                <button type="button" id="logout-button" class="logout-button w-100">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="menu-text">Logout</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    @stack('scripts')
    <script src="{{ asset('js/table-action-controls.js') }}?v={{ filemtime(public_path('js/table-action-controls.js')) }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success') || session('error'))
    <script>
    const showFlashModal = function () {
        if (!window.Swal) return;
        window.Swal.fire({
            icon: @json(session('success') ? 'success' : 'error'),
            title: @json(session('success') ? 'Success' : 'Error'),
            text: @json(session('success') ?: session('error')),
            confirmButtonColor: '#7e22ce'
        });
    };
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', showFlashModal) : showFlashModal();
    </script>
    @endif
    
    <!-- Logout Confirmation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle logout button click
            const logoutForm = document.getElementById('logout-form');
            const logoutButton = document.getElementById('logout-button');
            
            if (logoutButton) {
                logoutButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Logout',
        html: '<svg class="logout-modal-symbol" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M12 5l7 7-7 7v-4H7V9h5V5Z" fill="currentColor"/></svg><p class="logout-modal-message">Are you sure you want to log out?</p>',
        showCancelButton: true,
                        confirmButtonText: 'Yes, log me out',
                        cancelButtonText: 'Cancel',
                        focusConfirm: false,
                        showClass: {
                            popup: 'animate__animated animate__fadeIn animate__faster',
                            backdrop: 'swal2-backdrop-show',
                            icon: 'swal2-icon-show'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster',
                            backdrop: 'swal2-backdrop-hide',
                            icon: 'swal2-icon-hide'
                        },
                        customClass: {
                            confirmButton: 'swal2-confirm',
                            cancelButton: 'swal2-cancel',
                            popup: 'shadow-lg app-logout-modal',
                            container: 'swal2-container',
                            title: 'text-xl font-semibold',
                            htmlContainer: 'text-gray-600',
                            actions: 'mt-4'
                        },
                        buttonsStyling: true,
                        reverseButtons: true,
                        confirmButtonColor: '#4a00e0',
                        cancelButtonColor: '#f8f9fa',
                        allowOutsideClick: false,
                        allowEscapeKey: true,
                        allowEnterKey: false,
                        showCloseButton: false,
                        showLoaderOnConfirm: false,
                        scrollbarPadding: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show success message before submitting the form
                            Swal.fire({
                                title: 'Logging out...',
                                html: '<div class="text-center">\
                                    <div class="mb-4">\
                                        <i class="fas fa-check-circle text-4xl text-green-500"></i>\
                                    </div>\
                                    <p class="text-gray-700">You have been successfully logged out.</p>\
                                </div>',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                showClass: {
                                    popup: 'animate__animated animate__fadeIn animate__faster',
                                    backdrop: 'swal2-backdrop-show',
                                    icon: 'swal2-icon-show'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOut animate__faster',
                                    backdrop: 'swal2-backdrop-hide',
                                    icon: 'swal2-icon-hide'
                                },
                                customClass: {
                                    popup: 'shadow-lg',
                                    container: 'swal2-container',
                                    title: 'text-xl font-semibold',
                                    htmlContainer: 'text-gray-600',
                                    timerProgressBar: 'bg-blue-500'
                                },
                                willClose: () => {
                                    // Submit the logout form after the success message
                                    if (logoutForm) {
                                        logoutForm.submit();
                                    }
                                }
                            });
                        }
                    });
                });
            }
        });
    </script>
@include('components.inactivity-timeout')
<script src="{{ asset('js/contact-number-validation.js') }}?v={{ filemtime(public_path('js/contact-number-validation.js')) }}"></script>
</body>
</html>
