<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MSWDO Silang</title>
    <meta name="description" content="Municipal Social Welfare and Development Office - Municipality of Silang">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body class="bg-[#F8FAFC] text-[#1F2937] antialiased">
    <!-- ========================= -->
    <!-- NAVBAR -->
    <!-- ========================= -->
    <header class="fixed top-0 left-0 right-0 z-50 w-full shadow-lg" style="background: #1A237E;">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between" style="height: 60px;">
                @php
                $logo = null;
                if (file_exists(public_path('images/mswdo-logo.png'))) {
                    $logo = 'mswdo-logo.png';
                } else {
                    $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
                    if (!empty($files))
                        $logo = basename($files[0]);
                }
                @endphp
                <!-- Mobile: hamburger + text (left) ... logo (right) -->
                <div class="flex items-center gap-3 lg:hidden min-w-0 flex-1">
                    <button id="menuButton"
                        class="shrink-0 p-2 rounded-lg hover:bg-white/10 transition focus:outline-none"
                        style="color: #fff;" aria-label="Toggle navigation">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-white font-bold text-sm leading-tight" style="white-space:nowrap;">MSWDO SILANG</h1>
                        <p class="text-white/60 text-[10px] leading-tight" style="white-space:nowrap;">Municipal Social Welfare &amp; Development Office</p>
                    </div>
                </div>
                <div class="lg:hidden shrink-0">
                    @if($logo)
                        <img src="{{ asset('images/' . $logo) }}" style="width:40px;height:40px;object-fit:contain;border-radius:50%;flex-shrink:0;" alt="Logo">
                    @endif
                </div>

                <!-- Desktop: logo text (left) ... nav links (right) -->
                <a href="#" class="hidden lg:flex items-center gap-3 shrink-0">
                    <div class="h-14 w-14 rounded-full p-1 shrink-0">
                        @if($logo)
                            <img src="{{ asset('images/' . $logo) }}" class="rounded-full h-full w-full object-cover">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-lg tracking-tight leading-tight">MSWDO SILANG</h1>
                        <p class="text-white/60 text-xs leading-tight">Municipal Social Welfare &amp; Development Office</p>
                    </div>
                </a>
                <nav class="hidden lg:flex items-center gap-8 text-white/75">
                    <a href="#home" class="hover:text-[#FBC02D] transition">Home</a>
                    <a href="#services" class="hover:text-[#FBC02D] transition">Services</a>
                    <a href="#about" class="hover:text-[#FBC02D] transition">About</a>
                    <a href="#programs" class="hover:text-[#FBC02D] transition">Programs</a>
                    <a href="#contact" class="hover:text-[#FBC02D] transition">Contact</a>
                    <a href="/admin" class="navbar-login-btn">Login</a>
                </nav>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu">
            <ul class="mobile-menu-list">
                <li>
                    <a href="#home" class="active">
                        <i data-lucide="home" style="width:20px;height:20px"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="#services">
                        <i data-lucide="briefcase" style="width:20px;height:20px"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li>
                    <a href="#about">
                        <i data-lucide="info" style="width:20px;height:20px"></i>
                        <span>About</span>
                    </a>
                </li>
                <li>
                    <a href="#programs">
                        <i data-lucide="heart-handshake" style="width:20px;height:20px"></i>
                        <span>Programs</span>
                    </a>
                </li>
                <li>
                    <a href="#contact">
                        <i data-lucide="phone" style="width:20px;height:20px"></i>
                        <span>Contact</span>
                    </a>
                </li>
                <li>
                    <hr class="mobile-menu-divider">
                </li>
                <li>
                    <a href="/admin">
                        <i data-lucide="log-in" style="width:20px;height:20px"></i>
                        <span>Login</span>
                    </a>
                </li>
            </ul>
        </div>
    </header>
    <!-- ========================= -->
    <!-- HERO -->
    <!-- ========================= -->
    <section id="home"
        class="relative overflow-hidden pt-24 sm:pt-28 lg:pt-24 xl:pt-32 pb-24 sm:pb-28 lg:pb-24 xl:pb-36 border-b border-slate-200/50 bg-[#F8FAFC]">
        <!-- Hero Background Image -->
        <div class="absolute inset-0 z-0 pointer-events-none select-none">
            <img src="{{ asset('images/background.png') }}" class="w-full h-full object-cover object-center"
                alt="Hero Background">
        </div>

        <!-- Faint Watermark Background Logos (90% transparent / 10% opacity watermark) -->
        <div
            class="absolute left-0 sm:left-2 lg:left-4 xl:left-8 top-1/2 -translate-y-1/2 w-[28%] sm:w-[26%] lg:w-[22%] xl:w-[28%] max-w-[130px] sm:max-w-[180px] md:max-w-[220px] lg:max-w-[260px] xl:max-w-[340px] opacity-10 pointer-events-none select-none">
            <img src="{{ asset('images/dswdlogo.png') }}" class="w-full h-auto object-contain" alt="DSWD Logo">
        </div>
        <div
            class="absolute right-0 sm:right-2 lg:right-4 xl:right-8 top-1/2 -translate-y-1/2 w-[28%] sm:w-[26%] lg:w-[22%] xl:w-[28%] max-w-[120px] sm:max-w-[170px] md:max-w-[210px] lg:max-w-[250px] xl:max-w-[330px] opacity-10 pointer-events-none select-none">
            <img src="{{ asset('images/silangseal.png') }}" class="w-full h-auto object-contain" alt="Silang Seal">
        </div>

        <!-- Central content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Simple Elegant Eyebrow Tag -->
                <p
                    class="text-xs sm:text-sm font-bold tracking-wider uppercase mb-2 sm:mb-2.5 lg:mb-2 xl:mb-4 leading-normal">
                    <span class="text-[#1A237E]">Official Portal</span>
                    <span class="text-[#FBC02D] mx-1.5 sm:mx-2">&bull;</span>
                    <span class="text-[#D32F2F]">Municipality of Silang</span>
                </p>

                <!-- Headline -->
                <h1 class="tracking-tight">
                    <span
                        class="block text-xl sm:text-2xl md:text-3xl lg:text-3xl xl:text-4xl font-extrabold text-[#1F2937] leading-tight mb-1 sm:mb-1.5">
                        Municipal Social Welfare &amp; Development Office
                    </span>
                    <span
                        class="block text-4xl sm:text-5xl md:text-6xl lg:text-6xl xl:text-7xl font-black text-[#1A237E] uppercase tracking-normal leading-none mt-0.5 sm:mt-1">
                        SILANG
                    </span>
                </h1>

                <!-- Subtitle -->
                <p
                    class="text-base sm:text-lg md:text-xl lg:text-lg xl:text-2xl font-bold text-[#1F2937] mt-2.5 sm:mt-3.5 lg:mt-3 xl:mt-5 tracking-tight leading-snug">
                    Empowering &amp; Uplifting <span class="text-[#D32F2F] font-extrabold">Every Silangueño</span>,
                    Together.
                </p>

                <!-- Description -->
                <p
                    class="mt-2 sm:mt-3 lg:mt-2.5 xl:mt-4 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base text-[#6B7280] leading-relaxed max-w-2xl xl:max-w-3xl mx-auto font-medium">
                    Providing compassionate protection, development opportunities, and responsive welfare assistance to
                    support families, children, and seniors in Silang.
                </p>

                <!-- CTA Actions -->
                <div
                    class="mt-4 sm:mt-6 lg:mt-4 xl:mt-8 flex flex-row items-center justify-center gap-2.5 sm:gap-3.5 w-full sm:w-auto">
                    <a href="#services"
                        class="flex-1 sm:flex-none sm:w-auto text-center bg-[#1A237E] text-white px-6 sm:px-8 xl:px-9 py-2.5 sm:py-3.5 rounded-xl text-sm sm:text-base xl:text-lg font-bold hover:bg-[#111827] hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
                        Explore Services
                    </a>
                    <a href="#contact"
                        class="flex-1 sm:flex-none sm:w-auto text-center border-2 border-[#CBD5E1] text-[#1F2937] hover:border-[#1A237E] hover:text-[#1A237E] px-6 sm:px-8 xl:px-9 py-2.5 sm:py-3.5 rounded-xl text-sm sm:text-base xl:text-lg font-bold hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
                        Contact Us
                    </a>
                </div>

            </div>
        </div>
    </section>
    <!-- ========================= -->
    <!-- OVERLAPPING QUICK SERVICES -->
    <!-- ========================= -->
    <section id="services" class="py-12 bg-slate-50 relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">

            <div
                class="relative bg-white rounded-2xl sm:rounded-3xl lg:rounded-[2.5rem] shadow-[0_20px_50px_rgba(21,61,107,0.08)] p-6 sm:p-8 lg:p-12 border border-slate-100 -mt-16 sm:-mt-20 lg:-mt-16 xl:-mt-24 mb-16">
                <div class="text-center mb-8 sm:mb-12">

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-primary mt-2">Get Started with Our Online
                        Services</h2>
                    <p class="text-slate-500 mt-2 text-xs sm:text-sm max-w-md mx-auto">Providing quality welfare
                        services and
                        assistance programs for every Silangueño.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Service 1: Financial Assistance -->
                    <div
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-warm-gold/10 text-warm-gold flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M5.25 7.5h13.5m-12 9h10.5M5.25 10.5h13.5m-12 3h10.5" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-primary">Financial Assistance</h3>
                            <p class="text-slate-600 text-xs mt-3 leading-relaxed">
                                Emergency financial, medical, burial, and transportation assistance for individuals and
                                families in crisis.
                            </p>
                        </div>
                        <a href="/financial-assistance"
                            class="mt-6 flex items-center text-warm-gold font-bold text-[10px] uppercase tracking-wider gap-1 group-hover:translate-x-1 transition duration-200 cursor-pointer">
                            Apply & Details
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                    <!-- Service 2: Protection & VAWC -->
                    <div
                        class="group bg-slate-50 hover:bg-white hover:border-accent/40 rounded-2xl p-6 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-accent/10 text-accent flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0 1 12 2.714z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-primary">Protection & VAWC</h3>
                            <p class="text-slate-600 text-xs mt-3 leading-relaxed">
                                Counseling, shelter assistance, and immediate protective services for survivors of
                                domestic violence and abuse.
                            </p>
                        </div>
                        <a href="/service-request"
                            class="mt-6 flex items-center text-accent font-bold text-[10px] uppercase tracking-wider gap-1 group-hover:translate-x-1 transition duration-200 cursor-pointer">
                            Get Support
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                    <!-- Service 3: Senior & PWD Welfare -->
                    <div
                        class="group bg-slate-50 hover:bg-white hover:border-primary/40 rounded-2xl p-6 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-primary">Senior Citizen</h3>
                            <p class="text-slate-600 text-xs mt-3 leading-relaxed">
                                Social pension applications, ID issuances, and community-centered assistance programs
                                for senior citizens and PWDs.
                            </p>
                        </div>
                        <a href="/service-request"
                            class="mt-6 flex items-center text-primary font-bold text-[10px] uppercase tracking-wider gap-1 group-hover:translate-x-1 transition duration-200 cursor-pointer">
                            Apply Pension
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                    <!-- Service 4: Social Case Study -->
                    <div
                        class="group bg-slate-50 hover:bg-white hover:border-emerald-500/40 rounded-2xl p-6 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-primary">Social Case Study</h3>
                            <p class="text-slate-600 text-xs mt-3 leading-relaxed">
                                Formal social case study reports for hospitalization, medicines, burial referrals, and
                                government benefits support.
                            </p>
                        </div>
                        <a href="/service-request"
                            class="mt-6 flex items-center text-emerald-600 font-bold text-[10px] uppercase tracking-wider gap-1 group-hover:translate-x-1 transition duration-200 cursor-pointer">
                            Request Report
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===================================== -->
    <!-- ABOUT MSWDO -->
    <!-- ===================================== -->
    <section id="about" class="py-16 sm:py-20 lg:py-24 bg-offwhite">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                <div>
                    <span class="text-primary font-semibold uppercase tracking-widest">
                        About Us
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mt-4">
                        Serving the People of
                        <span class="text-primary">Silang</span>
                    </h2>
                    <p class="mt-6 sm:mt-8 text-base sm:text-lg leading-7 sm:leading-8 text-secondary">
                        The Municipal Social Welfare and Development Office (MSWDO)
                        is dedicated to uplifting the lives of individuals,
                        families, and communities through responsive social welfare
                        programs and inclusive development initiatives.
                    </p>
                    <p class="mt-4 sm:mt-5 text-base sm:text-lg leading-7 sm:leading-8 text-secondary">
                        We promote social justice, protect vulnerable sectors,
                        and ensure that every citizen receives quality services
                        regardless of age, gender, or social status.
                    </p>
                </div>
                <div>
                    <div class="grid grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Compassion
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Delivering services with empathy and dignity.
                            </p>
                        </div>
                        <div class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Integrity
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Transparent and accountable public service.
                            </p>
                        </div>
                        <div class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Development
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Empowering individuals to become self-sufficient.
                            </p>
                        </div>
                        <div class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Public Service
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Committed to excellent government service.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===================================== -->
    <!-- MISSION & VISION -->
    <!-- ===================================== -->
    <section class="py-16 sm:py-20 lg:py-24 bg-offwhite">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-6 lg:gap-10">
                <div class="bg-primary text-white rounded-2xl sm:rounded-3xl p-8 sm:p-12 shadow-xl">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-6 sm:mb-8">
                        Mission
                    </h2>
                    <p class="text-base sm:text-lg leading-7 sm:leading-8">
                        To provide efficient, compassionate, and accessible social welfare
                        services that improve the quality of life of every Silangueño through
                        people-centered programs, community participation, and sustainable
                        development.
                    </p>
                </div>
                <div class="bg-warm-gold rounded-2xl sm:rounded-3xl p-8 sm:p-12 shadow-xl">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 mb-6 sm:mb-8">
                        Vision
                    </h2>
                    <p class="text-base sm:text-lg leading-7 sm:leading-8 text-slate-800">
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
    <section class="py-16 sm:py-20 lg:py-24 bg-offwhite">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold">
                    How to Apply
                </h2>
                <p class="text-secondary mt-4">
                    Simple steps to request assistance.
                </p>
            </div>
            <div class="flex flex-wrap justify-center gap-4 sm:gap-6 lg:gap-8 mt-8 sm:mt-12 lg:mt-16">
                <div class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl">
                        1
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Submit Request
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-primary -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl">
                        2
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Document Review
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-primary -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl">
                        3
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Assessment
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-primary -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl">
                        4
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Approval
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-primary -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-warm-gold text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl">
                        5
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Release Assistance
                    </h3>
                </div>
            </div>
        </div>
    </section>
    <!-- ===================================== -->
    <!-- PROGRAMS IN ACTION (AUTOMATIC CAROUSEL) -->
    <!-- ===================================== -->
    <section id="programs" class="py-10 sm:py-14 md:py-18 bg-white border-t border-gray-100">

        <div class="max-w-5xl mx-auto px-3 sm:px-6">
            <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                    Programs &amp; Activities in Action
                </h2>
                <p class="text-gray-600 mt-2 text-xs sm:text-sm md:text-base max-w-xl mx-auto px-2">
                    Snapshots of MSWDO Silang social welfare services, client consultations, and assistance operations.
                </p>
            </div>

            <!-- Automatic Carousel Container -->
            <div id="simpleCarousel" class="programs-carousel">
                <!-- Slides -->
                @php
                    $carouselImages = [
                        ['file' => 'fa1.jpg', 'title' => 'Financial Assistance Intake & Verification'],
                        ['file' => 'fa2.jpg', 'title' => 'Senior Citizen & Sectoral Assistance Desk'],
                        ['file' => 'fa3.jpg', 'title' => 'Direct Cash Assistance Release'],
                        ['file' => 'socialcase1.jpg', 'title' => 'Social Case Study Intake & Interview'],
                        ['file' => 'socialcase2.jpg', 'title' => 'Personalized Welfare Consultation'],
                        ['file' => 'socialcase3.jpg', 'title' => 'Community Intake & Evaluation Desk'],
                        ['file' => 'socialcase4.jpg', 'title' => 'Social Case Report Documentation & Review'],
                    ];
                @endphp

                @foreach ($carouselImages as $index => $item)
                    <div class="carousel-slide absolute inset-0 w-full h-full"
                        style="transition: opacity 0.8s ease-in-out; opacity: {{ $index === 0 ? '1' : '0' }}; z-index: {{ $index === 0 ? '10' : '1' }}; pointer-events: {{ $index === 0 ? 'auto' : 'none' }};"
                        data-index="{{ $index }}">
                        <img src="{{ asset('images/' . $item['file']) }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover object-center" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        <!-- Clean gradient overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent pointer-events-none"></div>
                        <!-- Caption positioned cleanly on bottom left -->
                        <div class="programs-carousel-caption absolute bottom-0 inset-x-0 z-10 pointer-events-none">
                            <p class="programs-carousel-title font-semibold text-white drop-shadow-md line-clamp-2">
                                {{ $item['title'] }}
                            </p>
                        </div>
                    </div>
                @endforeach

                <!-- Indicator Dots -->
                <div class="programs-carousel-dots absolute z-20 flex items-center">
                    @foreach ($carouselImages as $index => $item)
                        <button type="button" class="programs-carousel-dot carousel-dot {{ $index === 0 ? 'active' : '' }}"
                            data-index="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================== -->
    <!-- CALL TO ACTION -->
    <!-- ===================================== -->
    <section class="py-16 sm:py-20 bg-primary relative overflow-hidden">
        <div class="max-w-5xl mx-auto text-center px-4 sm:px-6 relative z-10">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl sm:text-5xl font-bold text-white">
                Need Social Assistance?
            </h2>
            <p class="text-offwhite mt-4 sm:mt-6 text-base sm:text-lg sm:text-xl max-w-2xl mx-auto">
                Our dedicated team is ready to assist you with your concerns. Select your required social service below to proceed.
            </p>
            <div class="mt-8 sm:mt-10 flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                <a href="/financial-assistance"
                    class="bg-warm-gold text-[#1F2937] px-8 sm:px-10 py-4 rounded-xl font-bold hover:scale-105 transition shadow-lg inline-flex items-center justify-center text-base sm:text-lg gap-2.5">
                    <i data-lucide="hand-coins" class="w-5 h-5"></i>
                    <span>Financial Assistance</span>
                </a>
                <a href="/service-request"
                    class="border-2 border-warm-gold/90 bg-white/10 hover:bg-warm-gold hover:text-[#1F2937] text-white px-8 sm:px-10 py-4 rounded-xl font-bold hover:scale-105 transition shadow-lg inline-flex items-center justify-center text-base sm:text-lg gap-2.5 backdrop-blur">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span>Social Case Study</span>
                </a>
            </div>
        </div>
    </section>
    <!-- ===================================== -->
    <!-- CONTACT SECTION -->
    <!-- ===================================== -->
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
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5 text-primary shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A2.25 2.25 0 0 0 18 8.083h-3.75V6.75a2.25 2.25 0 0 0-4.5 0v1.333H6a2.25 2.25 0 0 0-2.25 2.25V21" />
                        </svg>
                        Office Information
                    </h3>
                    <div class="info-list">
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="info-icon">
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="info-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.188-4.166-7-7l1.3-1.3c.362-.272.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.11-1.008H5.036a2.25 2.25 0 0 0-2.25 2.25v1.356Z" />
                            </svg>
                            <div class="info-content">
                                <h4 class="info-label">Phone</h4>
                                <p class="info-text">
                                    <a href="tel:0464140202" class="info-link">(046) 414-0202</a>
                                </p>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="info-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <div class="info-content">
                                <h4 class="info-label">Email</h4>
                                <p class="info-text">
                                    <a href="mailto:socialwelfaresilang@gmail.com" class="info-link">socialwelfaresilang@gmail.com</a>
                                </p>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="info-icon">
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
                <!-- Emergency Hotlines -->
                <div class="emergency-card">
                    <div class="emergency-header">
                        <div class="emergency-title-wrapper">
                            <h3 class="emergency-title">Emergency Hotlines</h3>
                        </div>
                        <span class="emergency-badge">Emergency 24/7</span>
                    </div>
                    <div id="emergencyGrid" class="emergency-grid">
                        <div class="hotline-item">
                            <span class="hotline-label">Silang Municipal Office:</span>
                            <a href="tel:0464140202" class="hotline-value">(046) 414-0202</a>
                        </div>
                        <div class="hotline-item">
                            <span class="hotline-label">PDRRMO (Silang):</span>
                            <a href="tel:0464240203" class="hotline-value">(046) 424-0203</a>
                        </div>
                        <div class="hotline-item">
                            <span class="hotline-label">PNP WCPD – Silang:</span>
                            <a href="tel:09983970222" class="hotline-value">0998-397-0222</a>
                        </div>
                        <div class="hotline-item">
                            <span class="hotline-label">Silang PNP Mobile:</span>
                            <a href="tel:09985985622" class="hotline-value">0998-598-5622</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">DSWD AICS:</span>
                            <a href="tel:89622813" class="hotline-value">8962-2813</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">DSWD Central Office:</span>
                            <a href="tel:89318101" class="hotline-value">8-931-8101</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">DSWD Mobile:</span>
                            <a href="tel:09199116200" class="hotline-value">0919-911-6200</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Makabata Helpline:</span>
                            <a href="tel:1383" class="hotline-value">1383</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Bantay Bata Hotline:</span>
                            <a href="tel:163" class="hotline-value">163</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Emergency (All):</span>
                            <a href="tel:911" class="hotline-value">911</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">NCMH Mental Health:</span>
                            <a href="tel:1553" class="hotline-value">1553</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Complaints Hotline:</span>
                            <a href="tel:8888" class="hotline-value">8888</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Anti-Trafficking Line:</span>
                            <a href="tel:1343" class="hotline-value">1343</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">PNP Women's Desk:</span>
                            <a href="tel:117" class="hotline-value">117</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Medical Assistance:</span>
                            <a href="tel:1555" class="hotline-value">1555</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">DOH Hotline:</span>
                            <a href="tel:894COVID" class="hotline-value">894-COVID</a>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">DSWD Help:</span>
                            <a href="tel:09329333251" class="hotline-value">0932-933-3251</a>
                        </div>
                    </div>
                    <button id="showAllHotlines"
                        class="lg:hidden mt-4 text-warm-gold font-semibold text-sm hover:underline">
                        Show All
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- ===================================== -->
    <!-- FOOTER -->
    <!-- ===================================== -->
    <footer class="bg-primary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-10 lg:py-20">
            <div class="grid gap-6 sm:gap-8 lg:gap-12 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                <div class="hidden sm:block lg:col-span-1">
                    <h2 class="text-2xl sm:text-3xl font-bold">
                        MSWDO Silang
                    </h2>
                    <p class="mt-4 sm:mt-5 text-offwhite leading-7 sm:leading-8 text-sm sm:text-base hidden lg:block">
                        Municipal Social Welfare and Development Office committed to
                        providing quality, transparent, and compassionate public
                        service.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <h3 class="font-bold text-lg sm:text-xl mb-4 sm:mb-5">
                        Quick Links
                    </h3>
                    <ul class="space-y-2 sm:space-y-3 text-offwhite text-sm sm:text-base">
                        <li><a href="#home" class="hover:text-warm-gold transition">Home</a></li>
                        <li><a href="#services" class="hover:text-warm-gold transition">Services</a></li>
                        <li><a href="#about" class="hover:text-warm-gold transition">About</a></li>
                        <li><a href="#contact" class="hover:text-warm-gold transition">Contact</a></li>
                    </ul>
                </div>
                <div class="hidden lg:block">
                    <h3 class="font-bold text-lg sm:text-xl mb-4 sm:mb-5">
                        Programs
                    </h3>
                    <ul class="space-y-2 sm:space-y-3 text-offwhite text-sm sm:text-base">
                        <li>Financial Assistance</li>
                        <li>VAWC</li>
                        <li>BCPC</li>
                        <li>Senior Citizens</li>
                        <!-- <li>Solo Parent</li> -->
                        <li>Social Case Study</li>
                    </ul>
                </div>
                <div class="hidden lg:block">
                    <h3 class="font-bold text-lg sm:text-xl mb-4 sm:mb-5">
                        Office Hours
                    </h3>
                    <p class="text-offwhite leading-6 sm:leading-7 text-sm sm:text-base">
                        Monday - Friday
                        <br><br>
                        8:00 AM - 5:00 PM
                    </p>
                </div>
            </div>
            <div class="flex justify-center items-center text-[10px] sm:text-xs sm:text-sm">
                <p class="text-offwhite text-center">
                    © {{ date('Y') }} MSWDO Silang. All Rights Reserved.
                </p>
            </div>
        </div>
    </footer>
    <!-- ===================================== -->
    <!-- SCROLL TO TOP -->
    <!-- ===================================== -->
    <button id="scrollTop"
        class="hidden fixed bottom-6 right-6 bg-warm-gold p-4 rounded-full shadow-xl hover:scale-110 transition">
        ↑
    </button>
    <!-- ===================================== -->
    <!-- JAVASCRIPT -->
    <!-- ===================================== -->
    <script src="{{ asset('js/welcome.js') }}"></script>
</body>

</html>