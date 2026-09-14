<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Social Case Study Application - MSWDO Silang</title>
    <meta name="description" content="Opisyal na Online Application Form para sa Social Case Study Report (SCSR) sa MSWDO Silang">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="bg-[#F8FAFC] text-[#1F2937] antialiased">

    <!-- NAVBAR -->
    <header class="fixed top-0 z-50 w-full bg-primary bg-opacity-95 backdrop-blur shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 sm:gap-4 shrink-0">
                    <div class="h-11 w-11 sm:h-14 sm:w-14 rounded-full p-1 shrink-0">
                        @php
                        $logo = null;
                        if (file_exists(public_path('images/mswdo-logo.png'))) {
                        $logo = 'mswdo-logo.png';
                        } else {
                        $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
                        if (!empty($files)) {
                        $logo = basename($files[0]);
                        }
                        }
                        @endphp
                        @if ($logo)
                        <img src="{{ asset('images/' . $logo) }}" class="rounded-full h-full w-full object-cover" alt="MSWDO Logo">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-base sm:text-lg tracking-tight leading-tight">
                            MSWDO SILANG
                        </h1>
                        <p class="text-offwhite text-[11px] sm:text-xs leading-tight">
                            Municipal Social Welfare &amp; Development Office
                        </p>
                    </div>
                </a>
                <!-- Navigation & Actions -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- <nav class="hidden lg:flex items-center gap-6 text-offwhite text-sm font-medium mr-2">
                        <a href="/" class="hover:text-warm-gold transition">Home</a>
                        <a href="/#services" class="hover:text-warm-gold transition">Coverage &amp; Purpose</a>
                        <a href="/#requirements" class="hover:text-warm-gold transition">Requirements</a>
                        <a href="/#process" class="hover:text-warm-gold transition">How to Apply</a>
                        <a href="/#about" class="hover:text-warm-gold transition">About</a>
                        <a href="/#contact" class="hover:text-warm-gold transition">Contact</a>
                    </nav> -->
                    <a href="/" class="flex items-center gap-1.5 text-xs sm:text-sm font-medium text-white/90 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg transition">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Bumalik sa Home</span>
                    </a>
                    <a href="/admin" class="navbar-login-btn text-xs sm:text-sm">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </header>


    <!-- MAIN CONTENT -->
    <main class="relative pt-24 pb-20 px-4 sm:px-6">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 pointer-events-none select-none">
            <img src="{{ asset('images/background.png') }}" class="w-full h-full object-cover object-center" alt="Background">
        </div>
        
        <div class="relative z-10 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white shadow-md">
                    <i data-lucide="file-text" class="h-7 w-7"></i>
                </div>
                <p class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 mb-1">
                    Republika ng Pilipinas &bull; Bayan ng Silang, Cavite
                </p>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-primary mb-2">
                    Online Social Case Study Application
                </h1>
                <p class="text-slate-600 text-sm sm:text-base max-w-2xl mx-auto">
                    Punan ang opisyal na form sa ibaba upang magsumite ng aplikasyon para sa Social Case Study Report (SCSR) sa MSWDO Silang.
                </p>
            </div>

            <!-- Information Notice -->
            <div class="mx-auto mb-8 rounded-xl border border-blue-200 bg-blue-50/90 p-4 sm:p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="mt-0.5 h-5 w-5 shrink-0 text-primary"></i>
                    <div class="text-xs sm:text-sm leading-relaxed text-slate-700">
                        <p class="font-bold text-primary">Paalala Bago Magsumite ng Social Case Study Request:</p>
                        <p class="mt-1 text-slate-600">
                            Pakitiyak na wasto at kumpleto ang lahat ng impormasyon ng kliyente o pasyente. Siguraduhing malinaw at nababasa ang mga ilalakip na dokumento (tulad ng Valid ID, Barangay Certificate of Indigency, Medical Abstract/Billing, Reseta, o Death Certificate). Ang inyong kahilingan ay dadaan sa pagsusuri at panayam ng lisensyadong Social Worker ng MSWDO.
                        </p>
                    </div>
                </div>
            </div>


            <!-- Form -->
            <form id="serviceRequestForm" class="bg-white rounded-2xl shadow-lg p-6 sm:p-10 border border-slate-100">

                @csrf

                <!-- Section 1: Client / Beneficiary -->
                <div class="mb-8 pb-8 border-b border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#1A237E] block mb-1">Hakbang 1</span>
                    <h2 class="text-lg sm:text-xl font-bold text-primary mb-4">Sino ang nangangailangan ng Social Case Study? (Client / Beneficiary)</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="myself" required class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900 text-sm sm:text-base">Sarili ko (Myself)</span>
                                <span class="block text-xs sm:text-sm text-slate-500">Ako ang mismong nangangailangan ng Social Case Study Report.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="child" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900 text-sm sm:text-base">Aking Anak (My Child)</span>
                                <span class="block text-xs sm:text-sm text-slate-500">Ang kahilingan ng Social Case Study ay para sa aking anak.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="parent" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900 text-sm sm:text-base">Aking Magulang (My Parent)</span>
                                <span class="block text-xs sm:text-sm text-slate-500">Ang kahilingan ay para sa aking magulang (tatay o nanay).</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="family" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900 text-sm sm:text-base">Ibang Miyembro ng Pamilya (Family Member)</span>
                                <span class="block text-xs sm:text-sm text-slate-500">Ang kahilingan ay para sa aming kasambahay o miyembro ng pamilya.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="assisting" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900 text-sm sm:text-base">Tinutulungang Kliyente / Kinatawan (Authorized Representative)</span>
                                <span class="block text-xs sm:text-sm text-slate-500">Ako ay nag-aasikaso sa ngalan ng ibang tao o pasyente.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Beneficiary Information -->
                <div class="mb-8 pb-8 border-b border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#1A237E] block mb-1">Hakbang 2</span>
                    <h2 class="text-lg sm:text-xl font-bold text-primary mb-4">Impormasyon ng Benepisyaryo / Pasyente (Beneficiary Information)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="firstName" class="block text-sm font-semibold text-slate-700 mb-2">Unang Pangalan (First Name) <span class="text-rose-600 font-bold">*</span></label>
                            <input type="text" id="firstName" name="first_name" required placeholder="Halimbawa: Juan" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-semibold text-slate-700 mb-2">Apelyido (Last Name) <span class="text-rose-600 font-bold">*</span></label>
                            <input type="text" id="lastName" name="last_name" required placeholder="Halimbawa: Dela Cruz" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="dob" class="block text-sm font-semibold text-slate-700 mb-2">Petsa ng Kapanganakan (Date of Birth) <span class="text-rose-600 font-bold">*</span></label>
                            <input type="date" id="dob" name="dob" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label for="barangay" class="block text-sm font-semibold text-slate-700 mb-2">Barangay sa Silang (Barangay) <span class="text-rose-600 font-bold">*</span></label>
                            <select id="barangay" name="barangay" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                                <option value="">Pumili ng barangay</option>
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contactNumber" class="block text-sm font-semibold text-slate-700 mb-2">Numero ng Kontak (Contact Number) <span class="text-rose-600 font-bold">*</span></label>
                            <input type="text" id="contactNumber" name="contact_number" required placeholder="Halimbawa: 09123456789" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address (Email) <span class="text-rose-600 font-bold">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="Halimbawa: juan@gmail.com" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Kumpletong Tirahan (Complete Address)</label>
                        <input type="text" id="address" name="address" placeholder="Halimbawa: House No., Street, Subdivision, Barangay" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                    </div>
                </div>

                <!-- Section 3: Social Case Study Details -->
                <div class="mb-8 pb-8 border-b border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#1A237E] block mb-1">Hakbang 3</span>
                    <h2 class="text-lg sm:text-xl font-bold text-primary mb-4">Mga Detalye ng Social Case Study (Case Study Details)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Dedicated Service Type Display -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Uri ng Kahilingan (Service Type)</label>
                            <div class="flex items-center gap-2.5 px-4 py-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-800 font-semibold text-sm">
                                <i data-lucide="file-check-2" class="w-5 h-5 text-primary shrink-0"></i>
                                <span>Social Case Study Report (SCSR)</span>
                            </div>
                            <input type="hidden" id="serviceType" name="service_type" value="social_case_study">
                            <p class="text-[11px] text-slate-500 mt-1">Nakatakda eksklusibo para sa Social Case Study application.</p>
                        </div>

                        <!-- Assistance Type / Case Study Purpose -->
                        <div>
                            <label for="assistanceType" class="block text-sm font-semibold text-slate-700 mb-2">Layunin ng Case Study (Purpose / Category) <span class="text-rose-600 font-bold">*</span></label>
                            <select id="assistanceType" name="assistance_type" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 text-sm">
                                <option value="">Pumili ng layunin ng Social Case Study</option>
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

                    <div>
                        <label for="situation" class="block text-sm font-semibold text-slate-700 mb-2">Salaysay ng Sitwasyon at Pangangailangan (Situation &amp; Case Background) <span class="text-rose-600 font-bold">*</span></label>
                        <textarea id="situation" name="situation" rows="4" required placeholder="Halimbawa: Humihiling po kami ng Social Case Study Report para sa Guarantee Letter (GL) sa Malasakit Center para sa hospital bill at gamot ng aking anak na naospital..." class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 resize-vertical text-sm"></textarea>
                        <p class="text-[11px] text-slate-500 mt-1">Ilarawan nang maikli ang kasalukuyang kalagayan at kung saang ahensya o ospital isusumite ang Social Case Study Report.</p>
                    </div>
                </div>

                <!-- Section 4: Upload Documents -->
                <div class="mb-8">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#1A237E] block mb-1">Hakbang 4</span>
                    <h2 class="text-lg sm:text-xl font-bold text-primary mb-2">Pag-upload ng mga Dokumento (Upload Supporting Documents)</h2>
                    <p class="text-xs text-slate-600 mb-4">
                        Mag-upload ng malinaw na kopya o litrato ng mga sumusunod na kinakailangang dokumento para sa pagsusuri ng inyong Social Case Study.
                    </p>

                    <!-- Documentary Guidance Checklist -->
                    <div class="mb-4 p-4 bg-blue-50/70 border border-blue-200/80 rounded-xl text-xs leading-relaxed">
                        <p class="font-bold text-primary mb-2 flex items-center gap-1.5">
                            <i data-lucide="paperclip" class="w-4 h-4 text-primary"></i>
                            Mga Karaniwang Kinakailangang Dokumento para sa Social Case Study Report:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Valid Government ID ng Kliyente o Pasyente</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Barangay Certificate of Indigency (para sa SCSR)</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Medical Abstract / Hospital Statement of Account</span>
                            </div>
                            <div class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5"></i>
                                <span>Reseta ng Gamot, Laboratory Request, o Death Certificate</span>
                            </div>
                        </div>
                    </div>
                    
                    <div id="uploadArea" class="border-2 border-dashed border-primary/50 rounded-xl p-6 sm:p-8 text-center bg-slate-50 cursor-pointer transition-all duration-300 hover:bg-slate-100 hover:border-primary">
                        <div class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
                        <p class="text-primary font-bold text-base mb-1">Pindutin upang mag-upload ng files</p>
                        <p class="text-slate-600 text-xs sm:text-sm">o i-drag at i-drop ang mga dokumento rito</p>
                        <p class="text-slate-500 text-[11px] mt-2">Tinatanggap na format: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                        <p class="text-slate-500 text-[11px]">Pinakamalaking sukat: 10MB bawat file</p>
                    </div>
                    <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden">
                    <div id="fileList" class="mt-4"></div>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-2">
                    <button type="submit" id="submitBtn" class="flex-1 bg-primary text-white px-8 py-3.5 sm:py-4 rounded-xl font-bold hover:bg-slate-800 transition shadow">
                        <div class="flex flex-col items-center">
                            <span class="text-sm sm:text-base">Isumite ang Social Case Study Application</span>
                            <span class="text-xs font-normal text-white/70 mt-0.5">Submit Case Study Request</span>
                        </div>
                    </button>
                    <a href="/" class="flex-1 text-center border-2 border-slate-300 text-slate-700 px-8 py-3.5 sm:py-4 rounded-xl font-bold hover:border-primary hover:text-primary transition flex items-center justify-center">
                        <div class="flex flex-col items-center">
                            <span class="text-sm sm:text-base">Kanselahin (Cancel)</span>
                            <span class="text-xs font-normal text-slate-500 mt-0.5">Bumalik sa Home</span>
                        </div>
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-primary text-white py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <p class="text-offwhite text-xs sm:text-sm">
                © {{ date('Y') }} MSWDO Silang &bull; Municipal Social Welfare &amp; Development Office. All Rights Reserved.
            </p>
        </div>
    </footer>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        let selectedFiles = [];

        // File upload handling
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
            uploadArea.classList.add('bg-slate-100', 'border-primary');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('bg-slate-100', 'border-primary');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('bg-slate-100', 'border-primary');
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
                        text: `Hindi suportado ang uri ng "${file.name}". Pakilakip lamang ang PDF, DOC, DOCX, JPG, JPEG, o PNG na dokumento.`,
                        icon: 'error',
                        confirmButtonColor: '#DC2626'
                    });
                    continue;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'Masyadong Malaki ang File',
                        text: `Ang "${file.name}" ay lagpas sa pinahihintulutang 10MB na sukat.`,
                        icon: 'error',
                        confirmButtonColor: '#DC2626'
                    });
                    continue;
                }

                // Check for duplicates
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
                fileItem.className = 'flex items-center justify-between p-3 bg-slate-50 rounded-lg mb-2';
                fileItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="text-sm text-slate-700">${file.name}</span>
                        <span class="text-xs text-slate-500">(${(file.size / 1024).toFixed(1)} KB)</span>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                fileList.appendChild(fileItem);
            });
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

        // Form submission
        document.getElementById('serviceRequestForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Isinusumite ang Social Case Study Application...</span>
                    </div>
                `;
            }
            
            const formData = new FormData(this);
            
            // Delete pre-existing documents[] entry to prevent duplicate uploads
            formData.delete('documents[]');
            
            // Add selected files
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
                try {
                    data = await response.json();
                } catch (jsonErr) {
                    data = null;
                }

                if (response.ok && data && data.success) {
                    Swal.fire({
                        title: 'Aplikasyon Naisumite!',
                        text: 'Matagumpay na naisumite ang inyong aplikasyon para sa Social Case Study Report (SCSR). Susuriin ito ng MSWDO Social Worker para sa case evaluation.',
                        icon: 'success',
                        confirmButtonColor: '#1A237E',
                        confirmButtonText: 'Bumalik sa Home'
                    }).then(() => {
                        window.location.href = '/';
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
                }
            });
        });
    </script>
</body>
</html>