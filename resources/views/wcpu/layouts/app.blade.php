<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SWD Admin') }} - @yield('title')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts - Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Custom SweetAlert2 styles */
        .swal2-popup {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            font-family: 'Montserrat', sans-serif;
            padding: 2rem;
        }
        
        .swal2-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        
        .swal2-html-container {
            font-size: 1rem;
            color: #4a5568;
            margin: 1rem 0;
        }
        
        .swal2-actions {
            margin: 1.5rem 0 0 0;
            gap: 0.75rem;
        }
        
        .swal2-styled.swal2-confirm {
            background-color: #4a00e0;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .swal2-styled.swal2-confirm:hover {
            background-color: #3a00b3;
            transform: translateY(-1px);
        }
        
        .swal2-styled.swal2-cancel {
            background-color: #fff;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .swal2-styled.swal2-cancel:hover {
            background-color: #f7fafc;
            border-color: #cbd5e0;
            transform: translateY(-1px);
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #4a00e0 0%, #8e2de2 100%);
            color: white;
            width: 250px;
            transition: all 0.3s;
            position: fixed;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            margin-left: -250px;
        }
        .main-content.expanded {
            width: 100%;
            margin-left: 0;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }
        .btn-primary {
            background-color: #4a00e0;
            border-color: #4a00e0;
        }
        .btn-primary:hover {
            background-color: #3a00b3;
            border-color: #3a00b3;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-top: none;
        }
        .badge {
            padding: 0.5em 0.8em;
            font-weight: 500;
            border-radius: 4px;
        }
    </style>
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/table-action-controls.css') }}?v={{ filemtime(public_path('css/table-action-controls.css')) }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="p-4">
                <h4 class="text-center mb-4">SWD Admin</h4>
                <ul class="nav flex-column">
                    @if(Auth::check())
                        @if(Auth::user()->position === 'Administrator')
                            <li class="nav-item">
                                <a href="{{ route('admin-dashboard') }}" class="nav-link text-white">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin-barangay-cases') }}" class="nav-link text-white">
                                    <i class="fas fa-folder-open me-2"></i> Barangay Cases
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link text-white">
                                    <i class="fas fa-users me-2"></i> Users
                                </a>
                            </li>
                        @elseif(Auth::user()->position === 'Desk Officer')
                            <li class="nav-item">
                                <a href="{{ route('desk-officer.dashboard') }}" class="nav-link text-white {{ request()->routeIs('desk-officer.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('desk-officer.cases.index') }}" class="nav-link text-white {{ request()->routeIs('desk-officer.cases.*') ? 'active' : '' }}">
                                    <i class="fas fa-folder-open me-2"></i> Cases
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('desk-officer.referrals') }}" class="nav-link text-white {{ request()->routeIs('desk-officer.referrals*') ? 'active' : '' }}">
                                    <i class="fas fa-share-square me-2"></i> Referrals
                                </a>
                            </li>
                        @endif
                    @endif
                    <li class="nav-item mt-4">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                            @csrf
                        </form>
                        <button type="button" id="logout-button" class="btn btn-light w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="container-fluid">
                    <button class="btn" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="d-flex align-items-center">
                        <span class="me-3">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'A') }}&background=4a00e0&color=fff" 
                             class="rounded-circle" width="40" height="40" alt="User">
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid py-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        });
    </script>
    
    @stack('scripts')
    <script src="{{ asset('js/table-action-controls.js') }}?v={{ filemtime(public_path('js/table-action-controls.js')) }}"></script>
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
                        html: '<div class="text-center">\
                            <div class="mb-4">\
                                <i class="fas fa-sign-out-alt text-4xl text-purple-500 mb-3"></i>\
                            </div>\
                            <p class="text-gray-700">Are you sure you want to log out?</p>\
                        </div>',
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
                            popup: 'shadow-lg',
                            container: 'swal2-container',
                            title: 'text-xl font-semibold',
                            htmlContainer: 'text-gray-600',
                            actions: 'mt-4'
                        },
                        buttonsStyling: true,
                        reverseButtons: true,
                        confirmButtonColor: '#4a00e0',
                        cancelButtonColor: '#f8f9fa',
                        cancelButtonText: '<span class="text-gray-700">Cancel</span>',
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
</body>
</html>
