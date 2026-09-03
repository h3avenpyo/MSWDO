<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Service Request - MSWDO Silang</title>
    <meta name="description" content="Submit an online service request to MSWDO Silang">
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
                        <img src="{{ asset('images/' . $logo) }}" class="rounded-full h-full w-full object-cover">
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
                <!-- Desktop Menu -->
                <nav class="hidden lg:flex items-center gap-8 text-offwhite">
                    <a href="/" class="hover:text-warm-gold transition">Home</a>
                    <a href="/#services" class="hover:text-warm-gold transition">Services</a>
                    <a href="/#about" class="hover:text-warm-gold transition">About</a>
                    <a href="/#contact" class="hover:text-warm-gold transition">Contact</a>
                    <a href="/admin" class="navbar-login-btn">
                        Login
                    </a>
                </nav>
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
            <div class="text-center mb-10">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary text-white shadow-lg">
                    <i data-lucide="file-text" class="h-8 w-8"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-primary mb-3">Online Service Request</h1>
                <p class="text-slate-600 text-base sm:text-lg">Fill out the form below to submit your service request to MSWDO Silang.</p>
            </div>

            <!-- Information Notice -->
            <div class="mx-auto mb-8 rounded-xl border border-primary/30 bg-primary/5 p-5">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="mt-0.5 h-5 w-5 shrink-0 text-primary"></i>
                    <div class="text-sm leading-6 text-slate-700">
                        <p class="font-semibold text-primary">Before submitting your request</p>
                        <p class="mt-1 text-slate-600">
                            Please make sure that the information provided is correct and that your uploaded documents are clear and readable. An MSWDO representative may contact you for verification or additional requirements.
                        </p>
                    </div>
                </div>
            </div>


            <!-- Form -->
            <form id="serviceRequestForm" class="bg-white rounded-2xl shadow-lg p-6 sm:p-10 border border-slate-100">

                @csrf

                <!-- Section 1: Who needs assistance -->
                <div class="mb-8 pb-8 border-b border-slate-200">
                    <h2 class="text-xl font-bold text-primary mb-6">Who needs assistance? (Sino ang nangangailangan ng tulong?)</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="myself" required class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900">Myself (Ako)</span>
                                <span class="block text-sm text-slate-500">I am requesting assistance for myself.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="child" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900">My Child (Anak ko)</span>
                                <span class="block text-sm text-slate-500">I am requesting assistance for my child.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="parent" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900">My Parent (Magulang ko)</span>
                                <span class="block text-sm text-slate-500">I am requesting assistance for my parent.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="family" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900">Another family member (Ibang miyembro ng pamilya)</span>
                                <span class="block text-sm text-slate-500">The request is for our household/family.</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 cursor-pointer transition">
                            <input type="radio" name="request_for" value="assisting" class="w-5 h-5 text-primary focus:ring-primary">
                            <div>
                                <span class="block font-semibold text-slate-900">Someone I am assisting (Tinutulungan kong tao)</span>
                                <span class="block text-sm text-slate-500">I am submitting this request on behalf of another person.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Beneficiary Information -->
                <div class="mb-8 pb-8 border-b border-slate-200">
                    <h2 class="text-xl font-bold text-primary mb-6">Beneficiary Information (Impormasyon ng Benepisyaryo)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="firstName" class="block text-sm font-semibold text-slate-700 mb-2">First name (Unang Pangalan)</label>
                            <input type="text" id="firstName" name="first_name" required placeholder="e.g. Juan" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-semibold text-slate-700 mb-2">Last name (Apelyido)</label>
                            <input type="text" id="lastName" name="last_name" required placeholder="e.g. Dela Cruz" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="dob" class="block text-sm font-semibold text-slate-700 mb-2">Date of birth (Petsa ng Kapanganakan)</label>
                            <input type="date" id="dob" name="dob" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                        </div>
                        <div>
                            <label for="barangay" class="block text-sm font-semibold text-slate-700 mb-2">Barangay (Barangay)</label>
                            <select id="barangay" name="barangay" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contactNumber" class="block text-sm font-semibold text-slate-700 mb-2">Contact number (Numero ng Kontak)</label>
                            <input type="text" id="contactNumber" name="contact_number" required placeholder="e.g. 09123456789" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email address (Email)</label>
                            <input type="email" id="email" name="email" required placeholder="e.g. juan@gmail.com" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Address (Address)</label>
                        <input type="text" id="address" name="address" placeholder="e.g. Block 5 Lot 12, Phase 1, Barangay Poblacion" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                    </div>
                </div>

                <!-- Section 3: Service Details -->
                <div class="mb-8 pb-8 border-b border-slate-200">
                    <h2 class="text-xl font-bold text-primary mb-6">Service Request Details (Detalye ng Kahilingan ng Serbisyo)</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="serviceType" class="block text-sm font-semibold text-slate-700 mb-2">Type of Service (Uri ng Serbisyo)</label>
                            <select id="serviceType" name="service_type" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
                                <option value="">Select service type</option>
                                <option value="financial_assistance">Financial Assistance</option>
                                <option value="social_case_study">Social Case Study</option>
                            </select>
                        </div>
                        <div>
                            <label for="assistanceType" class="block text-sm font-semibold text-slate-700 mb-2">Assistance Type (Uri ng Tulong)</label>
                            <select id="assistanceType" name="assistance_type" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50">
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
                    </div>

                    <div>
                        <label for="situation" class="block text-sm font-semibold text-slate-700 mb-2">Situation Description (Paglalarawan ng Sitwasyon)</label>
                        <textarea id="situation" name="situation" rows="4" required placeholder="e.g. I need financial assistance for my child's hospitalization due to dengue fever." class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-slate-50 resize-vertical"></textarea>
                    </div>
                </div>

                <!-- Section 4: Upload Documents -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-primary mb-6">Upload Documents (Mag-upload ng Dokumento)</h2>
                    
                    <div id="uploadArea" class="border-2 border-dashed border-primary rounded-lg p-8 text-center bg-slate-50 cursor-pointer transition-all duration-300 hover:bg-slate-100">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
                        <p class="text-primary font-semibold text-lg mb-2">Click to upload files</p>
                        <p class="text-slate-600 text-sm">or drag and drop files here</p>
                        <p class="text-slate-500 text-xs mt-3">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                        <p class="text-slate-500 text-xs">Maximum file size: 10MB per file</p>
                    </div>
                    <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden">
                    <div id="fileList" class="mt-4"></div>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-primary text-white px-8 py-4 rounded-xl font-bold hover:bg-slate-800 transition shadow-lg">
                        <div class="flex flex-col items-center">
                            <span>Submit Request</span>
                            <span class="text-xs font-normal text-white/70 mt-1">Isumite ang Kahilingan</span>
                        </div>
                    </button>
                    <a href="/" class="flex-1 text-center border-2 border-slate-300 text-slate-700 px-8 py-4 rounded-xl font-bold hover:border-primary hover:text-primary transition">
                        <div class="flex flex-col items-center">
                            <span>Cancel</span>
                            <span class="text-xs font-normal text-slate-500 mt-1">Kanselahin</span>
                        </div>
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-primary text-white py-8 sm:py-10 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <p class="text-offwhite text-[10px] sm:text-xs sm:text-sm">
                © {{ date('Y') }} MSWDO Silang. All Rights Reserved.
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
                        title: 'Invalid File Type',
                        text: `${file.name} is not a valid file type. Please upload PDF, DOC, DOCX, JPG, JPEG, or PNG files.`,
                        icon: 'error',
                        confirmButtonColor: '#DC2626'
                    });
                    continue;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: `${file.name} exceeds the 10MB limit.`,
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
            
            const formData = new FormData(this);
            const serviceType = document.getElementById('serviceType').value;
            
            // Determine submission URL based on service type
            let submitUrl = '/service-request';
            if (serviceType === 'social_case_study') {
                submitUrl = '/admin/social-case/online-requests';
            }
            
            // Add selected files
            for (let i = 0; i < selectedFiles.length; i++) {
                formData.append('documents[]', selectedFiles[i]);
            }

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Request Submitted',
                        text: 'Your service request has been submitted successfully. An MSWDO officer will review your request.',
                        icon: 'success',
                        confirmButtonColor: '#1A237E',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '/';
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
        });
    </script>
</body>
</html>