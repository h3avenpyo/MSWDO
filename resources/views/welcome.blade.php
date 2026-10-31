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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/service-map.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
</head>

<body class="bg-[#F8FAFC] text-[#1F2937] antialiased">
    <!-- ========================= -->
    <!-- NAVBAR -->
    <!-- ========================= -->
    <header class="fixed top-0 left-0 right-0 z-50 w-full shadow-lg"
        style="background: linear-gradient(135deg, #b11116 0%, #8A0D11 100%);">
        <div class="max-w-7xl mx-auto px-4">
            <div class="welcome-navbar flex items-center justify-between h-[60px] lg:h-20">
                @php
                    $logo = 'IserveIcon.png';
                    if (!file_exists(public_path('images/IserveIcon.png'))) {
                        $logo = null;
                        if (file_exists(public_path('images/mswdo-logo.png'))) {
                            $logo = 'mswdo-logo.png';
                        } else {
                            $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
                            if (!empty($files)) {
                                $logo = basename($files[0]);
                            }
                        }
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
                        <h1 class="text-white font-bold text-sm leading-tight" style="white-space:nowrap;">iSERVE SILANG
                        </h1>
                        <p class="text-white/60 text-[10px] leading-tight" style="white-space:nowrap;">Municipal Social
                            Welfare &amp; Development Office</p>
                    </div>
                </div>
                <div class="lg:hidden shrink-0">
                    @if ($logo)
                        <img src="{{ asset('images/' . $logo) }}"
                            style="width:40px;height:40px;object-fit:contain;border-radius:50%;flex-shrink:0;"
                            alt="Logo">
                    @endif
                </div>

                <!-- Desktop: logo text (left) ... nav links (right) -->
                <a href="#" class="hidden lg:flex items-center gap-3 shrink-0">
                    <div class="h-14 w-14 rounded-full p-1 shrink-0">
                        @if ($logo)
                            <img src="{{ asset('images/' . $logo) }}" class="rounded-full h-full w-full object-cover">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-lg tracking-tight leading-tight">iSERVE SILANG</h1>
                        <p class="text-white/60 text-xs leading-tight">Municipal Social Welfare &amp; Development Office
                        </p>
                    </div>
                </a>
                <nav class="hidden lg:flex items-center gap-8 text-white/90">
                    <a href="#home" class="hover:text-[#FBC02D] transition font-medium">Home</a>
                    <a href="#services" class="hover:text-[#FBC02D] transition font-medium">Services</a>
                    <a href="#about" class="hover:text-[#FBC02D] transition font-medium">About</a>
                    <a href="#programs" class="hover:text-[#FBC02D] transition font-medium">Programs</a>
                    <a href="#contact" class="hover:text-[#FBC02D] transition font-medium">Contact</a>
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
                    <a href="/admin" class="navbar-login-btn">
                        <i data-lucide="log-in" style="width:20px;height:20px" aria-hidden="true"></i>
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
        class="relative overflow-hidden pt-24 sm:pt-28 lg:pt-28 xl:pt-32 pb-24 sm:pb-28 lg:pb-24 xl:pb-36 border-b border-slate-200/50 bg-[#F8FAFC]">
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
                    <span class="text-[#b11116]">Official Portal</span>
                    <span class="text-[#FBC02D] mx-1.5 sm:mx-2">&bull;</span>
                    <span class="text-[#b11116]">Municipality of Silang</span>
                </p>

                <!-- Headline -->
                <h1 class="tracking-tight">
                    <span
                        class="block text-xl sm:text-2xl md:text-3xl lg:text-3xl xl:text-4xl font-extrabold text-[#1F2937] leading-tight mb-1 sm:mb-1.5">
                        Municipal Social Welfare &amp; Development Office
                    </span>
                    <span
                        class="block text-4xl sm:text-5xl md:text-6xl lg:text-6xl xl:text-7xl font-black text-[#b11116] uppercase tracking-normal leading-none mt-0.5 sm:mt-1">
                        SILANG
                    </span>
                </h1>

                <!-- Subtitle -->
                <p
                    class="text-base sm:text-lg md:text-xl lg:text-lg xl:text-2xl font-bold text-[#1F2937] mt-2.5 sm:mt-3.5 lg:mt-3 xl:mt-5 tracking-tight leading-snug">
                    Empowering &amp; Uplifting <span class="text-[#b11116] font-extrabold">Every Silangueño</span>,
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
                    class="welcome-hero-actions mt-4 sm:mt-6 lg:mt-4 xl:mt-8 flex flex-row items-center justify-center gap-2.5 sm:gap-3.5 w-full sm:w-auto">
                    <a href="#services"
                        class="flex-1 sm:flex-none sm:w-auto text-center bg-[#b11116] text-white px-6 sm:px-8 xl:px-9 py-2.5 sm:py-3.5 rounded-xl text-sm sm:text-base xl:text-lg font-bold hover:bg-[#8A0D11] hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
                        Explore Services
                    </a>
                    <a href="#contact"
                        class="flex-1 sm:flex-none sm:w-auto text-center border-2 border-[#CBD5E1] text-[#1F2937] hover:border-[#b11116] hover:text-[#b11116] px-6 sm:px-8 xl:px-9 py-2.5 sm:py-3.5 rounded-xl text-sm sm:text-base xl:text-lg font-bold hover:-translate-y-0.5 active:translate-y-0 transition duration-200">
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
                class="relative bg-white rounded-2xl sm:rounded-3xl lg:rounded-[2.5rem] shadow-[0_20px_50px_rgba(177,17,22,0.06)] p-6 sm:p-8 lg:p-12 border border-slate-100 -mt-16 sm:-mt-20 lg:-mt-16 xl:-mt-24 mb-16">
                <div data-reveal="fade" class="text-center mb-8 sm:mb-12">

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#b11116] mt-2">Get Started with Our
                        Services</h2>
                    <p class="text-slate-500 mt-2 text-xs sm:text-sm max-w-md mx-auto">Providing quality welfare
                        services and
                        assistance programs for every Silangueño.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 lg:gap-8 max-w-6xl mx-auto">
                    <!-- Service 1: Financial Assistance -->
                    <div data-reveal="up"
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 sm:p-8 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <i data-lucide="hand-coins" class="w-6 h-6" aria-hidden="true"></i>
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl text-[#b11116]">Financial Assistance</h3>
                            <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                                Emergency financial, medical, burial, and transportation assistance for individuals and
                                families in crisis.
                            </p>
                        </div>
                    </div>
                    <!-- Service 2: Social Case Study -->
                    <div data-reveal="up"
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 sm:p-8 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl text-[#b11116]">Social Case Study</h3>
                            <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                                Formal social case study reports for hospitalization, medicines, burial referrals, and
                                government benefits support.
                            </p>
                        </div>
                    </div>
                    <!-- Service 3: Women & Children Protection (WCPU) -->
                    <div data-reveal="up"
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 sm:p-8 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl text-[#b11116]">Women & Children Protection</h3>
                            <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                                Protection services, incident reporting, VAWC and BCPC child monitoring, case
                                management, and crisis intervention.
                            </p>
                        </div>
                    </div>
                    <!-- Service 4: Senior Citizens -->
                    <div data-reveal="up"
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 sm:p-8 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl text-[#b11116]">Senior Citizens</h3>
                            <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                                Senior citizen registration, ID card issuance, and birthday cash gift benefits and
                                payouts for eligible Silangueño senior citizens.
                            </p>
                        </div>
                    </div>
                    <!-- Service 5: Persons with Disability -->
                    <div data-reveal="up"
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 sm:p-8 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <circle cx="12" cy="4" r="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m4 8 8 2 8-2m-8 2v5m0 0-4 6m4-6 4 6" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl text-[#b11116]">Persons with Disability</h3>
                            <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                                Assistance, referrals, and access to support programs for persons with disability
                                and their families.
                            </p>
                        </div>
                    </div>
                    <!-- Service 6: Solo Parents -->
                    <div data-reveal="up"
                        class="group bg-slate-50 hover:bg-white hover:border-warm-gold/50 rounded-2xl p-6 sm:p-8 border border-slate-100 transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1.5 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center mb-5 group-hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <circle cx="9" cy="6" r="3" />
                                    <circle cx="18" cy="11" r="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 21v-3a6 6 0 0 1 12 0v3m0 0v-2a3 3 0 0 1 6 0v2" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl text-[#b11116]">Solo Parents</h3>
                            <p class="text-slate-600 text-xs sm:text-sm mt-3 leading-relaxed">
                                Guidance, referrals, and access to support programs to help solo parents care for
                                their children and strengthen their families.
                            </p>
                        </div>
                    </div>
                </div>



                <div data-reveal="up" class="mt-8 sm:mt-12 rounded-2xl p-6 sm:p-10 text-center"
                    style="background: linear-gradient(135deg, #b11116 0%, #8A0D11 100%);">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">Need Social Assistance?</h2>
                    <p class="text-white/90 mt-3 sm:mt-4 text-sm sm:text-base max-w-2xl mx-auto">
                        Our dedicated team is ready to assist you with your concerns. Select your required social
                        service
                        below to proceed.
                    </p>
                    <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                        <a href="/financial-assistance"
                            class="w-full sm:w-auto bg-warm-gold text-slate-900 px-3 sm:px-10 py-4 rounded-xl font-bold hover:scale-105 hover:bg-[#F9A825] transition shadow-lg inline-flex items-center justify-center text-sm sm:text-lg gap-2 sm:gap-2.5">
                            <i data-lucide="hand-coins" class="w-5 h-5 shrink-0"></i>
                            <span class="whitespace-nowrap">Financial Assistance</span>
                        </a>
                        <a href="/service-request"
                            class="w-full sm:w-auto bg-warm-gold text-slate-900 px-3 sm:px-10 py-4 rounded-xl font-bold hover:scale-105 hover:bg-[#F9A825] transition shadow-lg inline-flex items-center justify-center text-sm sm:text-lg gap-2 sm:gap-2.5">
                            <i data-lucide="file-text" class="w-5 h-5 shrink-0"></i>
                            <span class="whitespace-nowrap">Social Case Study</span>
                        </a>
                    </div>

                    <section class="service-requirements" aria-labelledby="service-requirements-heading">

                        <p class="service-requirements-intro">Select a service to view its requirements.</p>
                        <details class="service-requirements-panel">
                            <summary>Medical Assistance Requirements</summary>
                            <div class="service-requirements-content">
                                <p>Please prepare the following documents before applying for medical assistance.</p>
                                <ol>
                                    <li>Letter addressed to Hon. Mayor Edward E. Carranza (Ret.)</li>
                                    <li>Medical Certificate or Medical Abstract</li>
                                    <li>Barangay Certificate of Indigency with dry seal (Patient and Claimant)</li>
                                    <li>Prescription, Laboratory Request, Treatment Protocol, Final Hospital Bill, or
                                        Running Hospital Bill</li>
                                    <li>Photocopy of Valid ID with signature or thumbmark (Patient and Claimant)</li>
                                </ol>
                            </div>
                        </details>
                        <details class="service-requirements-panel">
                            <summary>Social Case Study Report Requirements</summary>
                            <div class="service-requirements-content">
                                <p>Please prepare the following documents when requesting a Social Case Study Report.
                                </p>
                                <ol>
                                    <li>Letter addressed to Hon. Mayor Edward E. Carranza (Ret.)</li>
                                    <li>Endorsement Letter/Checklist of Requirements (if applicable)</li>
                                    <li>Medical/Clinical Abstract or Certificate, Statement of Account, Hospital Bill,
                                        Prescription, Protocol, Charge Slip, or Price Quotation (Medical Assistance)
                                    </li>
                                    <li>Authorization Letter of the Informant (if the client is not an immediate family
                                        member of the deceased), Funeral Contract, Promissory Note (if already buried),
                                        and Death Certificate (Burial Assistance)</li>
                                    <li>Photo of Damaged Property, Incident Report, or Endorsement from Barangay
                                        Official (Emergency Shelter Assistance)</li>
                                    <li>Endorsement from School/Foundation (Scholarship/Educational Assistance)</li>
                                    <li>Barangay Certificate of Indigency of Claimant and Beneficiary indicating the
                                        purpose</li>
                                    <li>Valid ID of Claimant and Beneficiary</li>
                                </ol>
                            </div>
                        </details>
                    </section>
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
                <div data-reveal="up">
                    <span class="text-[#b11116] font-semibold uppercase tracking-widest">
                        About Us
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mt-4">
                        Serving the People of
                        <span class="text-[#b11116]">Silang</span>
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
                        <div data-reveal="zoom" class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Compassion
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Delivering services with empathy and dignity.
                            </p>
                        </div>
                        <div data-reveal="zoom" class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Integrity
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Transparent and accountable public service.
                            </p>
                        </div>
                        <div data-reveal="zoom" class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
                            <div class="text-5xl mb-4">
                            </div>
                            <h3 class="font-bold text-lg sm:text-xl">
                                Development
                            </h3>
                            <p class="text-secondary mt-2 sm:mt-3 text-sm sm:text-base">
                                Empowering individuals to become self-sufficient.
                            </p>
                        </div>
                        <div data-reveal="zoom" class="bg-card-white rounded-2xl shadow-lg p-5 sm:p-8">
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
                <div data-reveal="up" class="text-white rounded-2xl sm:rounded-3xl p-8 sm:p-12 shadow-xl"
                    style="background: linear-gradient(135deg, #b11116 0%, #8A0D11 100%);">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-6 sm:mb-8 text-white">
                        Mission
                    </h2>
                    <p class="text-base sm:text-lg leading-7 sm:leading-8 text-white">
                        To provide efficient, compassionate, and accessible social welfare
                        services that improve the quality of life of every Silangueño through
                        people-centered programs, community participation, and sustainable
                        development.
                    </p>
                </div>
                <div data-reveal="up" class="bg-warm-gold rounded-2xl sm:rounded-3xl p-8 sm:p-12 shadow-xl">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 mb-6 sm:mb-8">
                        Vision
                    </h2>
                    <p class="text-base sm:text-lg leading-7 sm:leading-8 text-slate-900">
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
            <div data-reveal="fade" class="text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold">
                    How to Apply
                </h2>
                <p class="text-secondary mt-4">
                    Simple steps to request assistance.
                </p>
            </div>
            <div class="flex flex-wrap justify-center gap-4 sm:gap-6 lg:gap-8 mt-8 sm:mt-12 lg:mt-16">
                <div data-reveal="up"
                    class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-[#b11116] text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold shadow-md">
                        1
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Submit Request
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-[#b11116] -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div data-reveal="up"
                    class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-[#b11116] text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold shadow-md">
                        2
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Document Review
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-[#b11116] -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div data-reveal="up"
                    class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-[#b11116] text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold shadow-md">
                        3
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Assessment
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-[#b11116] -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div data-reveal="up"
                    class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-[#b11116] text-white flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold shadow-md">
                        4
                    </div>
                    <h3 class="font-bold text-xs sm:text-sm md:text-base mt-3 sm:mt-4 md:mt-6">
                        Approval
                    </h3>
                </div>
                <div class="hidden lg:flex items-center justify-center text-[#b11116] -mt-8 sm:-mt-10 md:-mt-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div data-reveal="up"
                    class="text-center flex-shrink-0 w-28 sm:w-32 md:w-36 lg:w-auto flex flex-col items-center">
                    <div
                        class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-full bg-warm-gold text-slate-900 flex items-center justify-center text-lg sm:text-xl md:text-2xl lg:text-3xl font-extrabold shadow-md">
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div data-reveal="fade" class="text-center mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                    Programs &amp; Activities in Action
                </h2>
                <p class="text-gray-600 mt-2 text-xs sm:text-sm md:text-base max-w-xl mx-auto px-2">
                    Highlights of BCPC activities, financial assistance, social case consultations, and child protection
                    meetings and seminars in Silang.
                </p>
            </div>

            <!-- Automatic Carousel Container -->
            <div data-reveal="zoom" id="simpleCarousel" class="programs-carousel">
                <!-- Slides -->
                @php
                    $carouselImages = [
                        [
                            'file' => 'bcpccollages.jpg',
                            'title' => 'BCPC Training & Community Child Protection Activities',
                        ],
                        ['file' => 'facollage.jpg', 'title' => 'Financial Assistance Intake & Cash Distribution'],
                        [
                            'file' => 'socialcaseCollage.jpg',
                            'title' => 'Social Case Study Interviews & Client Consultations',
                        ],
                        ['file' => '2024.png', 'title' => 'Child Protection Council Meetings & Cyber Safety Seminar'],
                    ];
                @endphp

                @foreach ($carouselImages as $index => $item)
                    <div class="carousel-slide absolute inset-0 w-full h-full"
                        style="transition: opacity 0.8s ease-in-out; opacity: {{ $index === 0 ? '1' : '0' }}; z-index: {{ $index === 0 ? '10' : '1' }}; pointer-events: {{ $index === 0 ? 'auto' : 'none' }};"
                        data-index="{{ $index }}">
                        <div class="absolute inset-0">
                            <img src="{{ asset('images/' . $item['file']) }}" alt="{{ $item['title'] }}"
                                class="w-full h-full object-cover object-center"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent pointer-events-none">
                        </div>
                        <!-- Overlay captions keep the image area full size. -->
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
                        <button type="button"
                            class="programs-carousel-dot carousel-dot {{ $index === 0 ? 'active' : '' }}"
                            data-index="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================== -->
    <!-- CONTACT SECTION -->
    <!-- ===================================== -->
    <section id="contact" class="contact-section">
        <div class="contact-container">
            <div data-reveal="fade" class="contact-header">
                <span class="contact-subtitle">Get In Touch</span>
                <h2 class="contact-title">Contact Us</h2>
                <p class="contact-description">
                    Find local service locations on the map and use the emergency hotlines to reach the support you
                    need.
                </p>
            </div>
            <div class="contact-grid">
                <!-- Local Services Map -->
                <div data-reveal="up" class="service-map-card" aria-labelledby="service-map-heading">
                    <div class="service-map-header">
                        <h3 id="service-map-heading" class="card-title">Find Local Services</h3>
                        <label for="serviceSelect" class="service-map-label">Select a service</label>
                        <select id="serviceSelect" class="service-map-select" aria-describedby="serviceMapStatus">
                            <option value="none">Select service</option>
                            <option value="hospital">Hospitals</option>
                            <option value="police">Police Stations</option>
                            <option value="barangay">Barangay Halls</option>
                            <option value="orphanage">Orphanages &amp; Children Homes</option>
                            <option value="municipal">Municipal Hall</option>
                        </select>
                        <p id="serviceMapStatus" class="service-map-status" role="status" aria-live="polite">
                            Choose a service to show its locations on the map.
                        </p>
                    </div>
                    <div id="mapContainer" class="service-map" role="region"
                        aria-label="Map of local services in Silang"></div>
                </div>
                <!-- Emergency Hotlines -->
                <div data-reveal="up" class="emergency-card">
                    <div class="emergency-header">
                        <div class="emergency-title-wrapper">
                            <h3 class="emergency-title">Emergency Hotlines</h3>
                        </div>
                        <span class="emergency-badge">Emergency 24/7</span>
                    </div>
                    <div id="emergencyGrid" class="emergency-grid">
                        <div class="hotline-item">
                            <span class="hotline-label text-warm-gold font-bold">Nationwide Emergency Hotline:</span>
                            <span class="hotline-value font-bold text-base">911</span>
                        </div>
                        <div class="hotline-item">
                            <span class="hotline-label">MDRRMO:</span>
                            <span class="hotline-value">0935-601-6738</span>
                        </div>
                        <div class="hotline-item">
                            <span class="hotline-label">MDRRMO:</span>
                            <span class="hotline-value">0922-384-6130</span>
                        </div>
                        <div class="hotline-item">
                            <span class="hotline-label">MDRRMO:</span>
                            <span class="hotline-value">(046) 414-3776</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">PNP:</span>
                            <span class="hotline-value">0995-987-8996</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">PNP:</span>
                            <span class="hotline-value">0998-598-5622</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">PNP:</span>
                            <span class="hotline-value">(046) 414-0204</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Central BFP:</span>
                            <span class="hotline-value">0945-559-5557</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Central BFP:</span>
                            <span class="hotline-value">(046) 414-0484</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Bulihan BFP Sub-Station:</span>
                            <span class="hotline-value">0930-430-8892</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Bulihan BFP Sub-Station:</span>
                            <span class="hotline-value">(046) 443-9457</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Inchican BFP Sub-Station:</span>
                            <span class="hotline-value">0956-399-2123</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Inchican BFP Sub-Station:</span>
                            <span class="hotline-value">(046) 420-9989</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Makabata Helpline:</span>
                            <span class="hotline-value">1383</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">Bantay Bata Hotline:</span>
                            <span class="hotline-value">163</span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">NCMH Mental Health:</span>
                            <span class="hotline-value text-right">
                                <span class="block">1553</span>
                                <span class="block">1-800-1888-1553</span>
                            </span>
                        </div>
                        <div class="hotline-item hidden-mobile">
                            <span class="hotline-label">DSWD Help:</span>
                            <span class="hotline-value">0932-933-3251</span>
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
    <footer class="text-white" style="background: linear-gradient(180deg, #8A0D11 0%, #68080B 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-10 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-12 sm:text-center lg:text-left">
                <div class="hidden sm:block lg:col-span-1">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">
                        MSWDO Silang
                    </h2>
                    <p
                        class="mt-3 lg:mt-5 max-w-2xl mx-auto lg:mx-0 lg:max-w-none text-white/80 leading-7 lg:leading-8 text-sm sm:text-base">
                        Municipal Social Welfare and Development Office committed to
                        providing quality, transparent, and compassionate public
                        service.
                    </p>
                </div>
                <div class="hidden sm:flex sm:flex-wrap sm:items-center sm:justify-center gap-x-8 gap-y-3 lg:block">
                    <h3 class="font-bold text-lg sm:text-xl lg:mb-5 text-white">
                        Quick Links
                    </h3>
                    <ul
                        class="flex flex-wrap justify-center gap-x-6 gap-y-3 lg:block lg:space-y-3 text-white/80 text-sm sm:text-base">
                        <li><a href="#home" class="hover:text-warm-gold transition">Home</a></li>
                        <li><a href="#services" class="hover:text-warm-gold transition">Services</a></li>
                        <li><a href="#about" class="hover:text-warm-gold transition">About</a></li>
                        <li><a href="#contact" class="hover:text-warm-gold transition">Contact</a></li>
                    </ul>
                </div>
                <div class="hidden lg:block">
                    <h3 class="font-bold text-lg sm:text-xl mb-4 sm:mb-5 text-white">
                        Programs
                    </h3>
                    <ul class="space-y-2 sm:space-y-3 text-white/80 text-sm sm:text-base">
                        <li>Financial Assistance</li>
                        <li>VAWC</li>
                        <li>BCPC</li>
                        <li>Senior Citizens</li>
                        <li>Solo Parent</li>
                        <li>Social Case Study</li>
                        <li>Person with Disabilities</li>
                    </ul>
                </div>
                <div
                    class="min-w-0 rounded-2xl border border-white/15 bg-white/5 p-5 sm:p-6 lg:rounded-none lg:border-0 lg:bg-transparent lg:p-0">
                    <h3 class="font-bold text-lg sm:text-xl mb-4 sm:mb-5 text-white">
                        Office Information
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-x-8 gap-y-5 text-sm leading-6">
                        <div class="min-w-0">
                            <dt class="text-warm-gold font-semibold">Address</dt>
                            <dd class="text-white/80">Municipal Hall, Silang, Cavite</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-warm-gold font-semibold">Phone</dt>
                            <dd>
                                <a href="tel:0464140202"
                                    class="inline-block py-1 text-white/80 hover:text-warm-gold transition">(046)
                                    414-0202</a>
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-warm-gold font-semibold">Email</dt>
                            <dd>
                                <a href="mailto:socialwelfaresilang@gmail.com"
                                    class="inline-block max-w-full py-1 text-white/80 hover:text-warm-gold transition [overflow-wrap:anywhere]">socialwelfaresilang@gmail.com</a>
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-warm-gold font-semibold">Office Hours</dt>
                            <dd class="text-white/80">Monday - Friday<br>8:00 AM – 5:00 PM</dd>
                        </div>
                    </dl>
                </div>
            </div>
            <div
                class="mt-8 sm:mt-12 lg:mt-16 flex justify-center items-center text-[10px] sm:text-xs sm:text-sm border-t border-white/10 pt-8">
                <p class="text-white/80 text-center">
                    © {{ date('Y') }} MSWDO Silang. All Rights Reserved.
                </p>
            </div>
        </div>
    </footer>
    <!-- ===================================== -->
    <!-- SCROLL TO TOP -->
    <!-- ===================================== -->
    <button id="scrollTop"
        class="hidden fixed bottom-6 right-6 bg-warm-gold text-slate-900 font-bold p-4 rounded-full shadow-xl hover:scale-110 transition z-40"
        aria-label="Scroll to top">
        ↑
    </button>
    <!-- ===================================== -->
    <!-- JAVASCRIPT -->
    <!-- ===================================== -->
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script
        src="{{ asset('js/contact-number-validation.js') }}?v={{ filemtime(public_path('js/contact-number-validation.js')) }}">
    </script>
</body>

</html>
