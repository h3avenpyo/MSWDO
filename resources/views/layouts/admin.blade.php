<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MSWDO Admin')</title>
    @vite(['resources/css/admin-compat.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @yield('page-styles')
</head>
<body>
    @hasSection('sidebar')
        @yield('sidebar')
    @else
        @include('partials.admin-sidebar')
    @endif
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-content">
        @include('partials.admin-navbar')

        <div class="p-4" style="flex: 1;">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        document.addEventListener('click', function (e) {
            var dismiss = e.target.closest('[data-bs-dismiss="alert"]');
            if (dismiss) {
                var alert = dismiss.closest('.alert');
                if (alert) alert.remove();
            }
        });
    </script>
    @yield('page-scripts')

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
