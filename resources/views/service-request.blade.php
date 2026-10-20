<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Social Case Study Application - MSWDO Silang</title>
    <meta name="description" content="Opisyal na Online Application Form para sa Social Case Study Report (SCSR) sa MSWDO Silang, Cavite.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/financial-assistance.css') }}">
    <style>
        /* SweetAlert2 modal */
        .swal2-popup {
            border-radius: 20px !important;
            padding: 1.75rem 1.5rem 1.5rem !important;
            font-family: inherit !important;
            box-shadow: 0 30px 60px rgba(15, 23, 42, .18) !important;
            background: #FFFFFF !important;
        }
        .swal2-title {
            color: #0F172A !important;
            font-weight: 700 !important;
            font-size: 1.3rem !important;
            padding: 0 !important;
        }
        .swal2-html-container {
            font-size: .9375rem !important;
            color: #64748B !important;
            line-height: 1.6 !important;
            margin: .5rem 0 0 !important;
        }
        .swal2-icon {
            margin: .5rem auto 1rem !important;
        }
        .swal2-confirm {
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-family: inherit !important;
            font-size: .875rem !important;
            padding: 12px 28px !important;
            transition: transform .2s ease, filter .2s ease !important;
        }
        .swal2-confirm:hover {
            filter: brightness(1.06) !important;
            transform: translateY(-1px) !important;
        }
        .swal2-actions {
            margin-top: 1.25rem !important;
        }
        .swal-err-intro {
            font-size: .9375rem;
            color: #64748B;
            margin: 0 0 .85rem;
            line-height: 1.5;
        }
        .swal-err-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: .4rem;
            text-align: left;
        }
        .swal-err-list li {
            position: relative;
            padding: .55rem .75rem .55rem 2.1rem;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 10px;
            font-size: .8125rem;
            font-weight: 500;
            color: #991B1B;
            line-height: 1.35;
        }
        .swal-err-list li::before {
            content: '';
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: #DC2626;
        }
        @media (max-width: 575.98px) {
            .swal2-popup {
                padding: 1.25rem 1rem 1rem !important;
                border-radius: 16px !important;
            }
            .swal2-title {
                font-size: 1.1rem !important;
            }
            .swal2-confirm {
                width: 100% !important;
            }
        }
    </style>
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <!-- ========================= -->
    <!-- NAVBAR -->
    <!-- ========================= -->
    <header class="sticky top-0 z-50 w-full shadow-sm" style="background: linear-gradient(135deg, #b11116 0%, #8A0D11 100%);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16 sm:h-18">
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
                <!-- Mobile: hamburger + brand text (left) ... logo (right) -->
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1 lg:hidden">
                    <button id="menuButton"
                        class="shrink-0 p-1.5 rounded-lg hover:bg-white/10 transition focus:outline-none"
                        aria-label="Toggle navigation" style="color:#fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-white font-bold text-sm tracking-tight leading-tight truncate">
                            iSERVE SILANG
                        </h1>
                        <p class="text-slate-200 text-[11px] leading-tight truncate">
                            Municipal Social Welfare &amp; Development Office
                        </p>
                    </div>
                </div>
                <div class="lg:hidden shrink-0">
                    <div class="h-9 w-9 rounded-full p-0.5 shrink-0 bg-white/10">
                        @if ($logo)
                            <img src="{{ asset('images/' . $logo) }}" alt="MSWDO Logo" class="rounded-full h-full w-full object-cover">
                        @endif
                    </div>
                </div>

                <!-- Desktop: logo & title (left) ... back nav (right) -->
                <a href="/" class="hidden lg:flex items-center gap-3 shrink-0 min-w-0">
                    <div class="h-12 w-12 rounded-full p-0.5 shrink-0 bg-white/10">
                        @if ($logo)
                            <img src="{{ asset('images/' . $logo) }}" alt="MSWDO Logo" class="rounded-full h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-white font-bold text-base tracking-tight leading-tight truncate">
                            iSERVE SILANG
                        </h1>
                        <p class="text-slate-200 text-[11px] leading-tight truncate">
                            Municipal Social Welfare &amp; Development Office
                        </p>
                    </div>
                </a>
                <!-- Desktop Back Navigation -->
                <nav class="hidden lg:flex items-center text-sm text-white/90 shrink-0">
                    <a href="/" class="hover:text-[#FBC02D] transition flex items-center gap-1.5 font-medium px-2.5 py-1.5 rounded hover:bg-white/10">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Back to Home</span>
                    </a>
                </nav>
            </div>
        </div>
        <!-- Mobile Dropdown Menu -->
        <div id="mobileMenu">
            <ul class="mobile-menu-list">
                <li>
                    <a href="/" class="active">
                        <i data-lucide="home" style="width:20px;height:20px"></i>
                        <span>Back to Home</span>
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <!-- ========================= -->
    <!-- MAIN CONTENT -->
    <!-- ========================= -->
    <main class="max-w-4xl mx-auto px-3 sm:px-6 py-6 sm:py-10 flex-1 w-full">

        <!-- Official Header / Heading -->
        <div class="text-center mb-6">
            <p class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 mb-0.5">
                Republika ng Pilipinas &bull; Bayan ng Silang, Cavite
            </p>
            <p class="text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
                TANGGAPAN NG KAGALINGANG PANLIPUNAN AT PAGPAPAUNLAD (MSWDO)
            </p>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#b11116] tracking-tight">
                SOCIAL CASE STUDY APPLICATION
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-xl mx-auto">
                Online Application Form para sa Social Case Study Report (SCSR)
            </p>
            <div class="mt-2.5 flex items-center justify-center gap-3 text-xs text-slate-500">
                <span>Petsa: <strong class="text-slate-700">{{ date('F d, Y') }}</strong></span>
            </div>
        </div>

        <!-- Notice Box -->
        <div class="bg-slate-50 border border-slate-200 rounded p-3 mb-6 text-xs text-slate-600 text-center leading-relaxed">
            <span class="font-bold text-slate-700">Paalala:</span>
            Punan nang wasto ang mga patlang na may pulang asterisk (<span class="text-rose-600 font-bold">*</span>).
            Siguraduhing malinaw at nababasa ang mga ilalakip na dokumento. Ang inyong kahilingan ay dadaan sa pagsusuri ng lisensyadong Social Worker ng MSWDO.
        </div>

        <!-- ===================================== -->
        <!-- INTAKE DOCUMENT FORM SHEET -->
        <!-- ===================================== -->
        <div class="intake-document">
            <form id="serviceRequestForm" method="POST" action="{{ route('service-request.store') }}" novalidate enctype="multipart/form-data">
                @csrf

                <!-- Section I: Sino ang Nangangailangan -->
                <div class="form-section">
                    <h2 class="section-heading">I. Para Kanino ang Kahilingan?</h2>
                    <p class="section-subtext">Client / Beneficiary — Piliin kung sino ang nangangailangan ng Social Case Study Report.</p>

                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 p-3 sm:p-3.5 rounded border border-slate-200 hover:border-[#b11116]/40 hover:bg-[#b11116]/5 cursor-pointer transition select-none">
                            <input type="radio" name="request_for" value="myself" required class="w-4 h-4 text-[#b11116] focus:ring-[#b11116] shrink-0">
                            <div>
                                <span class="block font-semibold text-slate-900 text-xs sm:text-sm">Sarili ko (Myself)</span>
                                <span class="block text-[11px] sm:text-xs text-slate-500">Ako ang mismong nangangailangan ng Social Case Study Report.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 sm:p-3.5 rounded border border-slate-200 hover:border-[#b11116]/40 hover:bg-[#b11116]/5 cursor-pointer transition select-none">
                            <input type="radio" name="request_for" value="child" class="w-4 h-4 text-[#b11116] focus:ring-[#b11116] shrink-0">
                            <div>
                                <span class="block font-semibold text-slate-900 text-xs sm:text-sm">Aking Anak (My Child)</span>
                                <span class="block text-[11px] sm:text-xs text-slate-500">Ang kahilingan ng Social Case Study ay para sa aking anak.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 sm:p-3.5 rounded border border-slate-200 hover:border-[#b11116]/40 hover:bg-[#b11116]/5 cursor-pointer transition select-none">
                            <input type="radio" name="request_for" value="parent" class="w-4 h-4 text-[#b11116] focus:ring-[#b11116] shrink-0">
                            <div>
                                <span class="block font-semibold text-slate-900 text-xs sm:text-sm">Aking Magulang (My Parent)</span>
                                <span class="block text-[11px] sm:text-xs text-slate-500">Ang kahilingan ay para sa aking magulang (tatay o nanay).</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 sm:p-3.5 rounded border border-slate-200 hover:border-[#b11116]/40 hover:bg-[#b11116]/5 cursor-pointer transition select-none">
                            <input type="radio" name="request_for" value="family" class="w-4 h-4 text-[#b11116] focus:ring-[#b11116] shrink-0">
                            <div>
                                <span class="block font-semibold text-slate-900 text-xs sm:text-sm">Ibang Miyembro ng Pamilya (Family Member)</span>
                                <span class="block text-[11px] sm:text-xs text-slate-500">Ang kahilingan ay para sa aming kasambahay o miyembro ng pamilya.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 sm:p-3.5 rounded border border-slate-200 hover:border-[#b11116]/40 hover:bg-[#b11116]/5 cursor-pointer transition select-none">
                            <input type="radio" name="request_for" value="assisting" class="w-4 h-4 text-[#b11116] focus:ring-[#b11116] shrink-0">
                            <div>
                                <span class="block font-semibold text-slate-900 text-xs sm:text-sm">Tinutulungang Kliyente / Kinatawan (Authorized Representative)</span>
                                <span class="block text-[11px] sm:text-xs text-slate-500">Ako ay nag-aasikaso sa ngalan ng ibang tao o pasyente.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section II: Impormasyon ng Benepisyaryo -->
                <div class="form-section">
                    <h2 class="section-heading">II. Impormasyon ng Benepisyaryo / Pasyente</h2>
                    <p class="section-subtext">Beneficiary Information — Ang taong nangangailangan ng Social Case Study Report.</p>

                    <!-- Name Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="firstName" class="form-label-custom">
                                        Unang Pangalan <span class="label-sub">(First Name)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="text" id="firstName" name="first_name" required placeholder="Hal. Juan" class="form-input-custom">
                            </div>
                        </div>
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="lastName" class="form-label-custom">
                                        Apelyido <span class="label-sub">(Last Name)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="text" id="lastName" name="last_name" required placeholder="Hal. Dela Cruz" class="form-input-custom">
                            </div>
                        </div>
                    </div>

                    <!-- DOB, Barangay Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="dob" class="form-label-custom">
                                        Petsa ng Kapanganakan <span class="label-sub">(Date of Birth)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="date" id="dob" name="dob" required class="form-input-custom">
                            </div>
                        </div>
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="barangay" class="form-label-custom">
                                        Barangay sa Silang <span class="required-star">*</span>
                                    </label>
                                </div>
                                <select id="barangay" name="barangay" required class="form-input-custom">
                                    <option value="">Pumili ng Barangay...</option>
                                    <option value="ACACIA">Acacia</option>
                                    <option value="ADLAS">Adlas</option>
                                    <option value="ANAHAW 1">Anahaw I</option>
                                    <option value="ANAHAW 2">Anahaw II</option>
                                    <option value="BALITE I">Balite I</option>
                                    <option value="BALITE II">Balite II</option>
                                    <option value="BALUBAD">Balubad</option>
                                    <option value="BANABA">Banaba</option>
                                    <option value="BATAS">Batas</option>
                                    <option value="BIGA 1">Biga I</option>
                                    <option value="BIGA 2">Biga II</option>
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
                                    <option value="IPIL 2">Ipil II</option>
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
                                    <option value="POBLACION 1">Poblacion I</option>
                                    <option value="POBLACION 2">Poblacion II</option>
                                    <option value="POBLACION 3">Poblacion III</option>
                                    <option value="POBLACION 4">Poblacion IV</option>
                                    <option value="POBLACION 5">Poblacion V</option>
                                    <option value="POOC I">Pooc I</option>
                                    <option value="POOC II">Pooc II</option>
                                    <option value="PULONG BUNGA">Pulong Bunga</option>
                                    <option value="PULONG SAGING">Pulong Saging</option>
                                    <option value="PUTING KAHOY">Puting Kahoy</option>
                                    <option value="SABUTAN">Sabutan</option>
                                    <option value="SAN MIGUEL I">San Miguel I</option>
                                    <option value="SAN MIGUEL II">San Miguel II</option>
                                    <option value="SAN VICENTE I">San Vicente I</option>
                                    <option value="SAN VICENTE II">San Vicente II</option>
                                    <option value="SANTOL">Santol</option>
                                    <option value="TARTARIA">Tartaria</option>
                                    <option value="TIBIG">Tibig</option>
                                    <option value="TOLEDO">Toledo</option>
                                    <option value="TUBUAN 1">Tubuan I</option>
                                    <option value="TUBUAN 2">Tubuan II</option>
                                    <option value="TUBUAN 3">Tubuan III</option>
                                    <option value="ULAT">Ulat</option>
                                    <option value="YAKAL">Yakal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact, Email Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="contactNumber" class="form-label-custom">
                                        Numero ng Kontak <span class="label-sub">(Contact Number)</span> <span class="required-star">*</span>
                                    </label>
                                    <span id="contactCounter" class="digit-counter text-slate-400">0/11 digits</span>
                                </div>
                                <input type="tel" id="contactNumber" name="contact_number" required placeholder="09XXXXXXXXX" maxlength="11" class="form-input-custom">
                                <span id="contactError" class="hidden text-xs text-rose-600 mt-1 block">Dapat eksaktong 11 digits (09XXXXXXXXX).</span>
                            </div>
                        </div>
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="email" class="form-label-custom">
                                        Email Address <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="email" id="email" name="email" required placeholder="Hal. juan@gmail.com" class="form-input-custom">
                            </div>
                        </div>
                    </div>

                    <!-- Address Row -->
                    <div class="grid grid-cols-1 gap-x-4 gap-y-4">
                        <div class="form-field-group">
                            <div class="form-label-wrapper">
                                <label for="address" class="form-label-custom">
                                    Kumpletong Tirahan <span class="label-sub">(Complete Address — House No., Street, Purok)</span>
                                </label>
                            </div>
                            <input type="text" id="address" name="address" placeholder="Hal. Blk 2 Lot 5, Purok 3" class="form-input-custom">
                        </div>
                    </div>
                </div>

                <!-- Section III: Mga Detalye ng Kahilingan -->
                <div class="form-section">
                    <h2 class="section-heading">III. Mga Detalye ng Social Case Study</h2>
                    <p class="section-subtext">Case Study Details — Uri ng serbisyo, layunin, at salaysay ng sitwasyon.</p>

                    <!-- Service Type (read-only display) + Assistance Type -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label class="form-label-custom">
                                        Uri ng Kahilingan <span class="label-sub">(Service Type)</span>
                                    </label>
                                </div>
                                <div class="form-input-custom bg-slate-50 flex items-center gap-2 text-slate-700 font-semibold cursor-not-allowed" style="display:flex;">
                                    <i data-lucide="file-check" class="w-4 h-4 text-[#b11116] shrink-0"></i>
                                    <span class="text-sm">Social Case Study Report (SCSR)</span>
                                </div>
                                <input type="hidden" id="serviceType" name="service_type" value="social_case_study">
                                <p class="text-[11px] text-slate-500 mt-1">Nakatakda para sa Social Case Study application.</p>
                            </div>
                        </div>
                        <div class="sm:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="assistanceType" class="form-label-custom">
                                        Layunin ng Case Study <span class="label-sub">(Purpose / Category)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <select id="assistanceType" name="assistance_type" required class="form-input-custom">
                                    <option value="">Pumili ng layunin ng Social Case Study...</option>
                                    <option value="medical">Tulong Medikal at Ospitalisasyon (Hospitalization &amp; Surgery)</option>
                                    <option value="burial">Tulong sa Burol at Libing (Burial &amp; Funeral Referral)</option>
                                    <option value="educational">Edukasyon at Pag-aaral (Educational Support Endorsement)</option>
                                    <option value="transportation">Pamasahe / Pagbiyahe (Transportation Referral)</option>
                                    <option value="food">Suporta sa Pagkain (Food Subsistence Endorsement)</option>
                                    <option value="emergency">Emerhensiya at Krisis (Emergency Crisis Assessment)</option>
                                    <option value="livelihood">Pangkabuhayan (Livelihood Assessment)</option>
                                    <option value="others">Iba Pang Layunin ng Case Study (Others)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Situation Narrative -->
                    <div class="mb-4 sm:mb-5">
                        <div class="form-label-wrapper">
                            <label for="situation" class="form-label-custom">
                                Salaysay ng Sitwasyon at Pangangailangan <span class="label-sub">(Situation &amp; Case Background)</span> <span class="required-star">*</span>
                            </label>
                            <span id="situationCounter" class="digit-counter text-slate-400">0 / 2000</span>
                        </div>
                        <textarea id="situation" name="situation" rows="5" required maxlength="2000"
                            placeholder="Hal. Humihiling po kami ng Social Case Study Report para sa Guarantee Letter (GL) sa Malasakit Center para sa hospital bill at gamot ng aking anak na naospital..."
                            class="form-input-custom resize-vertical" style="height:auto; min-height:120px;"></textarea>
                        <p class="text-[11px] text-slate-500 mt-1">Ilarawan nang maikli ang kasalukuyang kalagayan at kung saang ahensya o ospital isusumite ang Social Case Study Report.</p>
                    </div>
                </div>

                <!-- Section IV: Pag-upload ng mga Dokumento -->
                <div class="form-section">
                    <h2 class="section-heading">IV. Pag-upload ng mga Dokumento</h2>
                    <p class="section-subtext">Upload Supporting Documents — Mag-upload ng malinaw na kopya ng mga kinakailangang dokumento.</p>

                    <!-- Documentary Checklist -->
                    <div class="bg-slate-50 border border-slate-200 rounded p-3 mb-4 text-xs leading-relaxed">
                        <p class="font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                            <i data-lucide="paperclip" class="w-3.5 h-3.5 text-[#b11116]"></i>
                            Mga Karaniwang Kinakailangang Dokumento para sa Social Case Study Report:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-slate-600">
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3 h-3 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Valid Government ID ng Kliyente o Pasyente</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3 h-3 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Barangay Certificate of Indigency (para sa SCSR)</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3 h-3 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Medical Abstract / Hospital Statement of Account</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3 h-3 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Reseta ng Gamot, Laboratory Request, o Death Certificate</span>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Area -->
                    <div id="uploadArea" class="border-2 border-dashed border-[#b11116]/40 rounded p-6 sm:p-8 text-center bg-slate-50/70 cursor-pointer transition-all duration-200 hover:bg-[#FEF2F2] hover:border-[#b11116]">
                        <i data-lucide="upload-cloud" class="w-10 h-10 mx-auto text-[#b11116] mb-2.5"></i>
                        <p class="text-[#b11116] font-bold text-sm mb-1">Pindutin upang mag-upload ng files</p>
                        <p class="text-slate-600 text-xs">o i-drag at i-drop ang mga dokumento rito</p>
                        <p class="text-slate-500 text-[11px] mt-2">Tinatanggap na format: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                        <p class="text-slate-500 text-[11px]">Pinakamalaking sukat: 10MB bawat file</p>
                    </div>
                    <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden">

                    <!-- File List -->
                    <div id="fileList" class="mt-3 space-y-2"></div>
                </div>

                <!-- Submit Action Row -->
                <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 mt-6">
                    <a href="/" class="text-slate-600 hover:text-slate-900 font-semibold text-xs sm:text-sm flex items-center justify-center sm:justify-start gap-1.5 transition py-2 sm:py-0">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Kanselahin at Bumalik</span>
                    </a>

                    <button type="submit" id="submitBtn" class="action-submit-btn w-full sm:w-auto text-xs sm:text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Isumite ang Social Case Study Application</span>
                    </button>
                </div>

            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer style="background: linear-gradient(135deg, #b11116 0%, #8A0D11 100%);"
        class="py-6 text-center text-xs px-4 mt-8 text-white">
        <p>© {{ date('Y') }} Municipal Social Welfare and Development Office (MSWDO) &bull; Municipality of Silang, Cavite.</p>
    </footer>

    <!-- ===================================== -->
    <!-- MAHALAGANG PAUNAWA ADVISORY MODAL -->
    <!-- ===================================== -->
    <div id="applicantAdvisoryModal" class="advisory-modal" role="dialog" aria-modal="true"
        aria-labelledby="advisoryModalTitle">
        <div class="advisory-modal-content">
            <!-- Modal Header -->
            <div class="advisory-modal-header">
                <h3 id="advisoryModalTitle" class="advisory-modal-title">
                    PAUNAWA
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="advisory-modal-body space-y-3 sm:space-y-3.5">
                <p>
                    1. Punan nang wasto at kumpleto ang lahat ng kinakailangang impormasyon na may <span
                        class="text-rose-600 font-bold">pulang asterisk (*)</span>.
                </p>
                <p>
                    2. <strong class="text-amber-600 font-bold">6-Month Policy Restriction:</strong> Ang tulong
                    pinansyal ay may <strong class="text-amber-600 font-bold">6-month validity period</strong> bawat
                    benepisyaryo alinsunod sa patakaran ng MSWDO Silang.
                </p>
                <p>
                    3. Ihanda ang mga <strong class="text-amber-600 font-bold">sumusuportang dokumento</strong> (Valid
                    ID, Barangay Certificate of Indigency, Medical Abstract/Certificate o Hospital Bill) kapag pupunta
                    sa tanggapan.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="advisory-modal-footer">
                <button type="button" onclick="closeAdvisoryModal()"
                    class="advisory-btn-close w-full sm:w-auto text-center">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('applicantAdvisoryModal');
            if (!modal) return;

            window.closeAdvisoryModal = function () {
                modal.classList.remove('active');
                document.body.classList.remove('modal-open');
            };

            modal.addEventListener('click', function (e) {
                if (e.target === modal) window.closeAdvisoryModal();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') window.closeAdvisoryModal();
            });

            modal.classList.add('active');
            document.body.classList.add('modal-open');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        })();
    </script>

    <!-- Scripts -->
    <script>
        // Init Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // =====================================
        // MOBILE MENU TOGGLE (dropdown style) with hamburger ↔ X icon swap
        // =====================================
        (function () {
            const menuButton = document.getElementById('menuButton');
            const mobileMenu = document.getElementById('mobileMenu');
            const svg = menuButton ? menuButton.querySelector('svg') : null;
            const hamburgerPath = 'M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5';
            const closePath = 'M6 18L18 6M6 6l12 12';

            function updateIcon() {
                if (!svg) return;
                const isOpen = mobileMenu.classList.contains('show');
                svg.querySelector('path').setAttribute('d', isOpen ? closePath : hamburgerPath);
            }

            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', () => {
                    mobileMenu.classList.toggle('show');
                    const isOpen = mobileMenu.classList.contains('show');
                    document.body.classList.toggle('mobile-menu-open', isOpen);
                    updateIcon();
                });
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.remove('show');
                        document.body.classList.remove('mobile-menu-open');
                        updateIcon();
                    });
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        mobileMenu.classList.remove('show');
                        document.body.classList.remove('mobile-menu-open');
                        updateIcon();
                    }
                });
            }
        })();

        // =====================================
        // CONTACT NUMBER COUNTER & VALIDATION
        // =====================================
        (function () {
            const input = document.getElementById('contactNumber');
            const counter = document.getElementById('contactCounter');
            const error = document.getElementById('contactError');
            if (!input) return;

            function update() {
                input.value = input.value.replace(/[^0-9]/g, '').slice(0, 11);
                const len = input.value.length;
                if (counter) {
                    counter.textContent = `${len}/11 digits`;
                    if (len === 11) {
                        counter.className = 'digit-counter valid';
                        if (error) error.classList.add('hidden');
                    } else if (len > 0) {
                        counter.className = 'digit-counter invalid';
                        if (error) error.classList.remove('hidden');
                    } else {
                        counter.className = 'digit-counter text-slate-400';
                        if (error) error.classList.add('hidden');
                    }
                }
            }
            input.addEventListener('input', update);
            update();
        })();

        // =====================================
        // NAME VALIDATION (letters & spaces only)
        // =====================================
        (function () {
            function attach(id) {
                const input = document.getElementById(id);
                if (!input) return;

                function sanitize() {
                    input.value = input.value.replace(/[^\p{L}\s]/gu, '');
                }
                input.addEventListener('input', sanitize);
                sanitize();
            }
            attach('firstName');
            attach('lastName');
        })();

        // =====================================
        // SITUATION COUNTER
        // =====================================
        (function () {
            const textarea = document.getElementById('situation');
            const counter = document.getElementById('situationCounter');
            if (!textarea || !counter) return;

            function update() {
                const len = textarea.value.length;
                counter.textContent = `${len} / 2000`;
                if (len >= 1800) {
                    counter.className = 'digit-counter invalid';
                } else {
                    counter.className = 'digit-counter text-slate-400';
                }
            }
            textarea.addEventListener('input', update);
            update();
        })();

        // =====================================
        // FILE UPLOAD HANDLING
        // =====================================
        let selectedFiles = [];

        const uploadArea = document.getElementById('uploadArea');
        const documentsInput = document.getElementById('documents');
        const fileList = document.getElementById('fileList');

        uploadArea.addEventListener('click', () => {
            documentsInput.click();
        });

        documentsInput.addEventListener('change', (e) => {
            handleFileSelection(e.target.files);
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('bg-[#FEF2F2]', 'border-[#b11116]');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('bg-[#FEF2F2]', 'border-[#b11116]');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('bg-[#FEF2F2]', 'border-[#b11116]');
            handleFileSelection(e.dataTransfer.files);
        });

        function handleFileSelection(files) {
            const validTypes = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const extension = '.' + file.name.split('.').pop().toLowerCase();

                if (!validTypes.includes(extension)) {
                    Swal.fire({
                        title: 'Hindi Tinatanggap na Format',
                        text: `Hindi suportado ang uri ng "${file.name}". Pakilakip lamang ang PDF, DOC, DOCX, JPG, JPEG, o PNG.`,
                        icon: 'error',
                        confirmButtonColor: '#b11116'
                    });
                    continue;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'Masyadong Malaki ang File',
                        text: `Ang "${file.name}" ay lagpas sa pinahihintulutang 10MB na sukat.`,
                        icon: 'error',
                        confirmButtonColor: '#b11116'
                    });
                    continue;
                }

                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    selectedFiles.push(file);
                }
            }

            updateFileList();
            syncFileInput();
        }

        function updateFileList() {
            fileList.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded text-xs';
                fileItem.innerHTML = `
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="file" class="w-4 h-4 text-[#b11116] shrink-0"></i>
                        <span class="text-slate-700 truncate font-medium">${file.name}</span>
                        <span class="text-slate-500 shrink-0">(${(file.size / 1024).toFixed(1)} KB)</span>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-rose-500 hover:text-rose-700 shrink-0 ml-2" title="Alisin">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                `;
                fileList.appendChild(fileItem);
            });
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileList();
            syncFileInput();
        }

        function syncFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            documentsInput.files = dataTransfer.files;
        }

        // =====================================
        // FORM SUBMISSION
        // =====================================
        document.getElementById('serviceRequestForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Isinusumite ang aplikasyon...</span>
                `;
            }

            const formData = new FormData(this);
            formData.delete('documents[]');
            for (let i = 0; i < selectedFiles.length; i++) {
                formData.append('documents[]', selectedFiles[i]);
            }

            fetch('/service-request', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                let data = null;
                try { data = await response.json(); } catch (e) { data = null; }

                if (response.ok && data && data.success) {
                    Swal.fire({
                        title: 'Aplikasyon Naisumite!',
                        text: 'Matagumpay na naisumite ang inyong aplikasyon para sa Social Case Study Report (SCSR). Susuriin ito ng MSWDO Social Worker para sa case evaluation.',
                        icon: 'success',
                        confirmButtonColor: '#b11116',
                        confirmButtonText: 'Bumalik sa Home'
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    const fieldLabels = {
                        request_for: 'Para kanino ang kahilingan',
                        first_name: 'Pangalan',
                        last_name: 'Apelyido',
                        dob: 'Petsa ng kapanganakan',
                        barangay: 'Barangay',
                        contact_number: 'Numero ng telepono',
                        email: 'Email',
                        address: 'Tirahan',
                        service_type: 'Uri ng serbisyo',
                        assistance_type: 'Uri ng tulong',
                        situation: 'Paglalahad ng sitwasyon',
                        documents: 'Mga dokumento'
                    };
                    let errorHtml = '';
                    if (data && data.errors && typeof data.errors === 'object') {
                        const items = Object.keys(data.errors).map(key => {
                            const label = fieldLabels[key] || key.replace(/_/g, ' ');
                            return `<li>${label}</li>`;
                        });
                        if (items.length) {
                            errorHtml = `<p class="swal-err-intro">Pakipunan ang mga sumusunod na kinakailangang impormasyon:</p><ul class="swal-err-list">${items.join('')}</ul>`;
                        }
                    }
                    if (errorHtml) {
                        Swal.fire({
                            title: 'Hindi Naisumite',
                            html: errorHtml,
                            icon: 'error',
                            confirmButtonColor: '#DC2626',
                            confirmButtonText: 'Subukang Muli'
                        });
                    } else {
                        const message = (data && data.message) ? data.message : 'Nagkaroon ng problema sa pagsumite ng inyong aplikasyon. Pakisubukang muli.';
                        Swal.fire({
                            title: 'Hindi Naisumite',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#DC2626',
                            confirmButtonText: 'Subukang Muli'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Hindi Naisumite',
                    text: 'Nagkaroon ng problema sa koneksyon o pagsumite ng form. Pakisubukang muli.',
                    icon: 'error',
                    confirmButtonColor: '#DC2626',
                    confirmButtonText: 'Subukang Muli'
                });
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            });
        });
    </script>

<script src="{{ asset('js/contact-number-validation.js') }}?v={{ filemtime(public_path('js/contact-number-validation.js')) }}"></script>
</body>

</html>
