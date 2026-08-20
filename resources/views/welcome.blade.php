<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MSWDO Silang</title>
    <meta name="description" content="Municipal Social Welfare and Development Office - Municipality of Silang">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800"
        rel="stylesheet" />

    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>
<body class="bg-[#F8FAFC] text-[#1F2937] antialiased">

<!-- ========================= -->
<!-- SIDEBAR (desktop) -->
<!-- ========================= -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="building-2" style="width:24px;height:24px"></i>
        <span>MSWDO Silang</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="#home"><i data-lucide="home" style="width:20px;height:20px"></i> Home</a></li>
        <li><a href="#services"><i data-lucide="briefcase" style="width:20px;height:20px"></i> Services</a></li>
        <li><a href="#about"><i data-lucide="info" style="width:20px;height:20px"></i> About</a></li>
        <li><a href="#contact"><i data-lucide="mail" style="width:20px;height:20px"></i> Contact</a></li>
    </ul>
    <div class="sidebar-foot">
        <a href="/admin" class="flex items-center gap-2 text-xs" style="color:rgba(255,255,255,.5);text-decoration:none">
            <i data-lucide="log-in" style="width:14px;height:14px"></i> Admin Login
        </a>
    </div>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

<!-- Hamburger Button (mobile) -->
<button id="hamburgerBtn" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
    <i data-lucide="menu" style="width:24px;height:24px"></i>
</button>

<!-- Mobile Header (visible only on mobile) -->
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">Municipal Social Welfare & Development Office</p>
        </div>
        <div class="mobile-logo">
            @php
            $logo = null;
            if(file_exists(public_path('images/mswdo-logo.png'))){
                $logo='mswdo-logo.png';
            }else{
                $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
                if(!empty($files))
                $logo=basename($files[0]);
            }
            @endphp
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
    <a href="/admin" class="mobile-login-btn">Login</a>
</div>

<!-- Desktop Navbar (visible only on laptop/desktop) -->
<nav class="desktop-navbar">
    <div class="navbar-brand">
        @php
        $logo = null;
        if(file_exists(public_path('images/mswdo-logo.png'))){
            $logo='mswdo-logo.png';
        }else{
            $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
            if(!empty($files))
            $logo=basename($files[0]);
        }
        @endphp
        @if($logo)
        <img src="{{ asset('images/'.$logo) }}" class="navbar-logo">
        @endif
        <span>MSWDO Silang</span>
    </div>
    <ul class="navbar-menu">
        <li><a href="#home">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="/admin" class="navbar-login-btn">Login</a></li>
    </ul>
</nav>

<!-- ========================= -->

<!-- HERO -->

<!-- ========================= -->

<section id="home"

class="relative overflow-hidden bg-gradient-to-r from-[#1A237E] via-[#1A237E] to-[#1A237E] pb-24 pt-24">

<div class="absolute inset-0 opacity-10">

<div class="absolute h-96 w-96 rounded-full bg-warm-gold blur-3xl -top-20 -left-20"></div>

<div class="absolute h-96 w-96 rounded-full bg-offwhite blur-3xl bottom-0 right-0"></div>

</div>

<div class="relative max-w-7xl mx-auto px-6">

<div class="grid lg:grid-cols-2 gap-14 items-center">

<div>

<p class="uppercase tracking-[5px] text-warm-gold">

WELCOME TO

</p>

<h1 class="mt-4 text-6xl font-extrabold text-white leading-tight">

MSWDO

<span class="text-warm-gold">

SILANG

</span>

</h1>

<p class="mt-6 text-offwhite text-lg leading-8">

The Municipal Social Welfare and Development Office is committed to

empowering vulnerable sectors through quality social protection,

community development, and accessible public services.

</p>

<div class="mt-10 flex flex-wrap gap-4">

<a href="#services"

class="bg-warm-gold px-8 py-4 rounded-xl font-bold hover:scale-105 transition">

Explore Services

</a>

<a href="#contact"

class="border border-offwhite text-offwhite px-8 py-4 rounded-xl hover:bg-offwhite hover:text-primary transition">

Contact Us

</a>

</div>

<div class="grid grid-cols-3 gap-8 mt-14">

<div>

<h2 class="text-4xl font-bold text-warm-gold">

10K+

</h2>

<p class="text-offwhite">

Families Assisted

</p>

</div>

<div>

<h2 class="text-4xl font-bold text-warm-gold">

25+

</h2>

<p class="text-offwhite">

Programs

</p>

</div>

<div>

<h2 class="text-4xl font-bold text-warm-gold">

24/7

</h2>

<p class="text-offwhite">

Online Support

</p>

</div>

</div>

</div>

<div class="flex justify-center">

<div class="bg-offwhite bg-opacity-70 backdrop-blur-lg rounded-3xl p-10 shadow-2xl">

@if($logo)

<img src="{{ asset('images/'.$logo) }}"

class="w-80">

@else

<div class="w-80 h-80 rounded-full bg-card-white"></div>

@endif

</div>

</div>

</div>

</div>

</section>

<!-- ========================= -->

<!-- QUICK SERVICES -->

<!-- ========================= -->

<section id="services" class="py-20 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="text-center">

<h2 class="text-4xl font-bold">

Our Services

</h2>

<p class="text-secondary mt-3">

Providing quality welfare services for every Silangueño.

</p>

</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-14 justify-items-center mx-auto max-w-4xl">

<div class="bg-card-white rounded-2xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center">

<div class="text-5xl mb-4"> </div>

<h3 class="font-bold text-xl">

Financial Assistance

</h3>

<p class="text-secondary mt-3">

Emergency and medical financial support for qualified residents.

</p>

</div>



<div class="bg-card-white rounded-2xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center">

<div class="text-5xl mb-4"></div>

<h3 class="font-bold text-xl">

Senior Citizens

</h3>

<p class="text-secondary mt-3">

Programs and benefits dedicated to senior citizens.

</p>

</div>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- ABOUT MSWDO -->
<!-- ===================================== -->

<section id="about" class="py-24 bg-offwhite">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div>

                <span class="text-primary font-semibold uppercase tracking-widest">
                    About Us
                </span>

                <h2 class="text-5xl font-bold mt-4">
                    Serving the People of
                    <span class="text-primary">Silang</span>
                </h2>
                <p class="text-secondary mt-4">
                    Simple steps to request assistance.
                </p>
            </div>
            <div class="grid md:grid-cols-5 gap-8 mt-16">
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">
                        1
                    </div>
                    <h3 class="font-bold mt-6">
                        Submit Request
                    </h3>
                </div>
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">
                        2
                    </div>
                    <h3 class="font-bold mt-6">
                        Document Review
                    </h3>
                </div>
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">
                        3
                    </div>
                    <h3 class="font-bold mt-6">
                        Assessment
                    </h3>
                </div>
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">
                        4
                    </div>
                    <h3 class="font-bold mt-6">
                        Approval
                    </h3>
                </div>
                <div class="text-center">
                    <div
                        class="w-20 h-20 mx-auto rounded-full bg-accent text-white flex items-center justify-center text-3xl">
                        5
                    </div>
                    <h3 class="font-bold mt-6">
                        Release Assistance
                    </h3>
                </div>
            </div>
        </div>

    </div>

</section>

<!-- ===================================== -->
<!-- MISSION & VISION -->
<!-- ===================================== -->

<section class="py-24 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="grid lg:grid-cols-2 gap-10">

<div class="bg-primary text-white rounded-3xl p-12 shadow-xl">

<h2 class="text-4xl font-bold mb-8">

Mission

</h2>

<p class="text-lg leading-8">

To provide efficient, compassionate, and accessible social welfare
services that improve the quality of life of every Silangueño through
people-centered programs, community participation, and sustainable
development.

</p>

</div>

<div class="bg-warm-gold rounded-3xl p-12 shadow-xl">

<h2 class="text-4xl font-bold text-slate-900 mb-8">

Vision

</h2>

<p class="text-lg leading-8 text-slate-800">

A resilient, inclusive, and empowered municipality where every citizen
has equal access to opportunities, protection, and quality social
services.

</p>

</div>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- PROCESS FLOW -->
<!-- ===================================== -->

<section class="py-24 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="text-center">

<h2 class="text-4xl font-bold">

How to Apply

</h2>

<p class="text-secondary mt-4">

Simple steps to request assistance.

</p>

</div>

<div class="grid md:grid-cols-5 gap-8 mt-16">

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

1

</div>

<h3 class="font-bold mt-6">

Submit Request

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

2

</div>

<h3 class="font-bold mt-6">

Document Review

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

3

</div>

<h3 class="font-bold mt-6">

Assessment

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

4

</div>

<h3 class="font-bold mt-6">

Approval

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-accent text-white flex items-center justify-center text-3xl">

5

</div>

<h3 class="font-bold mt-6">

Release Assistance

</h3>

</div>

</div>

</div>

</section>



<!-- ===================================== -->
<!-- CALL TO ACTION -->
<!-- ===================================== -->

<section class="py-20 bg-primary">

<div class="max-w-5xl mx-auto text-center px-6">

<h2 class="text-5xl font-bold text-white">

Need Social Assistance?

</h2>

<p class="text-offwhite mt-6 text-xl">

Our dedicated team is ready to assist you with your concerns.

</p>

<div class="mt-10 flex flex-wrap justify-center gap-6">

<a href="#contact"

class="bg-warm-gold px-8 py-4 rounded-xl font-bold hover:scale-105 transition">

Contact MSWDO

</a>

<a href="#services"

class="border border-offwhite px-8 py-4 rounded-xl text-white hover:bg-offwhite hover:text-primary transition">

View Services

</a>

<button onclick="openServiceRequestModal()" class="online-request-btn">

<i data-lucide="file-text" style="width:20px;height:20px;margin-right:8px;"></i>

Online Service Request

</button>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- CONTACT SECTION -->
<!-- ===================================== -->
<style>
/* ── Sidebar + Mobile Header ── */
.sidebar{width:var(--sidebar-width,260px);flex-shrink:0;background:#1A237E;color:#FFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
.sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
.sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:#FBC02D;}
.sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
.sidebar-menu li{margin-bottom:.2rem;}
.sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
.sidebar-menu a:hover{background:rgba(255,255,255,.1);color:#FBC02D;}
.sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}
.sidebar-foot{padding:1rem 1.5rem;font-size:11px;color:rgba(255,255,255,.4);border-top:1px solid rgba(255,255,255,.1);}
.sidebar-overlay.active{display:block!important;}

.hamburger-btn{display:none;position:fixed;top:12px;left:12px;z-index:1002;background:#1A237E;color:#fff;border:none;outline:none;border-radius:12px;width:44px;height:44px;align-items:center;justify-content:center;cursor:pointer;}
.hamburger-btn:focus{outline:none;box-shadow:0 0 0 3px rgba(26,35,126,0.4);}

.mobile-header{display:none!important;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;}
.mobile-header-brand{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
.mobile-logo{width:56px;height:56px;border-radius:50%;background:#FBC02D;padding:4px;flex-shrink:0;}
.mobile-logo-img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.mobile-brand-text{flex:1;min-width:0;}
.mobile-brand-title{font-size:18px;font-weight:700;color:#ffffff;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mobile-brand-subtitle{font-size:12px;color:rgba(255,255,255,.8);margin:2px 0 0 0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mobile-menu-btn{display:flex;align-items:center;justify-content:center;background:transparent;border:none;outline:none;color:#ffffff;cursor:pointer;padding:8px;flex-shrink:0;margin-right:24px;}
.mobile-menu-btn:focus{outline:none;}
.mobile-menu-icon{width:32px;height:32px;}
.mobile-login-btn{background:transparent;color:#fff;padding:0.5rem 1rem;border-radius:6px;font-weight:600;font-size:0.875rem;text-decoration:none;flex-shrink:0;}

.hamburger-btn{display:flex;}
.sidebar{transform:translateX(-100%)!important;z-index:1001!important;}
.sidebar.show{transform:translateX(0)!important;}
body{padding-left:0!important;padding-top:80px!important;}
.mobile-header{display:flex!important;z-index:998!important;}
.desktop-navbar{display:none!important;}

@media(min-width:1200px){
    body{padding-left:0;padding-top:0;}
    .hamburger-btn{display:none!important;}
    .mobile-header{display:none!important;}
    .sidebar{display:none!important;}
    .desktop-navbar{display:flex!important;}
}

/* Desktop Navbar Styles */
.desktop-navbar{display:none;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 3rem;align-items:center;justify-content:space-between;height:80px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.navbar-brand{display:flex;align-items:center;gap:1rem;color:#fff;font-weight:700;font-size:1.2rem;letter-spacing:0.5px;}
.navbar-logo{width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #FBC02D;}
.navbar-menu{list-style:none;margin:0;padding:0;display:flex;align-items:center;gap:2.5rem;}
.navbar-menu li{margin:0;}
.navbar-menu a{color:#fff;text-decoration:none;font-size:0.95rem;font-weight:500;padding:0.5rem 0;transition:color .2s ease,opacity .2s ease;opacity:0.9;}
.navbar-menu a:hover{color:#FBC02D;opacity:1;}
.navbar-login-btn{background:transparent;color:#fff;padding:0.6rem 1.5rem;border-radius:8px;font-weight:600;}

.contact-section {
    padding: 7rem 1.5rem;
    background-color: #F8FAFC; /* Match site off-white */
    border-top: 1px solid #E2E8F0;
}
.contact-container {
    max-width: 76rem;
    margin: 0 auto;
}
.contact-header {
    text-align: center;
    margin-bottom: 5rem;
}
.contact-subtitle {
    color: #B45309; /* Deep amber gold */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.75rem;
    display: block;
    margin-bottom: 0.75rem;
}
.contact-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1A237E; /* Brand primary blue */
    margin-bottom: 1.25rem;
    letter-spacing: -0.03em;
}
.contact-description {
    color: #475569; /* Slate secondary */
    font-size: 1.0625rem;
    max-width: 36rem;
    margin: 0 auto;
    line-height: 1.7;
}
.online-request-btn {
    display: inline-flex;
    align-items: center;
    background: #FBC02D;
    color: #1A237E;
    padding: 1rem 2rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}
.online-request-btn:hover {
    background: #FFD54F;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(251, 192, 45, 0.3);
}

/* Service Request Modal Styles */
.service-request-modal .swal2-popup {
    border-radius: 16px;
    padding: 2rem;
}
.service-request-modal .swal2-title {
    color: #1A237E;
    font-size: 24px;
    font-weight: 700;
}
.service-request-modal input,
.service-request-modal select {
    font-family: inherit;
}
.contact-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}
@media (min-width: 1024px) {
    .contact-grid {
        grid-template-columns: 1fr 1.2fr;
    }
}
.contact-card {
    background: #FFFFFF;
    border-radius: 0.75rem;
    border: 1px solid #E2E8F0;
    padding: 3rem;
    display: flex;
    flex-direction: column;
}
.card-title {
    font-size: 1.375rem;
    font-weight: 800;
    color: #1A237E; /* Brand primary blue */
    margin-bottom: 2.5rem;
    letter-spacing: -0.01em;
}
.info-list {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
    flex: 1;
}
.info-item {
    display: flex;
    align-items: start;
    gap: 1.25rem;
}
.info-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: #1A237E; /* Brand primary blue */
    margin-top: 0.125rem;
    flex-shrink: 0;
}
.info-content {
    flex: 1;
}
.info-label {
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #B45309; /* Amber gold */
    margin-bottom: 0.375rem;
}
.info-text {
    color: #1F2937;
    font-size: 1.0625rem;
    line-height: 1.5;
}
.contact-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    flex: 1;
}
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 640px) {
    .form-row-2 {
        grid-template-columns: 1fr 1fr;
    }
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.form-field-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
}
.form-input {
    width: 100%;
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
.form-textarea {
    resize: none;
    min-height: 8rem;
}
.submit-button {
    background-color: #1A237E; /* Brand primary blue */
    color: #FFFFFF;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: background-color 0.2s ease;
    margin-top: 1rem;
}
.submit-button:hover {
    background-color: #111827;
}
.submit-button:active {
    transform: translateY(1px);
}
.button-icon {
    width: 1.1rem;
    height: 1.1rem;
}

/* Emergency Card - High Contrast Brand Navy & Gold */
.emergency-card {
    background-color: #1A237E; /* Brand primary blue */
    border-radius: 0.75rem;
    padding: 3rem;
    color: #FFFFFF;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.emergency-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    padding-bottom: 1.5rem;
}
@media (min-width: 640px) {
    .emergency-header {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}
.emergency-title-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.emergency-badge {
    border: 1px solid #FBC02D; /* Brand warm gold */
    color: #FBC02D;
    font-weight: 700;
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    align-self: start;
}
.emergency-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0;
    letter-spacing: -0.02em;
}
.emergency-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem 3rem;
}
@media (min-width: 768px) {
    .emergency-grid {
        grid-template-columns: 1fr 1fr;
    }
}
@media (min-width: 1200px) {
    .emergency-grid {
        grid-template-columns: 1fr 1fr 1fr;
    }
}
.hotline-item {
    font-size: 0.95rem;
    line-height: 1.6;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.hotline-label {
    color: #E2E8F0;
    font-weight: 500;
}
.hotline-value {
    color: #FBC02D; /* Brand warm gold */
    font-weight: 700;
    font-family: monospace;
}
@media (max-width: 479px) {
    .contact-card {
        padding: 2rem;
    }
    .emergency-card {
        padding: 2rem;
    }
    .hotline-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>

    <!-- ===================================== -->
    <!-- CONTACT SECTION -->
    <!-- ===================================== -->
    <style>
        .contact-section {
            padding: 7rem 1.5rem;
            background-color: #F8FAFC;
            /* Match site off-white */
            border-top: 1px solid #E2E8F0;
        }
        .contact-container {
            max-width: 76rem;
            margin: 0 auto;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 5rem;
        }
        .contact-subtitle {
            color: #B45309;
            /* Deep amber gold */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-size: 0.75rem;
            display: block;
            margin-bottom: 0.75rem;
        }
        .contact-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1A237E;
            /* Brand primary blue */
            margin-bottom: 1.25rem;
            letter-spacing: -0.03em;
        }
        .contact-description {
            color: #475569;
            /* Slate secondary */
            font-size: 1.0625rem;
            max-width: 36rem;
            margin: 0 auto;
            line-height: 1.7;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        @media (min-width: 1024px) {
            .contact-grid {
                grid-template-columns: 1fr 1.2fr;
            }
        }
        .contact-card {
            background: #FFFFFF;
            border-radius: 0.75rem;
            border: 1px solid #E2E8F0;
            padding: 3rem;
            display: flex;
            flex-direction: column;
        }
        .card-title {
            font-size: 1.375rem;
            font-weight: 800;
            color: #1A237E;
            /* Brand primary blue */
            margin-bottom: 2.5rem;
            letter-spacing: -0.01em;
        }
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            flex: 1;
        }
        .info-item {
            display: flex;
            align-items: start;
            gap: 1.25rem;
        }
        .info-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #1A237E;
            /* Brand primary blue */
            margin-top: 0.125rem;
            flex-shrink: 0;
        }
        .info-content {
            flex: 1;
        }
        .info-label {
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #B45309;
            /* Amber gold */
            margin-bottom: 0.375rem;
        }
        .info-text {
            color: #1F2937;
            font-size: 1.0625rem;
            line-height: 1.5;
        }
        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            flex: 1;
        }
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 640px) {
            .form-row-2 {
                grid-template-columns: 1fr 1fr;
            }
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-field-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
        }
        .form-input {
            width: 100%;
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
        .form-textarea {
            resize: none;
            min-height: 8rem;
        }
        .submit-button {
            background-color: #1A237E;
            /* Brand primary blue */
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: background-color 0.2s ease;
            margin-top: 1rem;
        }
        .submit-button:hover {
            background-color: #111827;
        }
        .submit-button:active {
            transform: translateY(1px);
        }
        .button-icon {
            width: 1.1rem;
            height: 1.1rem;
        }
        /* Emergency Card - High Contrast Brand Navy & Gold */
        .emergency-card {
            background-color: #1A237E;
            /* Brand primary blue */
            border-radius: 0.75rem;
            padding: 3rem;
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .emergency-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            padding-bottom: 1.5rem;
        }
        @media (min-width: 640px) {
            .emergency-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .emergency-title-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .emergency-badge {
            border: 1px solid #FBC02D;
            /* Brand warm gold */
            color: #FBC02D;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            align-self: start;
        }
        .emergency-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #FFFFFF;
            margin: 0;
            letter-spacing: -0.02em;
        }
        .emergency-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem 3rem;
        }
        @media (min-width: 768px) {
            .emergency-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (min-width: 1200px) {
            .emergency-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }
        .hotline-item {
            font-size: 0.95rem;
            line-height: 1.6;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .hotline-label {
            color: #E2E8F0;
            font-weight: 500;
        }
        .hotline-value {
            color: #FBC02D;
            /* Brand warm gold */
            font-weight: 700;
            font-family: monospace;
        }
    </style>
    <section id="contact" class="contact-section">
        <div class="contact-container">
            <div class="contact-header">
                <span class="contact-subtitle">Get In Touch</span>
                <h2 class="contact-title">Contact Us</h2>
                <p class="contact-description">
                    Reach out to the Municipal Social Welfare and Development Office. We're here to help and answer any
                    questions you may have.
                </p>
            </div>
            <div class="contact-grid">
                <!-- Office Info Card -->
                <div class="contact-card">
                    <h3 class="card-title">Office Information</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.75" stroke="currentColor" class="info-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <div class="info-content">
                                <h4 class="info-label">Address</h4>
                                <p class="info-text">Municipal Hall, Silang, Cavite</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.75" stroke="currentColor" class="info-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.188-4.166-7-7l1.3-1.3c.362-.272.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.11-1.008H5.036a2.25 2.25 0 0 0-2.25 2.25v1.356Z" />
                            </svg>
                            <div class="info-content">
                                <h4 class="info-label">Phone</h4>
                                <p class="info-text">(046) XXX-XXXX</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.75" stroke="currentColor" class="info-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <div class="info-content">
                                <h4 class="info-label">Email</h4>
                                <p class="info-text">socialwelfaresilang@gmail.com</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.75" stroke="currentColor" class="info-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <div class="info-content">
                                <h4 class="info-label">Office Hours</h4>
                                <p class="info-text">Monday - Friday<br>8:00 AM – 5:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Contact Form Card -->
                <div class="contact-card">
                    <h3 class="card-title">Send us a Message</h3>
                    <form class="contact-form">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="fullname" class="form-field-label">Full Name</label>
                                <input type="text" id="fullname" name="name" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-field-label">Email Address</label>
                                <input type="email" id="email" name="email" required class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject" class="form-field-label">Subject</label>
                            <input type="text" id="subject" name="subject" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="message" class="form-field-label">Your Message</label>
                            <textarea id="message" name="message" rows="5" required class="form-input form-textarea"></textarea>
                        </div>
                        <button type="submit" class="submit-button">
                            <span>Send Message</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="button-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            <!-- Emergency Hotlines -->
            <div class="emergency-card">
                <div class="emergency-header">
                    <div class="emergency-title-wrapper">
                        <h3 class="emergency-title">Emergency Hotlines</h3>
                    </div>
                    <span class="emergency-badge">Emergency 24/7</span>
                </div>
                <div class="emergency-grid">
                    <div class="hotline-item"><span class="hotline-label">Silang Municipal Office:</span> <span
                            class="hotline-value">(046) 414-0202</span></div>
                    <div class="hotline-item"><span class="hotline-label">PDRRMO (Silang):</span> <span
                            class="hotline-value">(046) 424-0203</span></div>
                    <div class="hotline-item"><span class="hotline-label">PNP WCPD – Silang:</span> <span
                            class="hotline-value">0998-397-0222</span></div>
                    <div class="hotline-item"><span class="hotline-label">Silang PNP Mobile:</span> <span
                            class="hotline-value">0998-598-5622</span></div>
                    <div class="hotline-item"><span class="hotline-label">DSWD AICS:</span> <span
                            class="hotline-value">8962-2813</span></div>
                    <div class="hotline-item"><span class="hotline-label">DSWD Central Office:</span> <span
                            class="hotline-value">8-931-8101</span></div>
                    <div class="hotline-item"><span class="hotline-label">DSWD Mobile:</span> <span
                            class="hotline-value">0919-911-6200</span></div>
                    <div class="hotline-item"><span class="hotline-label">Makabata Helpline:</span> <span
                            class="hotline-value">1383</span></div>
                    <div class="hotline-item"><span class="hotline-label">Bantay Bata Hotline:</span> <span
                            class="hotline-value">163</span></div>
                    <div class="hotline-item"><span class="hotline-label">Emergency (All):</span> <span
                            class="hotline-value">911</span></div>
                    <div class="hotline-item"><span class="hotline-label">NCMH Mental Health:</span> <span
                            class="hotline-value">1553</span></div>
                    <div class="hotline-item"><span class="hotline-label">Complaints Hotline:</span> <span
                            class="hotline-value">8888</span></div>
                    <div class="hotline-item"><span class="hotline-label">Anti-Trafficking Line:</span> <span
                            class="hotline-value">1343</span></div>
                    <div class="hotline-item"><span class="hotline-label">PNP Women's Desk:</span> <span
                            class="hotline-value">117</span></div>
                    <div class="hotline-item"><span class="hotline-label">Medical Assistance:</span> <span
                            class="hotline-value">1555</span></div>
                    <div class="hotline-item"><span class="hotline-label">DOH Hotline:</span> <span
                            class="hotline-value">894-COVID</span></div>
                    <div class="hotline-item"><span class="hotline-label">DSWD Help:</span> <span
                            class="hotline-value">0932-933-3251</span></div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===================================== -->
    <!-- GOOGLE MAP -->
    <!-- ===================================== -->
    <section>
        <iframe src="https://www.google.com/maps?q=Silang%20Municipal%20Hall&t=&z=15&ie=UTF8&iwloc=&output=embed"
            class="w-full h-[450px]" loading="lazy">
        </iframe>
    </section>
    <!-- ===================================== -->
    <!-- FOOTER -->
    <!-- ===================================== -->
    <footer class="bg-primary text-white">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid gap-10 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <h2 class="text-3xl font-bold">
                        MSWDO Silang
                    </h2>
                    <p class="mt-5 text-offwhite leading-8">
                        Municipal Social Welfare and Development Office committed to
                        providing quality, transparent, and compassionate public
                        service.
                    </p>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-5">
                        Quick Links
                    </h3>
                    <ul class="space-y-3 text-offwhite">
                        <li><a href="#home" class="hover:text-warm-gold transition">Home</a></li>
                        <li><a href="#services" class="hover:text-warm-gold transition">Services</a></li>
                        <li><a href="#about" class="hover:text-warm-gold transition">About</a></li>
                        <li><a href="#contact" class="hover:text-warm-gold transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-5">
                        Programs
                    </h3>
                    <ul class="space-y-3 text-offwhite">
                        <li>Financial Assistance</li>
                        <li>VAWC</li>
                        <li>BCPC</li>
                        <li>Senior Citizens</li>
                        <!-- <li>Solo Parent</li> -->
                        <li>Social Case Study</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-5">
                        Office Hours
                    </h3>
                    <p class="text-offwhite leading-7">
                        Monday - Friday
                        <br><br>
                        8:00 AM - 5:00 PM
                    </p>
                </div>
            </div>
            <hr class="border-primary border-opacity-70 my-10">
            <div class="flex justify-center items-center text-sm">
                <p class="text-offwhite text-center">
                    © {{ date('Y') }} MSWDO Silang. All Rights Reserved.
                </p>
            </div>
        </div>

        <hr class="border-primary border-opacity-70 my-10">

        <div class="flex justify-center items-center text-sm">

            <p class="text-offwhite text-center">
                © {{ date('Y') }} MSWDO Silang. All Rights Reserved.
            </p>

        </div>

    </div>

</footer>

<!-- ===================================== -->
<!-- SCROLL TO TOP -->
<!-- ===================================== -->

<button
    id="scrollTop"
    class="hidden fixed bottom-6 right-6 bg-warm-gold p-4 rounded-full shadow-xl hover:scale-110 transition">

    ↑

</button>

<!-- ===================================== -->
<!-- JAVASCRIPT -->
<!-- ===================================== -->

<script>

// Sidebar toggle
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
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

// Overlay click
var overlay = document.getElementById('sidebarOverlay');
if (overlay) overlay.addEventListener('click', function() {
    var sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.remove('show');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
});

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var sidebar = document.getElementById('sidebar');
        var ov = document.getElementById('sidebarOverlay');
        if (sidebar && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            if (ov) ov.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

// Hide sidebar on desktop resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        var sidebar = document.getElementById('sidebar');
        var ov = document.getElementById('sidebarOverlay');
        if (sidebar && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            if (ov) ov.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

// Init Lucide icons
lucide.createIcons();

const scrollBtn = document.getElementById('scrollTop');

window.addEventListener('scroll', () => {

    if (window.scrollY > 300) {

        scrollBtn.classList.remove('hidden');

    } else {

        scrollBtn.classList.add('hidden');

    }

});

scrollBtn.addEventListener('click', () => {

    window.scrollTo({

        top: 0,

        behavior: 'smooth'

    });

});

// Service Request Modal
function openServiceRequestModal() {
    // Step 1: Who needs assistance + Beneficiary Information
    Swal.fire({
        title: 'Online Service Request',
        html: `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div style="text-align: left; padding: 10px;">
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Who needs assistance?</h3>
                    <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">Who is this request for?</label>
                    <select id="requestFor" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                        <option value="">Select an option</option>
                        <option value="myself">Myself</option>
                        <option value="child">My child</option>
                        <option value="parent">My parent</option>
                        <option value="family">Another family member</option>
                        <option value="assisting">Someone I am assisting</option>
                    </select>
                </div>
                <div>
                    <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 15px;">Beneficiary Information</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">First name</label>
                            <input type="text" id="firstName" placeholder="Enter first name" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                        </div>
                        <div>
                            <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Last name</label>
                            <input type="text" id="lastName" placeholder="Enter last name" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Date of birth</label>
                        <input type="date" id="dob" placeholder="Select date of birth" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Barangay</label>
                        <select id="barangay" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                            <option value="">Select barangay</option>
                            <option value="ACACIA">Acacia</option>
                            <option value="ADLAS">Adlas</option>
                            <option value="ANAHAW 1">Anahaw I</option>
                            <option value="ANAHAW 2">Anahaw 2</option>
                            <option value="BALITE I">Balite I</option>
                            <option value="BALITE II">Balite II</option>
                            <option value="BALUBAD">Balubad</option>
                            <option value="BANABA">Banaba</option>
                            <option value="BATAS">Batas</option>
                            <option value="BIGA 1">Biga 1</option>
                            <option value="BIGA 2">Biga 2</option>
                            <option value="BILUSO">Biluso</option>
                            <option value="BUCAL">Bucal</option>
                            <option value="BUHO">Buho</option>
                            <option value="BULIHAN">Bulihan</option>
                            <option value="CABANGAAN">Cabangaan</option>
                            <option value="CARMEN">Carmen</option>
                            <option value="HOYO">Hoyo</option>
                            <option value="HUKAY">Hukay</option>
                            <option value="IBA">Iba</option>
                            <option value="INCHICAN">Inchican</option>
                            <option value="IPIL 1">Ipil I</option>
                            <option value="IPIL 2">Ipil 2</option>
                            <option value="KALUBKOB">Kalubkob</option>
                            <option value="KAONG">Kaong</option>
                            <option value="LALAAN I">Lalaan I</option>
                            <option value="LALAAN II">Lalaan II</option>
                            <option value="LITLIT">Litlit</option>
                            <option value="LUCSUHIN">Lucsuhin</option>
                            <option value="LUMIL">Lumil</option>
                            <option value="MAGUYAM">Maguyam</option>
                            <option value="MALABAG">Malabag</option>
                            <option value="MALAKING TATIAO">Malaking Tatiao</option>
                            <option value="MATAAS NA BUROL">Mataas na Burol</option>
                            <option value="MUNTING ILOG">Munting Ilog</option>
                            <option value="NARRA I">Narra I</option>
                            <option value="NARRA II">Narra II</option>
                            <option value="NARRA III">Narra III</option>
                            <option value="PALIGAWAN">Paligawan</option>
                            <option value="PASONG LANGKA">Pasong Langka</option>
                            <option value="POBLACION 1">Poblacion 1</option>
                            <option value="POBLACION 2">Poblacion 2</option>
                            <option value="POBLACION 3">Poblacion 3</option>
                            <option value="POBLACION 4">Poblacion 4</option>
                            <option value="POBLACION 5">Poblacion 5</option>
                            <option value="POOC I">Pooc I</option>
                            <option value="POOC II">Pooc II</option>
                            <option value="PULONG BUNGA">Pulong Bunga</option>
                            <option value="PULONG SAGING">Pulong Saging</option>
                            <option value="PUTING KAHOY">Putting Kahoy</option>
                            <option value="SABUTAN">Sabutan</option>
                            <option value="SAN MIGUEL I">San Miguel I</option>
                            <option value="SAN MIGUEL II">San Miguel II</option>
                            <option value="SAN VICENTE I">San Vicente I</option>
                            <option value="SAN VICENTE II">San Vicente II</option>
                            <option value="SANTOL">Santol</option>
                            <option value="TARTARIA">Tartaria</option>
                            <option value="TIBIG">Tibig</option>
                            <option value="TOLEDO">Toledo</option>
                            <option value="TUBUAN 1">Tubuan 1</option>
                            <option value="TUBUAN 2">Tubuan 2</option>
                            <option value="TUBUAN 3">Tubuan 3</option>
                            <option value="ULAT">Ulat</option>
                            <option value="YAKAL">Yakal</option>
                        </select>
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Contact number</label>
                        <input type="text" id="contactNumber" placeholder="Enter contact number" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Email address</label>
                        <input type="email" id="email" placeholder="Enter email address" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Address</label>
                        <input type="text" id="address" placeholder="Enter address" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Next',
        confirmButtonColor: '#1A237E',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#64748B',
        customClass: {
            popup: 'service-request-modal'
        },
        preConfirm: () => {
            const requestFor = Swal.getPopup().querySelector('#requestFor').value;
            const firstName = Swal.getPopup().querySelector('#firstName').value;
            const lastName = Swal.getPopup().querySelector('#lastName').value;
            const dob = Swal.getPopup().querySelector('#dob').value;
            const barangay = Swal.getPopup().querySelector('#barangay').value;
            const contactNumber = Swal.getPopup().querySelector('#contactNumber').value;
            const email = Swal.getPopup().querySelector('#email').value;
            const address = Swal.getPopup().querySelector('#address').value;

            if (!requestFor) {
                Swal.showValidationMessage('Please select who this request is for');
                return false;
            }
            if (!firstName || !lastName) {
                Swal.showValidationMessage('Please enter both first and last name');
                return false;
            }
            if (!dob) {
                Swal.showValidationMessage('Please enter date of birth');
                return false;
            }
            if (!barangay) {
                Swal.showValidationMessage('Please enter barangay');
                return false;
            }
            if (!contactNumber) {
                Swal.showValidationMessage('Please enter contact number');
                return false;
            }
            if (!email || email.trim() === '') {
                Swal.showValidationMessage('Please enter email address');
                return false;
            }

            return { 
                request_for: requestFor, 
                first_name: firstName, 
                last_name: lastName, 
                dob: dob, 
                barangay: barangay, 
                contact_number: contactNumber, 
                email: email, 
                address: address 
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Step 2: Service Details
            Swal.fire({
                title: 'Service Request Details',
                html: `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div style="text-align: left; padding: 10px;">
                        <div style="margin-bottom: 25px;">
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Type of Service</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">What type of service do you need?</label>
                            <select id="serviceType" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                                <option value="">Select service type</option>
                                <option value="financial_assistance">Financial Assistance</option>
                                <option value="social_case_study">Social Case Study</option>
                                <option value="senior_citizen">Senior Citizen Services</option>
                                <option value="vawc">VAWC Services</option>
                                <option value="bcpc">BCPC Services</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 25px;">
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Assistance Type</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">What type of assistance do you need?</label>
                            <select id="assistanceType" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                                <option value="">Select assistance type</option>
                                <option value="medical">Medical Assistance</option>
                                <option value="educational">Educational Assistance</option>
                                <option value="food">Food Assistance</option>
                                <option value="transportation">Transportation Assistance</option>
                                <option value="burial">Burial Assistance</option>
                                <option value="livelihood">Livelihood Assistance</option>
                                <option value="emergency">Emergency Assistance</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div>
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Situation Description</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">Please provide a brief description of the client's situation</label>
                            <textarea id="situation" rows="4" placeholder="Describe the situation..." style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC; resize: vertical;"></textarea>
                        </div>
                        <div style="margin-top: 25px;">
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Upload Documents</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">Upload any supporting documents (optional)</label>
                            <div id="uploadArea" style="border: 2px dashed #1A237E; border-radius: 8px; padding: 20px; text-align: center; background: #F8FAFC; cursor: pointer; transition: all 0.3s ease;">
                                <div style="margin-bottom: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1A237E" style="width: 40px; height: 40px; margin: 0 auto;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <p style="color: #1A237E; font-size: 16px; font-weight: 600; margin: 0 0 6px 0;">Click to upload files</p>
                                <p style="color: #64748B; font-size: 13px; margin: 0;">or drag and drop files here</p>
                                <p style="color: #64748B; font-size: 12px; margin-top: 8px;">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                                <p style="color: #64748B; font-size: 12px;">Maximum file size: 10MB per file</p>
                            </div>
                            <input type="file" id="documents" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;" onchange="handleFileSelection(this)">
                            <div id="fileList" style="margin-top: 12px;"></div>
                        </div>
                    </div>
                `,
                width: '600px',
                confirmButtonText: 'Submit Request',
                confirmButtonColor: '#1A237E',
                showCancelButton: true,
                cancelButtonText: 'Back',
                cancelButtonColor: '#64748B',
                customClass: {
                    popup: 'service-request-modal'
                },
                didOpen: () => {
                    // Initialize drag and drop after modal opens
                    setTimeout(() => {
                        const uploadArea = document.getElementById('uploadArea');
                        const documentsInput = document.getElementById('documents');
                        
                        if (uploadArea && documentsInput) {
                            // Click handler
                            uploadArea.addEventListener('click', () => {
                                documentsInput.click();
                            });
                            
                            // Change handler
                            documentsInput.addEventListener('change', () => {
                                handleFileSelection(documentsInput);
                            });
                            
                            // Drag and drop handlers
                            uploadArea.addEventListener('dragover', (e) => {
                                e.preventDefault();
                                uploadArea.style.background = '#EEF2FF';
                                uploadArea.style.borderColor = '#1A237E';
                            });
                            
                            uploadArea.addEventListener('dragleave', (e) => {
                                e.preventDefault();
                                uploadArea.style.background = '#F8FAFC';
                                uploadArea.style.borderColor = '#1A237E';
                            });
                            
                            uploadArea.addEventListener('drop', (e) => {
                                e.preventDefault();
                                uploadArea.style.background = '#F8FAFC';
                                uploadArea.style.borderColor = '#1A237E';
                                
                                const files = e.dataTransfer.files;
                                
                                // Validate file types
                                const validTypes = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'];
                                const validFiles = [];
                                
                                for (let i = 0; i < files.length; i++) {
                                    const file = files[i];
                                    const extension = '.' + file.name.split('.').pop().toLowerCase();
                                    
                                    if (validTypes.includes(extension)) {
                                        validFiles.push(file);
                                    } else {
                                        Swal.fire({
                                            title: 'Invalid File Type',
                                            text: file.name + ' is not a supported file type.',
                                            icon: 'warning',
                                            confirmButtonColor: '#1A237E',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                }
                                
                                if (validFiles.length > 0) {
                                    // Get existing files and add new ones
                                    const dataTransfer = new DataTransfer();
                                    
                                    // Add existing files
                                    if (documentsInput.files.length > 0) {
                                        for (let i = 0; i < documentsInput.files.length; i++) {
                                            dataTransfer.items.add(documentsInput.files[i]);
                                        }
                                    }
                                    
                                    // Add new files
                                    for (let i = 0; i < validFiles.length; i++) {
                                        dataTransfer.items.add(validFiles[i]);
                                    }
                                    
                                    documentsInput.files = dataTransfer.files;
                                    handleFileSelection(documentsInput);
                                }
                            });
                        }
                    }, 100);
                },
                preConfirm: () => {
                    const serviceType = Swal.getPopup().querySelector('#serviceType').value;
                    const assistanceType = Swal.getPopup().querySelector('#assistanceType').value;
                    const situation = Swal.getPopup().querySelector('#situation').value;

                    if (!serviceType) {
                        Swal.showValidationMessage('Please select a service type');
                        return false;
                    }
                    if (!assistanceType) {
                        Swal.showValidationMessage('Please select an assistance type');
                        return false;
                    }
                    if (!situation) {
                        Swal.showValidationMessage('Please provide a brief description of the situation');
                        return false;
                    }

                    return { 
                        service_type: serviceType, 
                        assistance_type: assistanceType, 
                        situation: situation
                    };
                }
            }).then((result2) => {
                if (result2.isConfirmed) {
                    // Combine both steps data
                    const formData = new FormData();
                    Object.keys(result.value).forEach(key => {
                        formData.append(key, result.value[key]);
                    });
                    Object.keys(result2.value).forEach(key => {
                        formData.append(key, result2.value[key]);
                    });
                    
                    // Add files directly from the input element
                    const documentsInput = Swal.getPopup().querySelector('#documents');
                    if (documentsInput && documentsInput.files.length > 0) {
                        for (let i = 0; i < documentsInput.files.length; i++) {
                            formData.append('documents[]', documentsInput.files[i]);
                        }
                    }

                    console.log('Submitting data:', formData); // Debug log

                    // Send data to backend
                    fetch('/service-request', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            Swal.fire({
                                title: 'Request Submitted',
                                text: 'Your service request has been submitted successfully. An MSWDO officer will review your request.',
                                icon: 'success',
                                confirmButtonColor: '#1A237E',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'There was an error submitting your request. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#DC2626',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'There was an error submitting your request. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#DC2626',
                            confirmButtonText: 'OK'
                        });
                    });
                } else if (result2.dismiss === Swal.DismissReason.cancel) {
                    // If cancelled, go back to step 1
                    openServiceRequestModal();
                }
            });
        }
    });
}

</script>

<script>
function handleFileSelection(input) {
    console.log('Files selected:', input.files.length);
    console.log('Files:', input.files);
    updateFileList(input);
}

function updateFileList(input) {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    
    console.log('Updating file list with', input.files.length, 'files');
    
    if (input.files.length > 0) {
        const fileListHtml = document.createElement('div');
        fileListHtml.style.cssText = 'background: #F8FAFC; border-radius: 8px; padding: 12px; border: 1px solid #E2E8F0;';
        
        const title = document.createElement('h4');
        title.textContent = 'Selected Files (' + input.files.length + ')';
        title.style.cssText = 'margin: 0 0 10px 0; color: #1A237E; font-size: 14px; font-weight: 700;';
        fileListHtml.appendChild(title);
        
        const list = document.createElement('ul');
        list.style.cssText = 'margin: 0; padding-left: 20px;';
        
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const listItem = document.createElement('li');
            listItem.style.cssText = 'margin-bottom: 6px; color: #1F2937; font-size: 13px;';
            listItem.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            list.appendChild(listItem);
        }
        
        fileListHtml.appendChild(list);
        fileList.appendChild(fileListHtml);
    }
}

function formatFileSize(bytes) {
    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}
</script>

</body>
</html>