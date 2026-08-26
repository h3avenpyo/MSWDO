<style>
    /* ══════════════════════════════════════════════════════════
       SENIOR CITIZEN MODULE — SHARED NAVIGATION / LAYOUT WRAPPER
       (Identical responsive system used by the Senior Dashboard)
       ══════════════════════════════════════════════════════════ */

    /* Uniform SweetAlert & Custom Modal Styles */
    .swal2-popup {
        border-radius: 16px !important;
        padding: 1.5rem !important;
        font-family: inherit !important;
        max-width: 95vw !important;
        box-sizing: border-box !important;
    }
    .swal2-title {
        color: #1A237E !important;
        font-weight: 700 !important;
        font-size: 1.35rem !important;
    }
    .swal2-html-container {
        font-size: 0.925rem !important;
        color: #374151 !important;
        max-height: 75vh !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .swal2-confirm {
        background-color: #1A237E !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 10px 24px !important;
    }
    .swal2-cancel {
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 10px 24px !important;
    }
    .swal2-actions {
        flex-direction: row !important;
        gap: 12px;
    }
    @media (max-width: 575.98px) {
        .swal2-popup {
            padding: 1rem 0.75rem !important;
            border-radius: 12px !important;
        }
        .swal2-title {
            font-size: 1.1rem !important;
        }
        .swal2-actions {
            flex-direction: column-reverse !important;
            gap: 8px !important;
            width: 100% !important;
            margin-top: 1rem !important;
        }
        .swal2-actions button {
            width: 100% !important;
            margin: 0 !important;
            height: 44px !important;
        }
    }

    .app{display:flex;min-height:100vh;flex-direction:row;}

    /* Sidebar */
    .sidebar{width:var(--sidebar-width);flex-shrink:0;background:var(--primary);color:#FFF;position:fixed;left:0;top:0;height:100vh;z-index:1001;display:flex;flex-direction:column;transition:transform .3s ease;transform:translateX(-100%);}
    .sidebar.show{transform:translateX(0);}
    .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
    .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
    .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
    .sidebar-menu li{margin-bottom:.2rem;}
    .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
    .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
    .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
    .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}
    .sidebar-foot{padding:1rem 1.5rem;font-size:11px;color:rgba(255,255,255,.4);border-top:1px solid rgba(255,255,255,.1);}

    /* Main */
    .main{flex:1;min-width:0;margin-left:0;padding:16px;padding-top:72px;width:auto;max-width:none;height:auto;overflow:visible;display:flex;flex-direction:column;}
    .main-scroll{overflow:visible;flex:none;width:100%;height:auto;min-height:0;}

    /* Sidebar Overlay */
    .sidebar-overlay.active{display:block !important;}

    /* Floating Hamburger Button */
    .hamburger-btn{display:none;position:fixed;top:12px;left:12px;z-index:1002;background:var(--primary);color:#fff;border:none;border-radius:12px;width:44px;height:44px;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.2);transition:background 0.2s;}
    .hamburger-btn:hover{background:var(--primary-hover);}

    /* Mobile Header (integrated hamburger + brand, mobile only) */
    .mobile-header{display:none;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;border-bottom-left-radius:24px;border-bottom-right-radius:24px;}
    .mobile-header-brand{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
    .mobile-logo{width:56px;height:56px;border-radius:50%;background:#FBC02D;padding:4px;flex-shrink:0;}
    .mobile-logo-img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
    .mobile-brand-text{flex:1;min-width:0;}
    .mobile-brand-title{font-size:18px;font-weight:700;color:#ffffff;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .mobile-brand-subtitle{font-size:12px;color:rgba(255,255,255,0.8);margin:2px 0 0 0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .mobile-menu-btn{display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#ffffff;cursor:pointer;padding:8px;flex-shrink:0;margin-right:24px;}
    .mobile-menu-icon{width:32px;height:32px;}

    /* ── Desktop (1200px+): fixed expanded sidebar ── */
    @media (min-width:1200px){
        .sidebar{transform:translateX(0) !important;z-index:1000 !important;}
        .sidebar.show{transform:translateX(0) !important;}
        .main,.main-content{margin-left:var(--sidebar-width) !important;width:calc(100% - var(--sidebar-width)) !important;max-width:none !important;padding:var(--content-padding) !important;padding-top:var(--content-padding) !important;height:auto !important;overflow:visible !important;flex:none !important;}
        .main-scroll{overflow:visible !important;flex:none !important;width:100% !important;}
        .hamburger-btn{display:none !important;}
        .mobile-header{display:none !important;}
    }

    /* ── Large laptop (1200–1399px): tighter main padding ── */
    @media (min-width:1200px) and (max-width:1399px){
        .main,.main-content{padding:24px !important;padding-top:24px !important;}
    }

    /* ── Tablet (768–1199px): sidebar collapses to icon-only ── */
    @media (min-width:768px) and (max-width:1199px){
        .sidebar{width:72px !important;transform:translateX(0) !important;z-index:1000 !important;}
        .sidebar.show{transform:translateX(0) !important;}
        .sidebar-brand{justify-content:center;padding:1.25rem 0 !important;}
        .sidebar-brand span{display:none !important;}
        .sidebar-menu{padding:0.75rem 0;}
        .sidebar-menu a{position:relative;justify-content:center;padding:0.95rem 0 !important;}
        .sidebar-menu a span{display:none;position:absolute;left:72px;top:50%;transform:translateY(-50%);background:var(--primary-dark);color:#fff;padding:0.4rem 0.65rem;border-radius:6px;font-size:12px;font-weight:600;white-space:nowrap;z-index:1002;box-shadow:0 4px 12px rgba(0,0,0,0.2);}
        .sidebar-menu a:hover span{display:block;}
        .sidebar-foot{display:none !important;}
        .sidebar-overlay{display:none !important;}
        .hamburger-btn{display:none !important;}
        .main,.main-content{margin-left:72px !important;width:calc(100% - 72px) !important;max-width:none !important;padding:16px !important;padding-top:16px !important;height:auto !important;overflow:visible !important;flex:none !important;}
        .main-scroll{overflow:visible !important;flex:none !important;width:100% !important;}
        .mobile-header{display:none !important;}
    }

    /* ── Mobile (<768px): off-canvas drawer + mobile header ── */
    @media (max-width:767px){
        body{overflow-x:hidden;}
        .app{flex-direction:column;}
        .main,.main-content{margin-left:0 !important;width:100% !important;max-width:none !important;padding:12px 14px !important;padding-top:90px !important;height:auto !important;overflow:visible !important;flex:none !important;}
        .main-scroll{overflow:visible !important;height:auto !important;min-height:auto !important;flex:none !important;}
        .hamburger-btn{display:none !important;}
        .mobile-header{display:flex !important;z-index:998 !important;}
    }

    /* ── Small mobile (<480px) ── */
    @media (max-width:479px){
        .main,.main-content{padding:10px !important;padding-top:88px !important;}
        .mobile-header{padding:0 12px !important;height:72px !important;}
        .mobile-logo{width:48px !important;height:48px !important;}
        .mobile-brand-title{font-size:16px !important;}
        .mobile-brand-subtitle{font-size:11px !important;}
        .mobile-menu-icon{width:28px !important;height:28px !important;}
    }
</style>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="users" style="width:24px;height:24px"></i>
        <span>Senior Citizen</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/senior" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/senior/registration" class="{{ ($active ?? '') === 'registration' ? 'active' : '' }}"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>Registration</span></a></li>
        <li><a href="/admin/senior/masterlist" class="{{ ($active ?? '') === 'masterlist' ? 'active' : '' }}"><i data-lucide="list" style="width:20px;height:20px"></i><span>Masterlist</span></a></li>
        <li><a href="/admin/senior/birthdays" class="{{ ($active ?? '') === 'birthdays' ? 'active' : '' }}"><i data-lucide="cake" style="width:20px;height:20px"></i><span>Birthday Beneficiaries</span></a></li>
        <li><a href="/admin/senior/payouts-history" class="{{ ($active ?? '') === 'payouts' ? 'active' : '' }}"><i data-lucide="history" style="width:20px;height:20px"></i><span>Payout History</span></a></li>
        <li><a href="/admin/senior/statistics" class="{{ ($active ?? '') === 'statistics' ? 'active' : '' }}"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i><span>Statistics</span></a></li>
        <li><a href="/admin/senior/archive" class="{{ ($active ?? '') === 'archive' ? 'active' : '' }}"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

<!-- Hidden form for secure POST logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
    @csrf
</form>

<!-- Mobile Header (visible only on mobile) -->
@php
    $logo = null;
    if (file_exists(public_path('images/mswdo-logo.png'))) {
        $logo = 'mswdo-logo.png';
    } else {
        $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
        if (!empty($files)) $logo = basename($files[0]);
    }
@endphp
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">{{ $mobileSubtitle ?? 'Senior Citizen' }}</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
                <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>



<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (!sidebar) return;
        if (sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('show');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    (function () {
        var overlay = document.getElementById('sidebarOverlay');
        if (overlay) {
            overlay.addEventListener('click', function () {
                var sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('show');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var sidebar = document.getElementById('sidebar');
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    var ov = document.getElementById('sidebarOverlay');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1200) {
                var sidebar = document.getElementById('sidebar');
                var ov = document.getElementById('sidebarOverlay');
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    })();
</script>
