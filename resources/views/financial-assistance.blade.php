<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financial Assistance Intake Sheet - MSWDO Silang</title>
    <meta name="description" content="Municipal Social Welfare and Development Office - Financial Assistance Online Intake (Step 1)">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/financial-assistance.css') }}">
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <!-- ========================= -->
    <!-- NAVBAR -->
    <!-- ========================= -->
    <header class="sticky top-0 z-50 w-full bg-[#1A237E] shadow-sm">
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
                <nav class="hidden lg:flex items-center text-sm text-slate-100 shrink-0">
                    <a href="/" class="hover:text-white transition flex items-center gap-1.5 font-medium px-2.5 py-1.5 rounded hover:bg-white/10">
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
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#1A237E] tracking-tight">
                GENERAL INTAKE SHEET (STEP 1)
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-xl mx-auto">
                Opisyal na Form sa Paghingi ng Tulong Pinansyal (Financial Assistance Application Record)
            </p>
            <div class="mt-2.5 flex items-center justify-center gap-3 text-xs text-slate-500">
                <span>Petsa: <strong class="text-slate-700">{{ date('F d, Y') }}</strong></span>
                
               
            </div>
        </div>

        <!-- Official Paunawa Notice Box -->
        <div class="bg-slate-50 border border-slate-200 rounded p-3 mb-6 text-xs text-slate-600 text-center leading-relaxed">
            <span class="font-bold text-slate-700">Paunawa:</span> Punan nang wasto ang mga patlang na may pulang asterisk (<span class="text-rose-600 font-bold">*</span>). Ang tulong pinansyal ay may 6-month validity period bawat benepisyaryo.
        </div>

        <!-- Real-time 6-Month Duplicate Restriction Warning (Initially Hidden) -->
        <div id="duplicateAlertBox" class="hidden mb-6 bg-red-50 border border-red-200 rounded p-3.5 sm:p-4 text-slate-800">
            <div class="flex items-start gap-2.5 sm:gap-3">
                <div class="text-red-700 shrink-0 mt-0.5">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 min-w-0 space-y-1.5 text-xs sm:text-sm">
                    <!-- 1. Not Yet Eligible Status -->
                    <div class="font-bold text-red-900">
                        Hindi Pa Kwalipikado sa Kasalukuyan (Not Yet Eligible to Apply)
                    </div>

                    <!-- 2. Reason: 6-Month Policy Restriction -->
                    <div class="text-slate-700 text-xs leading-normal">
                        <span class="font-semibold text-slate-900">Dahilan (Reason):</span>
                        <span>6-Month Policy Restriction — Ang tulong pinansyal ay may 6-month validity period bawat benepisyaryo alinsunod sa patakaran ng MSWDO Silang.</span>
                    </div>

                    <!-- 3. When to Apply Again -->
                    <div class="pt-1.5 border-t border-red-200/80 text-xs flex flex-wrap items-center gap-1.5">
                        <span class="font-semibold text-slate-900">Kailan Maaaring Mag-apply Ulit (When to Apply Again):</span>
                        <span id="duplicateEligibleDate" class="font-bold text-red-800 bg-red-100/80 px-2 py-0.5 rounded border border-red-200">
                            Sinusuri...
                        </span>
                    </div>

                    <!-- Hidden storage for raw message text consumed by scripts -->
                    <p id="duplicateAlertMessage" class="hidden"></p>
                </div>
            </div>
        </div>

        <!-- ===================================== -->
        <!-- INTAKE DOCUMENT FORM SHEET -->
        <!-- ===================================== -->
        <div class="intake-document">
            <form id="financialIntakeForm" method="POST" action="{{ route('financial-assistance.store') }}" novalidate>
                @csrf
                <input type="hidden" name="beneficiary_city" value="SILANG">
                <input type="hidden" name="beneficiary_province" value="CAVITE">
                <input type="hidden" name="beneficiary_region" value="REGION IV-A">
                <input type="hidden" name="rep_city" value="SILANG">
                <input type="hidden" name="rep_province" value="CAVITE">
                <input type="hidden" name="rep_region" value="REGION IV-A">

                <!-- Katayuan ng Aplikasyon (Client Status) -->
                <div class="form-section">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Katayuan ng Aplikasyon</span>
                            <p class="text-xs sm:text-sm font-bold text-slate-800">Uri ng Kliyente (Client Status) <span class="required-star">*</span></p>
                        </div>
                        <div class="flex items-center gap-5 sm:gap-6 pt-1 sm:pt-0">
                            <label class="inline-flex items-center gap-2 cursor-pointer py-1 text-xs sm:text-sm text-slate-700">
                                <input type="radio" name="client_type" value="New" class="w-4 h-4 text-[#1A237E] focus:ring-[#1A237E]" checked>
                                <span class="font-medium">Bagong Kliyente (New)</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer py-1 text-xs sm:text-sm text-slate-700">
                                <input type="radio" name="client_type" value="Returning" class="w-4 h-4 text-[#1A237E] focus:ring-[#1A237E]">
                                <span class="font-medium">Dating Kliyente (Returning)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section I: Impormasyon ng Benepisyaryo -->
                <div class="form-section">
                    <h2 class="section-heading">I. Impormasyon ng Benepisyaryo / Pasyente</h2>
                    <p class="section-subtext">Beneficiary Identifying Information (Ang taong nangangailangan ng tulong)</p>

                    <!-- Name Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6 lg:col-span-3">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_last_name" class="form-label-custom">
                                        Apelyido <span class="label-sub">(Last Name)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="text" id="beneficiary_last_name" name="beneficiary_last_name" required placeholder="Hal. DELA CRUZ" class="form-input-custom uppercase">
                            </div>
                        </div>
                        <div class="sm:col-span-6 lg:col-span-3">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_first_name" class="form-label-custom">
                                        Unang Pangalan <span class="label-sub">(First Name)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="text" id="beneficiary_first_name" name="beneficiary_first_name" required placeholder="Hal. JUAN" class="form-input-custom uppercase">
                            </div>
                        </div>
                        <div class="sm:col-span-6 lg:col-span-3">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_middle_name" class="form-label-custom">
                                        Gitnang Pangalan <span class="label-sub">(Middle Name)</span>
                                    </label>
                                </div>
                                <input type="text" id="beneficiary_middle_name" name="beneficiary_middle_name" placeholder="Hal. SANTOS" class="form-input-custom uppercase">
                            </div>
                        </div>
                        <div class="sm:col-span-6 lg:col-span-3">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_extension_name" class="form-label-custom">
                                        Ext. <span class="label-sub">(Jr., Sr., III)</span>
                                    </label>
                                </div>
                                <select id="beneficiary_extension_name" name="beneficiary_extension_name" class="form-input-custom">
                                    <option value="">Wala (None)</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Address Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6 lg:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_street_address" class="form-label-custom">
                                        Tirahan <span class="label-sub">(House No. / Street / Purok)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="text" id="beneficiary_street_address" name="beneficiary_street_address" required placeholder="Hal. Blk 2 Lot 5, Purok 3" class="form-input-custom">
                            </div>
                        </div>
                        <div class="sm:col-span-6 lg:col-span-6">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_barangay" class="form-label-custom">
                                        Barangay <span class="required-star">*</span>
                                    </label>
                                </div>
                                <select id="beneficiary_barangay" name="beneficiary_barangay" required class="form-input-custom">
                                    <option value="">Pumili ng Barangay...</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact, Birthday, Age, Gender -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                        <div class="sm:col-span-6 lg:col-span-4">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_contact_number" class="form-label-custom">
                                        Numero ng Telepono <span class="label-sub">(Mobile No.)</span> <span class="required-star">*</span>
                                    </label>
                                    <span id="beneficiary_contact_counter" class="digit-counter text-slate-400">0/11 digits</span>
                                </div>
                                <input type="tel" id="beneficiary_contact_number" name="beneficiary_contact_number" required placeholder="09XXXXXXXXX" maxlength="11" class="form-input-custom">
                                <span id="beneficiary_contact_error" class="hidden text-xs text-rose-600 mt-1 block">Dapat eksaktong 11 digits (09XXXXXXXXX).</span>
                            </div>
                        </div>

                        <div class="sm:col-span-6 lg:col-span-3">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_birthday" class="form-label-custom">
                                        Kapanganakan <span class="label-sub">(Date of Birth)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="date" id="beneficiary_birthday" name="beneficiary_birthday" required class="form-input-custom">
                            </div>
                        </div>

                        <div class="sm:col-span-4 lg:col-span-2">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_age" class="form-label-custom">
                                        Edad <span class="label-sub">(Age)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <input type="number" id="beneficiary_age" name="beneficiary_age" readonly required placeholder="Edad" class="form-input-custom bg-slate-50 text-center font-bold">
                            </div>
                        </div>

                        <div class="sm:col-span-8 lg:col-span-3">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_sex" class="form-label-custom">
                                        Kasarian <span class="label-sub">(Gender)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <select id="beneficiary_sex" name="beneficiary_sex" required class="form-input-custom">
                                    <option value="">Pumili ng Kasarian...</option>
                                    <option value="Male">Lalaki (Male)</option>
                                    <option value="Female">Babae (Female)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Civil Status, Trabaho, Buwanang Kita -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4">
                        <div class="sm:col-span-4 lg:col-span-4">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_civil_status" class="form-label-custom">
                                        Katayuang Sibil <span class="label-sub">(Civil Status)</span> <span class="required-star">*</span>
                                    </label>
                                </div>
                                <select id="beneficiary_civil_status" name="beneficiary_civil_status" required class="form-input-custom">
                                    <option value="">Pumili...</option>
                                    <option value="Single">Single (Walang Asawa)</option>
                                    <option value="Married">Married (Kasal)</option>
                                    <option value="Widowed">Widowed (Biyudo / Biyuda)</option>
                                    <option value="Separated">Separated (Hiwalay)</option>
                                    <option value="Cohabiting">Cohabiting (Nagsasama)</option>
                                </select>
                            </div>
                        </div>
                        <div class="sm:col-span-4 lg:col-span-4">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_occupation" class="form-label-custom">
                                        Trabaho <span class="label-sub">(Occupation)</span>
                                    </label>
                                </div>
                                <input type="text" id="beneficiary_occupation" name="beneficiary_occupation" placeholder="Hal. Vendor, Magsasaka, N/A" class="form-input-custom">
                            </div>
                        </div>
                        <div class="sm:col-span-4 lg:col-span-4">
                            <div class="form-field-group">
                                <div class="form-label-wrapper">
                                    <label for="beneficiary_monthly_salary" class="form-label-custom">
                                        Buwanang Kita <span class="label-sub">(Monthly Salary)</span>
                                    </label>
                                </div>
                                <input type="number" step="0.01" min="0" id="beneficiary_monthly_salary" name="beneficiary_monthly_salary" placeholder="0.00" class="form-input-custom">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section II: Impormasyon ng Kinatawan -->
                <div class="form-section">
                    <h2 class="section-heading">II. Impormasyon ng Kinatawan (Kung Mayroon)</h2>
                    <p class="section-subtext">Representative Information (Kinatawang nag-aayos sa ngalan ng pasyente)</p>

                    <!-- Representative Switch Container -->
                    <div id="repSwitchContainer" class="rep-toggle-box">
                        <label class="flex items-start sm:items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="has_representative" id="has_representative" value="1" class="mt-0.5 sm:mt-0 w-4 h-4 text-[#1A237E] rounded border-slate-300 focus:ring-[#1A237E]" onchange="toggleRepresentative(this.checked)">
                            <div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800">May kinatawan ba na nagfa-file para sa benepisyaryo?</span>
                                <p class="text-[11px] sm:text-xs text-slate-500">I-tsek kung ibang tao (anak, asawa, magulang, kamag-anak) ang naglalakad ng tulong.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Representative Form Fields (Toggled) -->
                    <div id="representativeCard" class="hidden space-y-4 pt-1">
                        <!-- Relationship to Beneficiary -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                            <div class="sm:col-span-6 lg:col-span-4">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_relationship" class="form-label-custom">
                                            Relasyon sa Benepisyaryo <span class="label-sub">(Relationship)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <select id="rep_relationship" name="rep_relationship" class="form-input-custom rep-field" data-required-if-rep="true">
                                        <option value="">Pumili ng Relasyon...</option>
                                        @foreach($relationships as $rel)
                                            <option value="{{ $rel }}">{{ $rel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Rep Names -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                            <div class="sm:col-span-6 lg:col-span-3">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_last_name" class="form-label-custom">
                                            Apelyido <span class="label-sub">(Last Name)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <input type="text" id="rep_last_name" name="rep_last_name" placeholder="Hal. DELA CRUZ" class="form-input-custom rep-field uppercase" data-required-if-rep="true">
                                </div>
                            </div>
                            <div class="sm:col-span-6 lg:col-span-3">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_first_name" class="form-label-custom">
                                            Unang Pangalan <span class="label-sub">(First Name)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <input type="text" id="rep_first_name" name="rep_first_name" placeholder="Hal. MARIA" class="form-input-custom rep-field uppercase" data-required-if-rep="true">
                                </div>
                            </div>
                            <div class="sm:col-span-6 lg:col-span-3">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_middle_name" class="form-label-custom">
                                            Gitnang Pangalan <span class="label-sub">(Middle Name)</span>
                                        </label>
                                    </div>
                                    <input type="text" id="rep_middle_name" name="rep_middle_name" placeholder="Hal. REYES" class="form-input-custom rep-field uppercase">
                                </div>
                            </div>
                            <div class="sm:col-span-6 lg:col-span-3">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_extension_name" class="form-label-custom">
                                            Ext. <span class="label-sub">(Jr., Sr.)</span>
                                        </label>
                                    </div>
                                    <select id="rep_extension_name" name="rep_extension_name" class="form-input-custom rep-field">
                                        <option value="">Wala (None)</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Rep Address -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                            <div class="sm:col-span-6 lg:col-span-6">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_street_address" class="form-label-custom">
                                            Tirahan ng Kinatawan <span class="label-sub">(Street / House No.)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <input type="text" id="rep_street_address" name="rep_street_address" placeholder="Hal. Purok 4" class="form-input-custom rep-field" data-required-if-rep="true">
                                </div>
                            </div>
                            <div class="sm:col-span-6 lg:col-span-6">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_barangay" class="form-label-custom">
                                            Barangay <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <select id="rep_barangay" name="rep_barangay" class="form-input-custom rep-field" data-required-if-rep="true">
                                        <option value="">Pumili ng Barangay...</option>
                                        @foreach($barangays as $brgy)
                                            <option value="{{ $brgy }}">{{ $brgy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Rep Contact, Birthday, Age, Gender -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4 mb-4">
                            <div class="sm:col-span-6 lg:col-span-4">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_contact_number" class="form-label-custom">
                                            Numero ng Telepono <span class="label-sub">(Mobile No.)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                        <span id="rep_contact_counter" class="digit-counter text-slate-400">0/11 digits</span>
                                    </div>
                                    <input type="tel" id="rep_contact_number" name="rep_contact_number" placeholder="09XXXXXXXXX" maxlength="11" class="form-input-custom rep-field" data-required-if-rep="true">
                                    <span id="rep_contact_error" class="hidden text-xs text-rose-600 mt-1 block">Dapat eksaktong 11 digits (09XXXXXXXXX).</span>
                                </div>
                            </div>
                            <div class="sm:col-span-6 lg:col-span-3">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_birthday" class="form-label-custom">
                                            Kapanganakan <span class="label-sub">(Date of Birth)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <input type="date" id="rep_birthday" name="rep_birthday" class="form-input-custom rep-field" data-required-if-rep="true">
                                </div>
                            </div>
                            <div class="sm:col-span-4 lg:col-span-2">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_age" class="form-label-custom">
                                            Edad <span class="label-sub">(Age)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <input type="number" id="rep_age" name="rep_age" readonly placeholder="Edad" class="form-input-custom rep-field bg-slate-50 text-center font-bold" data-required-if-rep="true">
                                </div>
                            </div>
                            <div class="sm:col-span-8 lg:col-span-3">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_sex" class="form-label-custom">
                                            Kasarian <span class="label-sub">(Gender)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <select id="rep_sex" name="rep_sex" class="form-input-custom rep-field" data-required-if-rep="true">
                                        <option value="">Pumili ng Kasarian...</option>
                                        <option value="Male">Lalaki (Male)</option>
                                        <option value="Female">Babae (Female)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Rep Civil Status, Trabaho, Buwanang Kita -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-4">
                            <div class="sm:col-span-4 lg:col-span-4">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_civil_status" class="form-label-custom">
                                            Katayuang Sibil <span class="label-sub">(Civil Status)</span> <span class="required-star rep-star">*</span>
                                        </label>
                                    </div>
                                    <select id="rep_civil_status" name="rep_civil_status" class="form-input-custom rep-field" data-required-if-rep="true">
                                        <option value="">Pumili...</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-4 lg:col-span-4">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_occupation" class="form-label-custom">
                                            Trabaho <span class="label-sub">(Occupation)</span>
                                        </label>
                                    </div>
                                    <input type="text" id="rep_occupation" name="rep_occupation" placeholder="Trabaho ng Kinatawan" class="form-input-custom rep-field">
                                </div>
                            </div>
                            <div class="sm:col-span-4 lg:col-span-4">
                                <div class="form-field-group">
                                    <div class="form-label-wrapper">
                                        <label for="rep_monthly_salary" class="form-label-custom">
                                            Buwanang Kita <span class="label-sub">(Monthly Salary)</span>
                                        </label>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="rep_monthly_salary" name="rep_monthly_salary" placeholder="0.00" class="form-input-custom rep-field">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section III: Komposisyon ng Pamilya -->
                <div class="form-section">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-1">
                        <div>
                            <h2 class="section-heading mb-0 border-b-0 pb-0">III. Komposisyon ng Pamilya</h2>
                            <p class="section-subtext mb-0">Family Composition (Ilista ang mga kasamahan sa tahanan / pamilya)</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 mb-3.5 leading-relaxed">
                        Ilista ang impormasyon ng bawat miyembro ng pamilya o kasamahan sa bahay. I-click ang <strong>“+ Magdagdag ng Miyembro”</strong> upang magdagdag, o ang <strong>“Alisin”</strong> kung nais magbawas. Kung mag-isa lamang sa tahanan, maaaring iwanang walang laman.
                    </p>

                    <!-- Family Member Form Blocks Container -->
                    <div id="familyMembersContainer" class="space-y-3 sm:space-y-3.5">
                        <!-- Empty State Notice (shown only if all cards are removed) -->
                        <div id="familyEmptyState" class="hidden text-center py-5 px-4 border border-dashed border-slate-300 rounded bg-slate-50/50">
                            <i data-lucide="users" class="w-6 h-6 text-slate-400 mx-auto mb-1.5"></i>
                            <p class="text-xs font-semibold text-slate-700">Walang nakatalang miyembro ng pamilya</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Kung may nais idagdag na kasambahay o pamilya, i-click ang button sa ibaba.</p>
                        </div>

                        <!-- Member Block 1 -->
                        <div class="family-member-card" data-index="0">
                            <div class="family-member-header">
                                <div class="flex items-center gap-2">
                                    
                                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">(Kasamahan sa Tahanan)</span>
                                </div>
                                <button type="button" class="family-remove-btn" onclick="removeFamilyMember(this)" title="Alisin ang miyembrong ito">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Alisin</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-3">
                                <!-- Full Name -->
                                <div class="col-span-12 sm:col-span-6">
                                    <div class="form-field-group">
                                        <div class="form-label-wrapper-compact">
                                            <label class="form-label-custom">
                                                Buong Pangalan <span class="label-sub">(Full Name)</span>
                                            </label>
                                        </div>
                                        <input type="text" name="family_composition[0][name]" placeholder="Hal. Juan Dela Cruz Jr." class="form-input-custom uppercase">
                                    </div>
                                </div>

                                <!-- Relationship -->
                                <div class="col-span-12 sm:col-span-6">
                                    <div class="form-field-group">
                                        <div class="form-label-wrapper-compact">
                                            <label class="form-label-custom">
                                                Relasyon sa Benepisyaryo <span class="label-sub">(Relationship)</span>
                                            </label>
                                        </div>
                                        <input type="text" name="family_composition[0][relationship]" placeholder="Hal. Asawa, Anak, Magulang" class="form-input-custom">
                                    </div>
                                </div>

                                <!-- Age -->
                                <div class="col-span-12 sm:col-span-3">
                                    <div class="form-field-group">
                                        <div class="form-label-wrapper-compact">
                                            <label class="form-label-custom">
                                                Edad <span class="label-sub">(Age)</span>
                                            </label>
                                        </div>
                                        <input type="number" min="0" max="120" name="family_composition[0][age]" placeholder="Edad" class="form-input-custom text-center">
                                    </div>
                                </div>

                                <!-- Occupation -->
                                <div class="col-span-12 sm:col-span-5">
                                    <div class="form-field-group">
                                        <div class="form-label-wrapper-compact">
                                            <label class="form-label-custom">
                                                Trabaho <span class="label-sub">(Occupation)</span>
                                            </label>
                                        </div>
                                        <input type="text" name="family_composition[0][occupation]" placeholder="Hal. Magsasaka, Vendor, Wala" class="form-input-custom">
                                    </div>
                                </div>

                                <!-- Monthly Income -->
                                <div class="col-span-12 sm:col-span-4">
                                    <div class="form-field-group">
                                        <div class="form-label-wrapper-compact">
                                            <label class="form-label-custom">
                                                Buwanang Kita <span class="label-sub">(Monthly Income)</span>
                                            </label>
                                        </div>
                                        <input type="number" step="0.01" min="0" name="family_composition[0][salary]" placeholder="0.00" class="form-input-custom">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Family Member Action -->
                    <button type="button" class="family-add-btn" onclick="addFamilyMember()">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Magdagdag ng Miyembro ng Pamilya (+ Add Member)</span>
                    </button>
                </div>

                <!-- Section IV: Layunin ng Tulong at Kategorya -->
                <div class="form-section">
                    <h2 class="section-heading">IV. Layunin ng Tulong at Kategorya</h2>
                    <p class="section-subtext">Assistance Purpose / Medical Condition &amp; Beneficiary Category</p>

                    <!-- Assistance Purpose / Condition -->
                    <div class="mb-4 sm:mb-5">
                        <label for="assistance_purpose" class="form-label-custom">
                            Layunin ng Tulong / Medical Condition <span class="required-star">*</span>
                        </label>
                        <select id="assistance_purpose" name="assistance_purpose" required class="form-input-custom" onchange="togglePurposeOther()">
                            <option value="">Pumili ng Layunin ng Tulong / Kondisyong Medikal...</option>
                            @foreach($medicalConditions as $cond)
                                <option value="{{ $cond }}">{{ $cond }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="purpose_other_input" name="purpose_other" placeholder="Pakilahad ang partikular na karamdaman o detalye..." class="form-input-custom mt-2 hidden">
                    </div>

                    <!-- Beneficiary Category Checkboxes -->
                    <div class="mb-4 sm:mb-5">
                        <label class="form-label-custom mb-1.5">
                            Kategorya ng Benepisyaryo (Beneficiary Category - Pumili ng naaangkop)
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($categories as $cat)
                                @if($cat !== 'Others')
                                    <label class="category-checkbox-item">
                                        <input type="checkbox" name="beneficiary_categories[]" value="{{ $cat }}" class="w-4 h-4 rounded text-[#1A237E] border-slate-300 focus:ring-[#1A237E]">
                                        <span>{{ $cat }}</span>
                                    </label>
                                @endif
                            @endforeach
                            <label class="category-checkbox-item">
                                <input type="checkbox" name="beneficiary_categories[]" value="Others" id="cat_others" class="w-4 h-4 rounded text-[#1A237E] border-slate-300 focus:ring-[#1A237E]" onchange="toggleCategoryOther()">
                                <span>Iba pa (Others)</span>
                            </label>
                        </div>
                        <input type="text" id="beneficiary_category_other_input" name="beneficiary_category_other" placeholder="Pakitukoy ang ibang kategorya..." class="form-input-custom mt-2 hidden">
                    </div>
                </div>

                <!-- Submit Action Row -->
                <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 mt-6">
                    <a href="/" class="text-slate-600 hover:text-slate-900 font-semibold text-xs sm:text-sm flex items-center justify-center sm:justify-start gap-1.5 transition py-2 sm:py-0">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Kanselahin at Bumalik</span>
                    </a>

                    <button type="submit" id="submitBtn" class="action-submit-btn w-full sm:w-auto text-xs sm:text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>I-sumite ang Intake Sheet (Submit Application)</span>
                    </button>
                </div>

            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 px-4 mt-8">
        <p>© {{ date('Y') }} Tanggapan ng Kagalingang Panlipunan at Pagpapaunlad (MSWDO) &bull; Bayan ng Silang, Cavite.</p>
    </footer>

    <!-- ===================================== -->
    <!-- MAHALAGANG PAUNAWA ADVISORY MODAL (REFERENCE STYLE) -->
    <!-- ===================================== -->
    <div id="applicantAdvisoryModal" class="advisory-modal" role="dialog" aria-modal="true" aria-labelledby="advisoryModalTitle">
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
                    1. Punan nang wasto at kumpleto ang lahat ng kinakailangang impormasyon na may <span class="text-rose-600 font-bold">pulang asterisk (*)</span>.
                </p>
                <p>
                    2. <strong class="text-amber-600 font-bold">6-Month Policy Restriction:</strong> Ang tulong pinansyal ay may <strong class="text-amber-600 font-bold">6-month validity period</strong> bawat benepisyaryo alinsunod sa patakaran ng MSWDO Silang.
                </p>
                <p>
                    3. Ihanda ang mga <strong class="text-amber-600 font-bold">sumusuportang dokumento</strong> (Valid ID, Barangay Certificate of Indigency, Medical Abstract/Certificate o Hospital Bill) kapag pupunta sa tanggapan.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="advisory-modal-footer">
                <button type="button" onclick="closeAdvisoryModal()" class="advisory-btn-close w-full sm:w-auto text-center">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/financial-assistance.js') }}"></script>
    <script>
        // Mobile Menu Toggle (dropdown style) with hamburger ↔ X icon swap
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

        document.addEventListener('DOMContentLoaded', function () {
            const msgEl = document.getElementById('duplicateAlertMessage');
            const dateEl = document.getElementById('duplicateEligibleDate');
            if (msgEl && dateEl) {
                const observer = new MutationObserver(function () {
                    const text = msgEl.textContent || '';
                    const match = text.match(/Next eligible date:\s*([A-Za-z0-9, ]+?)(?:\.|$)/i);
                    if (match && match[1]) {
                        dateEl.textContent = match[1].trim();
                    }
                });
                observer.observe(msgEl, { childList: true, characterData: true, subtree: true });
            }
        });
    </script>
</body>

</html>
