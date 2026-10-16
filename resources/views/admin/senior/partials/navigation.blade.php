    <link rel="stylesheet" href="{{ asset('css/logout-modal.css') }}?v={{ filemtime(public_path('css/logout-modal.css')) }}">
<!-- Anti-flicker pre-render script for collapsed senior sidebar -->
<script>
    (function() {
        try {
            if (localStorage.getItem('senior_sidebar_collapsed') === 'true' && window.innerWidth >= 1200) {
                document.documentElement.classList.add('sidebar-collapsed');
                if (document.body) {
                    document.body.classList.add('sidebar-collapsed');
                }
            }
        } catch (e) {}
    })();
</script>

<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
<style>
    /* ══════════════════════════════════════════════════════════
       SENIOR CITIZEN MODULE — SHARED NAVIGATION / LAYOUT WRAPPER
       (Collapsible desktop sidebar + responsive tablet/mobile)
       ══════════════════════════════════════════════════════════ */

    :root {
        --sidebar-width: 260px;
        --sidebar-collapsed-width: 72px;
        --sidebar-bg: #b11116;
        --sidebar-dark: #8A0D11;
        --content-padding: 32px;
    }

    /* Uniform SweetAlert & Custom Modal Styles */
    .swal2-popup {
        border-radius: 16px !important;
        padding: 1.5rem !important;
        font-family: inherit !important;
        max-width: 95vw !important;
        box-sizing: border-box !important;
    }
    .swal2-title {
        color: #B11116 !important;
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
        background-color: #B11116 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 10px 24px !important;
    }
    .swal2-cancel {
        background-color: #6B7280 !important;
        color: #FFFFFF !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 10px 24px !important;
        transition: background-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }
    .swal2-cancel:hover,
    .swal2-cancel:focus {
        background-color: #4B5563 !important;
        color: #FFFFFF !important;
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

    .app {
        display: flex;
        min-height: 100vh;
        flex-direction: row;
    }

    /* Sidebar */
    .sidebar {
        width: var(--sidebar-width);
        flex-shrink: 0;
        background: var(--sidebar-bg, #b11116);
        color: #FFF;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1001;
        display: flex;
        flex-direction: column;
        transition: width .3s cubic-bezier(0.4, 0, 0.2, 1), transform .3s ease;
        transform: translateX(-100%);
        overflow: visible;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .sidebar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    .sidebar.show {
        transform: translateX(0);
    }
    .sidebar-brand {
        height: 72px;
        padding: 0 1rem 0 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, .15);
        color: #fff;
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        white-space: nowrap;
        transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar-brand-content {
        display: flex;
        align-items: center;
        gap: .65rem;
        overflow: hidden;
        min-width: 0;
        color: #FFFFFF;
        user-select: none;
        text-decoration: none;
    }
    .sidebar-brand-logo {
        width: 44px;
        height: 44px;
        object-fit: contain;
        flex-shrink: 0;
        transition: transform .2s ease, width .2s ease, height .2s ease;
    }
    .sidebar-brand-text,
    .sidebar-brand span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 1.05rem;
        letter-spacing: -0.01em;
        transition: opacity .2s ease;
    }
    .sidebar-brand i, .sidebar-brand [data-lucide], .sidebar-brand svg {
        width: 24px;
        height: 24px;
        color: #FFFFFF;
        flex-shrink: 0;
    }
    .sidebar-toggle-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 8px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .2);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all .2s ease;
        flex-shrink: 0;
    }
    .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, .24);
        border-color: rgba(255, 255, 255, .4);
        color: #ffffff;
        transform: scale(1.05);
    }
    .sidebar-toggle-btn .toggle-icon {
        color: #FFFFFF;
        transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1), color .2s ease;
    }
    .sidebar-toggle-btn:hover .toggle-icon {
        color: #FFFFFF;
    }
    .sidebar-menu {
        list-style: none;
        margin: 0;
        padding: 1rem 0;
        flex: 1;
        overflow: visible;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .sidebar-menu::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    .sidebar-menu li {
        margin-bottom: .25rem;
    }
    .sidebar-menu a {
        color: #FFFFFF !important;
        padding: .75rem 1.5rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        text-decoration: none;
        font-size: .95rem;
        font-weight: 600;
        border-left: none;
        transition: background-color .2s ease, color .2s ease, padding .2s ease;
        white-space: nowrap;
    }
    .sidebar-menu a span {
        color: #FFFFFF !important;
        font-weight: inherit;
    }
    .sidebar-menu a:hover {
        background: rgba(255, 255, 255, .14);
        color: #FFFFFF !important;
    }
    .sidebar-menu a i, .sidebar-menu a [data-lucide], .sidebar-menu a svg {
        width: 20px;
        height: 20px;
        min-width: 20px;
        min-height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #FFFFFF;
        flex-shrink: 0;
        transition: transform .2s cubic-bezier(0.4, 0, 0.2, 1), color .2s ease;
    }
    .sidebar-menu a:hover i, .sidebar-menu a:hover [data-lucide], .sidebar-menu a:hover svg {
        transform: scale(1.12);
        color: #FFFFFF;
    }
    .sidebar-menu a.active {
        background: rgba(0, 0, 0, 0.24);
        color: #FFFFFF !important;
        border-left: none !important;
        font-weight: 700;
    }
    .sidebar-menu a.active i, .sidebar-menu a.active [data-lucide], .sidebar-menu a.active svg {
        color: #FFFFFF !important;
        transform: scale(1.08);
    }
    .sidebar-foot {
        padding: 1rem 1.5rem;
        font-size: 11px;
        color: rgba(255, 255, 255, .6);
        border-top: 1px solid rgba(255, 255, 255, .15);
    }

    /* Main */
    .main, .main-content {
        flex: 1;
        min-width: 0;
        margin-left: 0;
        padding: 16px;
        padding-top: 72px;
        width: auto;
        max-width: none;
        height: auto;
        overflow: visible;
        display: flex;
        flex-direction: column;
        transition: margin-left .3s cubic-bezier(0.4, 0, 0.2, 1), width .3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .main-scroll {
        overflow: visible;
        flex: none;
        width: 100%;
        height: auto;
        min-height: 0;
    }

    /* Sidebar Overlay */
    .sidebar-overlay.active {
        display: block !important;
    }

    /* Floating Hamburger Button */
    .hamburger-btn {
        display: none;
        position: fixed;
        top: 12px;
        left: 12px;
        z-index: 1002;
        background: var(--sidebar-bg);
        color: #fff;
        border: none;
        border-radius: 12px;
        width: 44px;
        height: 44px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: background 0.2s;
    }
    .hamburger-btn:hover {
        background: var(--sidebar-dark, #8A0D11);
    }

    /* Mobile Header (integrated hamburger + brand, mobile only) */
    .mobile-header {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: var(--sidebar-bg, #b11116);
        color: #fff;
        padding: 0 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        align-items: center;
        justify-content: space-between;
        height: 80px;
    }
    .mobile-header-brand {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 0;
    }
    .mobile-logo {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #FBC02D;
        padding: 4px;
        flex-shrink: 0;
    }
    .mobile-logo-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .mobile-brand-text {
        flex: 1;
        min-width: 0;
    }
    .mobile-brand-title {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .mobile-brand-subtitle {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        margin: 2px 0 0 0;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .mobile-menu-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        color: #ffffff;
        cursor: pointer;
        padding: 8px;
        flex-shrink: 0;
        margin-right: 24px;
    }
    .mobile-menu-icon {
        width: 32px;
        height: 32px;
    }

    /* ── Desktop (1200px+): fixed expanded sidebar & collapsible states ── */
    @media (min-width: 1200px) {
        .sidebar {
            transform: translateX(0) !important;
            z-index: 1000 !important;
        }
        .sidebar.show {
            transform: translateX(0) !important;
        }
        .main, .main-content {
            margin-left: var(--sidebar-width) !important;
            width: calc(100% - var(--sidebar-width)) !important;
            max-width: none !important;
            padding: var(--content-padding) !important;
            padding-top: var(--content-padding) !important;
            height: auto !important;
            overflow: visible !important;
            flex: none !important;
        }
        .main-scroll {
            overflow: visible !important;
            flex: none !important;
            width: 100% !important;
        }
        .hamburger-btn {
            display: none !important;
        }
        .mobile-header {
            display: none !important;
        }

        /* Desktop Collapsed state */
        html.sidebar-collapsed .sidebar,
        body.sidebar-collapsed .sidebar,
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width, 72px) !important;
            overflow: visible !important;
        }
        html.sidebar-collapsed .sidebar-brand,
        body.sidebar-collapsed .sidebar-brand,
        .sidebar.collapsed .sidebar-brand {
            padding: 0.5rem 0 !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 0.35rem !important;
            height: 76px !important;
        }
        html.sidebar-collapsed .sidebar-brand-text,
        body.sidebar-collapsed .sidebar-brand-text,
        .sidebar.collapsed .sidebar-brand-text,
        html.sidebar-collapsed .sidebar-brand > span,
        body.sidebar-collapsed .sidebar-brand > span,
        .sidebar.collapsed .sidebar-brand > span {
            display: none !important;
        }
        html.sidebar-collapsed .sidebar-brand-logo,
        body.sidebar-collapsed .sidebar-brand-logo,
        .sidebar.collapsed .sidebar-brand-logo {
            width: 32px !important;
            height: 32px !important;
        }
        html.sidebar-collapsed .sidebar-toggle-btn,
        body.sidebar-collapsed .sidebar-toggle-btn,
        .sidebar.collapsed .sidebar-toggle-btn {
            width: 28px !important;
            height: 24px !important;
            min-width: 28px !important;
            border-radius: 6px !important;
        }
        html.sidebar-collapsed .sidebar-toggle-btn .toggle-icon,
        body.sidebar-collapsed .sidebar-toggle-btn .toggle-icon,
        .sidebar.collapsed .sidebar-toggle-btn .toggle-icon {
            transform: rotate(180deg);
        }
        html.sidebar-collapsed .sidebar-menu,
        body.sidebar-collapsed .sidebar-menu,
        .sidebar.collapsed .sidebar-menu {
            padding: 0.75rem 0;
            overflow: visible !important;
        }
        html.sidebar-collapsed .sidebar-menu a,
        body.sidebar-collapsed .sidebar-menu a,
        .sidebar.collapsed .sidebar-menu a {
            padding: 0.85rem 0 !important;
            justify-content: center !important;
            position: relative;
        }
        html.sidebar-collapsed .sidebar-menu a i,
        body.sidebar-collapsed .sidebar-menu a i,
        .sidebar.collapsed .sidebar-menu a i,
        html.sidebar-collapsed .sidebar-menu a svg,
        body.sidebar-collapsed .sidebar-menu a svg,
        .sidebar.collapsed .sidebar-menu a svg,
        html.sidebar-collapsed .sidebar-menu a [data-lucide],
        body.sidebar-collapsed .sidebar-menu a [data-lucide],
        .sidebar.collapsed .sidebar-menu a [data-lucide] {
            width: 22px !important;
            height: 22px !important;
            margin: 0 auto !important;
        }
        html.sidebar-collapsed .sidebar-menu a:hover i,
        body.sidebar-collapsed .sidebar-menu a:hover i,
        .sidebar.collapsed .sidebar-menu a:hover i,
        html.sidebar-collapsed .sidebar-menu a:hover svg,
        body.sidebar-collapsed .sidebar-menu a:hover svg,
        .sidebar.collapsed .sidebar-menu a:hover svg,
        html.sidebar-collapsed .sidebar-menu a:hover [data-lucide],
        body.sidebar-collapsed .sidebar-menu a:hover [data-lucide],
        .sidebar.collapsed .sidebar-menu a:hover [data-lucide] {
            transform: scale(1.15) !important;
            color: #FFFFFF !important;
        }
        html.sidebar-collapsed .sidebar-menu a.active,
        body.sidebar-collapsed .sidebar-menu a.active,
        .sidebar.collapsed .sidebar-menu a.active {
            border-left: none !important;
            background: rgba(0, 0, 0, 0.24) !important;
        }
        html.sidebar-collapsed .sidebar-menu a.active i,
        body.sidebar-collapsed .sidebar-menu a.active i,
        .sidebar.collapsed .sidebar-menu a.active i,
        html.sidebar-collapsed .sidebar-menu a.active svg,
        body.sidebar-collapsed .sidebar-menu a.active svg,
        .sidebar.collapsed .sidebar-menu a.active svg,
        html.sidebar-collapsed .sidebar-menu a.active [data-lucide],
        body.sidebar-collapsed .sidebar-menu a.active [data-lucide],
        .sidebar.collapsed .sidebar-menu a.active [data-lucide] {
            color: #FFFFFF !important;
        }
        html.sidebar-collapsed .sidebar-menu a span,
        body.sidebar-collapsed .sidebar-menu a span,
        .sidebar.collapsed .sidebar-menu a span {
            display: none;
            position: absolute;
            left: calc(var(--sidebar-collapsed-width, 72px) + 8px);
            top: 50%;
            transform: translateY(-50%);
            background: var(--sidebar-dark, #8A0D11);
            color: #FFFFFF;
            padding: 0.45rem 0.75rem;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            z-index: 1005;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            pointer-events: none;
        }
        html.sidebar-collapsed .sidebar-menu a:hover span,
        body.sidebar-collapsed .sidebar-menu a:hover span,
        .sidebar.collapsed .sidebar-menu a:hover span {
            display: block !important;
        }
        html.sidebar-collapsed .sidebar-menu a:hover span::before,
        body.sidebar-collapsed .sidebar-menu a:hover span::before,
        .sidebar.collapsed .sidebar-menu a:hover span::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            border-right: 4px solid var(--sidebar-dark, #8A0D11);
        }
        html.sidebar-collapsed .sidebar-foot,
        body.sidebar-collapsed .sidebar-foot,
        .sidebar.collapsed .sidebar-foot {
            display: none !important;
        }
        html.sidebar-collapsed .main,
        html.sidebar-collapsed .main-content,
        body.sidebar-collapsed .main,
        body.sidebar-collapsed .main-content,
        .main.expanded {
            margin-left: var(--sidebar-collapsed-width, 72px) !important;
            width: calc(100% - var(--sidebar-collapsed-width, 72px)) !important;
        }
    }

    /* ── Large laptop (1200–1399px): tighter main padding ── */
    @media (min-width: 1200px) and (max-width: 1399px) {
        .main, .main-content {
            padding: 24px !important;
            padding-top: 24px !important;
        }
    }

    /* ── Tablet (768–1199px): sidebar collapses to icon-only ── */
    @media (min-width: 768px) and (max-width: 1199px) {
        .sidebar {
            width: 72px !important;
            transform: translateX(0) !important;
            z-index: 1000 !important;
        }
        .sidebar.show {
            transform: translateX(0) !important;
        }
        .sidebar-brand {
            justify-content: center !important;
            padding: 0.5rem 0 !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 0.35rem !important;
            height: 76px !important;
        }
        .sidebar-brand span, .sidebar-brand-text {
            display: none !important;
        }
        .sidebar-brand-logo {
            width: 34px !important;
            height: 34px !important;
        }
        .sidebar-toggle-btn {
            display: none !important;
        }
        .sidebar-menu {
            padding: 0.75rem 0;
        }
        .sidebar-menu a {
            position: relative;
            justify-content: center;
            padding: 0.95rem 0 !important;
        }
        .sidebar-menu a i, .sidebar-menu a svg, .sidebar-menu a [data-lucide] {
            width: 22px !important;
            height: 22px !important;
            margin: 0 auto;
        }
        .sidebar-menu a:hover i, .sidebar-menu a:hover svg, .sidebar-menu a:hover [data-lucide] {
            transform: scale(1.15) !important;
            color: #FFFFFF !important;
        }
        .sidebar-menu a.active {
            border-left: none !important;
            background: rgba(0, 0, 0, 0.24) !important;
        }
        .sidebar-menu a.active i, .sidebar-menu a.active svg, .sidebar-menu a.active [data-lucide] {
            color: #FFFFFF !important;
        }
        .sidebar-menu a span {
            display: none;
            position: absolute;
            left: 72px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--sidebar-dark, #8A0D11);
            color: #fff;
            padding: 0.4rem 0.65rem;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            z-index: 1002;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            pointer-events: none;
        }
        .sidebar-menu a:hover span {
            display: block !important;
        }
        .sidebar-menu a:hover span::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            border-right: 4px solid var(--sidebar-dark, #8A0D11);
        }
        .sidebar-foot {
            display: none !important;
        }
        .sidebar-overlay {
            display: none !important;
        }
        .hamburger-btn {
            display: none !important;
        }
        .main, .main-content {
            margin-left: 72px !important;
            width: calc(100% - 72px) !important;
            max-width: none !important;
            padding: 16px !important;
            padding-top: 16px !important;
            height: auto !important;
            overflow: visible !important;
            flex: none !important;
        }
        .main-scroll {
            overflow: visible !important;
            flex: none !important;
            width: 100% !important;
        }
        .mobile-header {
            display: none !important;
        }
    }

    /* ── Mobile (<768px): off-canvas drawer + mobile header ── */
    @media (max-width: 767px) {
        body {
            overflow-x: hidden;
        }
        .app {
            flex-direction: column;
        }
        .sidebar-toggle-btn {
            display: none !important;
        }
        .main, .main-content {
            margin-left: 0 !important;
            width: 100% !important;
            max-width: none !important;
            padding: 12px 14px !important;
            padding-top: 90px !important;
            height: auto !important;
            overflow: visible !important;
            flex: none !important;
        }
        .main-scroll {
            overflow: visible !important;
            height: auto !important;
            min-height: auto !important;
            flex: none !important;
        }
        .hamburger-btn {
            display: none !important;
        }
        .mobile-header {
            display: flex !important;
            z-index: 998 !important;
        }
    }

    /* ── Small mobile (<480px) ── */
    @media (max-width: 479px) {
        .main, .main-content {
            padding: 10px !important;
            padding-top: 88px !important;
        }
        .mobile-header {
            padding: 0 12px !important;
            height: 72px !important;
        }
        .mobile-logo {
            width: 48px !important;
            height: 48px !important;
        }
        .mobile-brand-title {
            font-size: 16px !important;
        }
        .mobile-brand-subtitle {
            font-size: 11px !important;
        }
        .mobile-menu-icon {
            width: 28px !important;
            height: 28px !important;
        }
    }

    /* ── Mobile menu as dropdown below the top bar (welcome-page style) ── */
    @media (max-width: 767px) {
        .sidebar {
            top: 80px !important;
            left: 0 !important;
            right: 0 !important;
            bottom: auto !important;
            width: 100% !important;
            max-width: none !important;
            height: auto !important;
            max-height: calc(100vh - 80px) !important;
            overflow-y: auto !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            z-index: 997 !important;
            transform: translateY(-8px) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            border-radius: 0 0 14px 14px !important;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22) !important;
        }
        .sidebar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .sidebar.show {
            transform: translateY(0) !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .sidebar-brand { display: none !important; }
        .sidebar-menu { padding: .5rem !important; }
        .sidebar-menu a {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        .sidebar-overlay, .sidebar-overlay.active { display: none !important; pointer-events: none !important; }
    }

    /* Small phones: header is 72px tall, shift dropdown to match */
    @media (max-width: 479px) {
        .sidebar {
            top: 72px !important;
            max-height: calc(100vh - 72px) !important;
        }
    }

    @media print {
        .sidebar, .mobile-header, .sidebar-overlay { display: none !important; }
    }
</style>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-content" onclick="handleBrandClick()" title="Senior Citizen">
            <img src="{{ asset('images/IserveIcon.png') }}" class="sidebar-brand-logo" alt="iSERVE">
            <span class="sidebar-brand-text">Senior Citizen</span>
        </div>
        <button type="button" class="sidebar-toggle-btn" id="sidebarCollapseBtn" onclick="toggleSidebarCollapse()" aria-label="Collapse sidebar" title="Collapse sidebar">
            <svg class="toggle-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/senior" class="{{ ($active ?? '') === 'dashboard' ? 'active' : '' }}" title="Dashboard"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/senior/registration" class="{{ ($active ?? '') === 'registration' ? 'active' : '' }}" title="Registration"><i data-lucide="users"></i><span>Registration</span></a></li>
        <li><a href="/admin/senior/masterlist" class="{{ ($active ?? '') === 'masterlist' ? 'active' : '' }}" title="Masterlist"><i data-lucide="list"></i><span>Masterlist</span></a></li>
        <li><a href="/admin/senior/birthdays" class="{{ ($active ?? '') === 'birthdays' ? 'active' : '' }}" title="Birthday Beneficiaries"><i data-lucide="cake"></i><span>Birthday Beneficiaries</span></a></li>
        <li><a href="/admin/senior/in-between/eligibility-list" class="{{ in_array(($active ?? ''), ['in-between', 'in-between-eligibility', 'in-between-history']) ? 'active' : '' }}" title="In-Between Benefits"><i data-lucide="gift"></i><span>In-Between Benefits</span></a></li>
        <li><a href="/admin/senior/payouts-history" class="{{ ($active ?? '') === 'payouts' ? 'active' : '' }}" title="Payout History"><i data-lucide="history"></i><span>Payout History</span></a></li>
        <li><a href="/admin/senior/statistics" class="{{ ($active ?? '') === 'statistics' ? 'active' : '' }}" title="Statistics"><i data-lucide="bar-chart-3"></i><span>Statistics</span></a></li>
        <li><a href="/admin/senior/archive" class="{{ ($active ?? '') === 'archive' ? 'active' : '' }}" title="Archive"><i data-lucide="archive"></i><span>Archive</span></a></li>
        <li><a href="#" onclick="confirmLogout(event)" title="Logout"><i data-lucide="log-out"></i><span>Logout</span></a></li>
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
    $logo = 'IserveIcon.png';
    if (!file_exists(public_path('images/IserveIcon.png'))) {
        $logo = null;
        if (file_exists(public_path('images/mswdo-logo.png'))) {
            $logo = 'mswdo-logo.png';
        } else {
            $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
            if (!empty($files)) $logo = basename($files[0]);
        }
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
            <h1 class="mobile-brand-title">iSERVE SILANG</h1>
            <p class="mobile-brand-subtitle">{{ $mobileSubtitle ?? 'Senior Citizen' }}</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
                <img src="{{ asset('images/IserveIcon.png') }}" class="mobile-logo-img">
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
        updateMobileMenuIcon();
    }

    function toggleSidebarCollapse(forceState = null) {
        var sidebar = document.getElementById('sidebar');
        var collapseBtn = document.getElementById('sidebarCollapseBtn');
        var isCurrentlyCollapsed = document.documentElement.classList.contains('sidebar-collapsed') ||
            document.body.classList.contains('sidebar-collapsed') ||
            (sidebar && sidebar.classList.contains('collapsed'));

        var willCollapse = forceState !== null ? forceState : !isCurrentlyCollapsed;

        if (willCollapse) {
            document.documentElement.classList.add('sidebar-collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (sidebar) sidebar.classList.add('collapsed');
            if (collapseBtn) {
                collapseBtn.setAttribute('title', 'Expand sidebar');
                collapseBtn.setAttribute('aria-label', 'Expand sidebar');
            }
            try {
                localStorage.setItem('senior_sidebar_collapsed', 'true');
            } catch (e) {}
        } else {
            document.documentElement.classList.remove('sidebar-collapsed');
            document.body.classList.remove('sidebar-collapsed');
            if (sidebar) sidebar.classList.remove('collapsed');
            if (collapseBtn) {
                collapseBtn.setAttribute('title', 'Collapse sidebar');
                collapseBtn.setAttribute('aria-label', 'Collapse sidebar');
            }
            try {
                localStorage.setItem('senior_sidebar_collapsed', 'false');
            } catch (e) {}
        }

        // Trigger window resize so responsive charts, DataTables, and grids recalculate
        window.dispatchEvent(new Event('resize'));
    }

    function handleBrandClick() {
        var isCollapsed = document.documentElement.classList.contains('sidebar-collapsed') ||
            document.body.classList.contains('sidebar-collapsed') ||
            (document.getElementById('sidebar') && document.getElementById('sidebar').classList.contains('collapsed'));
        if (isCollapsed) {
            toggleSidebarCollapse(false);
        }
    }

    function updateMobileMenuIcon() {
        var sidebar = document.getElementById('sidebar');
        var btn = document.getElementById('mobileMenuBtn');
        if (!sidebar || !btn) return;
        var path = btn.querySelector('path');
        if (!path) return;
        var open = sidebar.classList.contains('show');
        path.setAttribute('d', open ? 'M6 18L18 6M6 6l12 12' : 'M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5');
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Logout',
        html: '<svg class="logout-modal-symbol" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M12 5l7 7-7 7v-4H7V9h5V5Z" fill="currentColor"/></svg><p class="logout-modal-message">Are you sure you want to log out?</p>',
        showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#B11116',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, log me out',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg app-logout-modal' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    (function () {
        function syncCollapseState() {
            try {
                if (localStorage.getItem('senior_sidebar_collapsed') === 'true' && window.innerWidth >= 1200) {
                    document.documentElement.classList.add('sidebar-collapsed');
                    if (document.body) document.body.classList.add('sidebar-collapsed');
                    var sidebar = document.getElementById('sidebar');
                    if (sidebar) sidebar.classList.add('collapsed');
                    var collapseBtn = document.getElementById('sidebarCollapseBtn');
                    if (collapseBtn) {
                        collapseBtn.setAttribute('title', 'Expand sidebar');
                        collapseBtn.setAttribute('aria-label', 'Expand sidebar');
                    }
                }
            } catch (e) {}
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncCollapseState);
        } else {
            syncCollapseState();
        }

        var overlay = document.getElementById('sidebarOverlay');
        if (overlay) {
            overlay.addEventListener('click', function () {
                var sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('show');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                updateMobileMenuIcon();
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
                    updateMobileMenuIcon();
                }
            }
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                if (window.innerWidth >= 1200) {
                    e.preventDefault();
                    toggleSidebarCollapse();
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
                    updateMobileMenuIcon();
                }
            }
        });
    })();
</script>
