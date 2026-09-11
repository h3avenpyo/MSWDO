<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={corePlugins:{preflight:false}}</script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{--primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;}
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100%;overflow-x:hidden;overflow-y:auto;}
        body{font-size:14px;line-height:1.5;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        .form-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);padding:32px;overflow:visible;}
        .form-label{font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px;display:block;text-transform:uppercase;letter-spacing:.3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .form-input{width:100%;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-size:14px;color:var(--text-primary);outline:none;transition:border-color .2s,box-shadow .2s;font-family:var(--font-family);height:44px;box-sizing:border-box;}
        .form-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.1);}
        .form-input::placeholder{color:var(--text-muted);}
        .form-input[readonly]{background-color:#F8FAFC;color:var(--text-secondary);cursor:default;}
        select.form-input{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right .75rem center;background-size:1rem;padding-right:2.5rem;}
        textarea.form-input{resize:vertical;height:auto;min-height:80px;}
        input[type="date"].form-input{position:relative;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 12px center;background-size:18px;padding-right:38px;}
        input[type="date"].form-input::-webkit-calendar-picker-indicator{opacity:0;cursor:pointer;position:absolute;right:0;top:0;width:38px;height:100%;}
        input[type="number"].form-input{-moz-appearance:textfield;appearance:textfield;}
        input[type="number"].form-input::-webkit-inner-spin-button,input[type="number"].form-input::-webkit-outer-spin-button{-webkit-appearance:none;margin:0;}
        .btn{border:1px solid var(--border);background:var(--surface);color:var(--text-primary);padding:10px 20px;border-radius:10px;font-size:14px;font-weight:500;display:inline-flex;align-items:center;gap:8px;box-shadow:var(--shadow);transition:all .2s ease;height:42px;cursor:pointer;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn.primary{background:var(--primary);color:#fff;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);}
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}

        /* ── Custom Modal Styles ── */
        .custom-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            z-index: 99999;
            backdrop-filter: blur(4px);
            transition: opacity 0.2s ease;
        }
        .custom-modal-backdrop.active {
            display: flex;
        }
        .custom-modal-dialog {
            background: var(--background);
            border-radius: 14px;
            width: 100%;
            max-width: 780px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .custom-modal-header {
            background: #1A237E;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .custom-modal-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #ffffff;
        }
        .custom-modal-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        .custom-modal-close:hover { opacity: 1; }
        .custom-modal-body {
            padding: 16px 20px;
            overflow-y: auto;
            flex: 1;
            -webkit-overflow-scrolling: touch;
        }
        .custom-modal-section {
            margin-bottom: 16px;
        }
        .custom-modal-section:last-child {
            margin-bottom: 0;
        }
        .custom-modal-section-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 6px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .custom-modal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .custom-modal-col-full {
            grid-column: 1 / -1;
        }
        .custom-modal-col-span2 {
            grid-column: span 2;
        }
        .custom-modal-field {
            margin-bottom: 0;
        }
        .custom-modal-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.72rem;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .custom-modal-value {
            background: var(--surface);
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 500;
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-size: 0.85rem;
            overflow-wrap: anywhere;
            min-height: 32px;
            display: flex;
            align-items: center;
        }
        .custom-modal-footer {
            padding: 10px 20px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Modal Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .custom-modal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .custom-modal-col-span2 {
                grid-column: 1 / -1;
            }
        }
        @media (max-width: 575.98px) {
            .custom-modal-backdrop {
                padding: 8px;
            }
            .custom-modal-dialog {
                max-height: 90vh;
                border-radius: 12px;
            }
            .custom-modal-header {
                padding: 10px 14px;
            }
            .custom-modal-title {
                font-size: 0.9rem;
            }
            .custom-modal-body {
                padding: 12px 12px;
            }
            .custom-modal-grid {
                grid-template-columns: 1fr !important;
                gap: 8px;
            }
            .custom-modal-col-full,
            .custom-modal-col-span2 {
                grid-column: 1 / -1 !important;
            }
            .custom-modal-label {
                font-size: 0.7rem;
            }
            .custom-modal-value {
                padding: 5px 8px;
                font-size: 0.8rem;
                min-height: 30px;
            }
            .custom-modal-footer {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'registration', 'mobileSubtitle' => 'Senior Citizen Registration'])

    <!-- Main Content -->
    <div class="main">
        <div class="main-scroll">
        <div class="form-card">
            <h2 class="text-lg font-bold mb-1">Register Senior Citizen</h2>
            <p class="text-sm mb-6" style="color:var(--text-secondary)">Fill in the details below to register a new senior citizen.</p>

            <form method="POST" action="{{ route('admin.senior.registration.store') }}" id="registrationForm">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
                        <i data-lucide="user" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div class="lg:col-span-2">
                            <label class="form-label">Full Name <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <input type="text" name="full_name" class="form-input" placeholder="Enter full name" value="{{ old('full_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label">Sex <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <select class="form-input" name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Birth Date <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <input type="date" name="birth_date" id="birthDate" class="form-input" value="{{ old('birth_date') }}" required onchange="calculateAge()">
                        </div>
                        <div>
                            <label class="form-label">Month</label>
                            <input type="text" name="month" id="month" class="form-input" placeholder="Auto-calculated" required readonly>
                        </div>
                        <div>
                            <label class="form-label">Age</label>
                            <input type="number" name="age" id="age" class="form-input" placeholder="Auto-calculated" value="{{ old('age') }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="mb-8">
                    <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
                        <i data-lucide="map-pin" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
                        Address Information
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div class="lg:col-span-2">
                            <label class="form-label">Address <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <input type="text" name="address" class="form-input" placeholder="Enter complete address" value="{{ old('address') }}" required>
                        </div>
                        <div>
                            <label class="form-label">Barangay <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <select class="form-input" name="barangay" id="barangay" required onchange="updateControlNumber()">
                                <option value="">Select Barangay</option>
                                <option value="Acacia" {{ old('barangay') == 'Acacia' ? 'selected' : '' }}>Acacia</option>
                                <option value="Adlas" {{ old('barangay') == 'Adlas' ? 'selected' : '' }}>Adlas</option>
                                <option value="Anahaw I" {{ old('barangay') == 'Anahaw I' ? 'selected' : '' }}>Anahaw I</option>
                                <option value="Anahaw II" {{ old('barangay') == 'Anahaw II' ? 'selected' : '' }}>Anahaw II</option>
                                <option value="Balite I" {{ old('barangay') == 'Balite I' ? 'selected' : '' }}>Balite I</option>
                                <option value="Balite II" {{ old('barangay') == 'Balite II' ? 'selected' : '' }}>Balite II</option>
                                <option value="Balubad" {{ old('barangay') == 'Balubad' ? 'selected' : '' }}>Balubad</option>
                                <option value="Banaba" {{ old('barangay') == 'Banaba' ? 'selected' : '' }}>Banaba</option>
                                <option value="Batas" {{ old('barangay') == 'Batas' ? 'selected' : '' }}>Batas</option>
                                <option value="Biga I" {{ old('barangay') == 'Biga I' ? 'selected' : '' }}>Biga I</option>
                                <option value="Biga II" {{ old('barangay') == 'Biga II' ? 'selected' : '' }}>Biga II</option>
                                <option value="Biluso" {{ old('barangay') == 'Biluso' ? 'selected' : '' }}>Biluso</option>
                                <option value="Bucal" {{ old('barangay') == 'Bucal' ? 'selected' : '' }}>Bucal</option>
                                <option value="Buho" {{ old('barangay') == 'Buho' ? 'selected' : '' }}>Buho</option>
                                <option value="Bulihan" {{ old('barangay') == 'Bulihan' ? 'selected' : '' }}>Bulihan</option>
                                <option value="Cabangaan" {{ old('barangay') == 'Cabangaan' ? 'selected' : '' }}>Cabangaan</option>
                                <option value="Carmen" {{ old('barangay') == 'Carmen' ? 'selected' : '' }}>Carmen</option>
                                <option value="Hoyo" {{ old('barangay') == 'Hoyo' ? 'selected' : '' }}>Hoyo</option>
                                <option value="Hukay" {{ old('barangay') == 'Hukay' ? 'selected' : '' }}>Hukay</option>
                                <option value="Iba" {{ old('barangay') == 'Iba' ? 'selected' : '' }}>Iba</option>
                                <option value="Inchican" {{ old('barangay') == 'Inchican' ? 'selected' : '' }}>Inchican</option>
                                <option value="Ipil I" {{ old('barangay') == 'Ipil I' ? 'selected' : '' }}>Ipil I</option>
                                <option value="Ipil II" {{ old('barangay') == 'Ipil II' ? 'selected' : '' }}>Ipil II</option>
                                <option value="Kalubkob" {{ old('barangay') == 'Kalubkob' ? 'selected' : '' }}>Kalubkob</option>
                                <option value="Kaong" {{ old('barangay') == 'Kaong' ? 'selected' : '' }}>Kaong</option>
                                <option value="Lalaan I" {{ old('barangay') == 'Lalaan I' ? 'selected' : '' }}>Lalaan I</option>
                                <option value="Lalaan II" {{ old('barangay') == 'Lalaan II' ? 'selected' : '' }}>Lalaan II</option>
                                <option value="Litlit" {{ old('barangay') == 'Litlit' ? 'selected' : '' }}>Litlit</option>
                                <option value="Lucsuhin" {{ old('barangay') == 'Lucsuhin' ? 'selected' : '' }}>Lucsuhin</option>
                                <option value="Lumil" {{ old('barangay') == 'Lumil' ? 'selected' : '' }}>Lumil</option>
                                <option value="Maguyam" {{ old('barangay') == 'Maguyam' ? 'selected' : '' }}>Maguyam</option>
                                <option value="Malabag" {{ old('barangay') == 'Malabag' ? 'selected' : '' }}>Malabag</option>
                                <option value="Malaking Tatyao" {{ old('barangay') == 'Malaking Tatyao' ? 'selected' : '' }}>Malaking Tatyao</option>
                                <option value="Mataas na Burol" {{ old('barangay') == 'Mataas na Burol' ? 'selected' : '' }}>Mataas na Burol</option>
                                <option value="Munting Ilog" {{ old('barangay') == 'Munting Ilog' ? 'selected' : '' }}>Munting Ilog</option>
                                <option value="Narra I" {{ old('barangay') == 'Narra I' ? 'selected' : '' }}>Narra I</option>
                                <option value="Narra II" {{ old('barangay') == 'Narra II' ? 'selected' : '' }}>Narra II</option>
                                <option value="Narra III" {{ old('barangay') == 'Narra III' ? 'selected' : '' }}>Narra III</option>
                                <option value="Paligawan" {{ old('barangay') == 'Paligawan' ? 'selected' : '' }}>Paligawan</option>
                                <option value="Pasong Langka" {{ old('barangay') == 'Pasong Langka' ? 'selected' : '' }}>Pasong Langka</option>
                                <option value="Barangay I (Poblacion)" {{ old('barangay') == 'Barangay I (Poblacion)' ? 'selected' : '' }}>Barangay I (Poblacion)</option>
                                <option value="Barangay II (Poblacion)" {{ old('barangay') == 'Barangay II (Poblacion)' ? 'selected' : '' }}>Barangay II (Poblacion)</option>
                                <option value="Barangay III (Poblacion)" {{ old('barangay') == 'Barangay III (Poblacion)' ? 'selected' : '' }}>Barangay III (Poblacion)</option>
                                <option value="Barangay IV (Poblacion)" {{ old('barangay') == 'Barangay IV (Poblacion)' ? 'selected' : '' }}>Barangay IV (Poblacion)</option>
                                <option value="Barangay V (Poblacion)" {{ old('barangay') == 'Barangay V (Poblacion)' ? 'selected' : '' }}>Barangay V (Poblacion)</option>
                                <option value="Pooc I" {{ old('barangay') == 'Pooc I' ? 'selected' : '' }}>Pooc I</option>
                                <option value="Pooc II" {{ old('barangay') == 'Pooc II' ? 'selected' : '' }}>Pooc II</option>
                                <option value="Pulong Bunga" {{ old('barangay') == 'Pulong Bunga' ? 'selected' : '' }}>Pulong Bunga</option>
                                <option value="Pulong Saging" {{ old('barangay') == 'Pulong Saging' ? 'selected' : '' }}>Pulong Saging</option>
                                <option value="Puting Kahoy" {{ old('barangay') == 'Puting Kahoy' ? 'selected' : '' }}>Puting Kahoy</option>
                                <option value="Sabutan" {{ old('barangay') == 'Sabutan' ? 'selected' : '' }}>Sabutan</option>
                                <option value="San Miguel I" {{ old('barangay') == 'San Miguel I' ? 'selected' : '' }}>San Miguel I</option>
                                <option value="San Miguel II" {{ old('barangay') == 'San Miguel II' ? 'selected' : '' }}>San Miguel II</option>
                                <option value="San Vicente I" {{ old('barangay') == 'San Vicente I' ? 'selected' : '' }}>San Vicente I</option>
                                <option value="San Vicente II" {{ old('barangay') == 'San Vicente II' ? 'selected' : '' }}>San Vicente II</option>
                                <option value="Santol" {{ old('barangay') == 'Santol' ? 'selected' : '' }}>Santol</option>
                                <option value="Tartaria" {{ old('barangay') == 'Tartaria' ? 'selected' : '' }}>Tartaria</option>
                                <option value="Tibig" {{ old('barangay') == 'Tibig' ? 'selected' : '' }}>Tibig</option>
                                <option value="Toledo" {{ old('barangay') == 'Toledo' ? 'selected' : '' }}>Toledo</option>
                                <option value="Tubuan I" {{ old('barangay') == 'Tubuan I' ? 'selected' : '' }}>Tubuan I</option>
                                <option value="Tubuan II" {{ old('barangay') == 'Tubuan II' ? 'selected' : '' }}>Tubuan II</option>
                                <option value="Tubuan III" {{ old('barangay') == 'Tubuan III' ? 'selected' : '' }}>Tubuan III</option>
                                <option value="Ulat" {{ old('barangay') == 'Ulat' ? 'selected' : '' }}>Ulat</option>
                                <option value="Yakal" {{ old('barangay') == 'Yakal' ? 'selected' : '' }}>Yakal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="mb-8">
                    <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
                        <i data-lucide="phone" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
                        Contact Information
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="form-label">Contact Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <input type="text" name="contact_number" class="form-input" placeholder="e.g. 09171234567" pattern="[0-9]{11}" maxlength="11" value="{{ old('contact_number') }}" required>
                        </div>
                        <div>
                            <label class="form-label">Emergency Contact Person Name</label>
                            <input type="text" name="emergency_contact_name" class="form-input" placeholder="e.g. Ricardo Villanueva" value="{{ old('emergency_contact_name') }}">
                        </div>
                        <div>
                            <label class="form-label">Emergency Phone Number</label>
                            <input type="text" name="emergency_contact_number" class="form-input" placeholder="e.g. 09181234567" pattern="[0-9]{11}" maxlength="11" value="{{ old('emergency_contact_number') }}">
                        </div>
                    </div>
                </div>

                <!-- Identification Information Section -->
                <div class="mb-8">
                    <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
                        <i data-lucide="id-card" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
                        Identification Information
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="form-label">Year Applied</label>
                            <input type="number" name="year_applied" id="year_applied" class="form-input" placeholder="e.g. 2026" value="{{ old('year_applied') ?? date('Y') }}" required onchange="updateControlNumber()">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="form-label">Control Number</label>
                            <input type="text" name="control_number" id="controlNumber" class="form-input" placeholder="Auto-generated" value="{{ old('control_number') }}" readonly>
                        </div>
                        <div>
                            <label class="form-label">PhilSys Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <input type="text" name="philsys_number" class="form-input" placeholder="Enter 12-digit PhilSys number" pattern="[0-9]{12}" maxlength="12" value="{{ old('philsys_number') }}">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="form-label">RRN Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                            <input type="text" name="rrn_number" class="form-input" placeholder="Enter 29-digit RRN number" pattern="[0-9]{29}" maxlength="29" value="{{ old('rrn_number') }}">
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="mb-8">
                    <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
                        <i data-lucide="file-text" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
                        Additional Information
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div class="lg:col-span-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-input" rows="3" placeholder="Enter any additional remarks">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn" style="height:44px;" onclick="location.href='/admin/senior'">Cancel</button>
                    <button type="button" class="btn primary" style="height:44px;" onclick="confirmSubmit(event)">
                        <i data-lucide="user-plus" style="width:16px;height:16px"></i> Register Senior Citizen
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="custom-modal-backdrop">
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">
                <i data-lucide="clipboard-check" style="width:20px;height:20px;"></i>
                Confirm Registration
            </h5>
            <button onclick="closeConfirmModal()" class="custom-modal-close" aria-label="Close modal">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <div class="custom-modal-body">
            <!-- Personal Information Section -->
            <div class="custom-modal-section">
                <div class="custom-modal-section-title">
                    <i data-lucide="user" style="width:16px;height:16px;"></i>
                    Personal Information
                </div>
                <div class="custom-modal-grid">
                    <div class="custom-modal-field custom-modal-col-full">
                        <label class="custom-modal-label">Full Name</label>
                        <div class="custom-modal-value" id="modalFullName">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Sex</label>
                        <div class="custom-modal-value" id="modalSex">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Birth Date</label>
                        <div class="custom-modal-value" id="modalBirthDate">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Month</label>
                        <div class="custom-modal-value" id="modalMonth">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Age</label>
                        <div class="custom-modal-value" id="modalAge">—</div>
                    </div>
                </div>
            </div>

            <!-- Address Information Section -->
            <div class="custom-modal-section">
                <div class="custom-modal-section-title">
                    <i data-lucide="map-pin" style="width:16px;height:16px;"></i>
                    Address Information
                </div>
                <div class="custom-modal-grid">
                    <div class="custom-modal-field custom-modal-col-full">
                        <label class="custom-modal-label">Address</label>
                        <div class="custom-modal-value" id="modalAddress">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Barangay</label>
                        <div class="custom-modal-value" id="modalBarangay">—</div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="custom-modal-section">
                <div class="custom-modal-section-title">
                    <i data-lucide="phone" style="width:16px;height:16px;"></i>
                    Contact Information
                </div>
                <div class="custom-modal-grid">
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Contact Number</label>
                        <div class="custom-modal-value" id="modalContactNumber">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Emergency Contact</label>
                        <div class="custom-modal-value" id="modalEmergencyContact">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Emergency Phone</label>
                        <div class="custom-modal-value" id="modalEmergencyPhone">—</div>
                    </div>
                </div>
            </div>

            <!-- Identification Information Section -->
            <div class="custom-modal-section">
                <div class="custom-modal-section-title">
                    <i data-lucide="id-card" style="width:16px;height:16px;"></i>
                    Identification Information
                </div>
                <div class="custom-modal-grid">
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Control Number</label>
                        <div class="custom-modal-value" id="modalControlNumber">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">Year Applied</label>
                        <div class="custom-modal-value" id="modalYearApplied">—</div>
                    </div>
                    <div class="custom-modal-field">
                        <label class="custom-modal-label">PhilSys Number</label>
                        <div class="custom-modal-value" id="modalPhilsysNumber">—</div>
                    </div>
                    <div class="custom-modal-field custom-modal-col-span2">
                        <label class="custom-modal-label">RRN Number</label>
                        <div class="custom-modal-value" id="modalRrnNumber">—</div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="custom-modal-section">
                <div class="custom-modal-section-title">
                    <i data-lucide="file-text" style="width:16px;height:16px;"></i>
                    Additional Information
                </div>
                <div class="custom-modal-grid">
                    <div class="custom-modal-field custom-modal-col-full">
                        <label class="custom-modal-label">Remarks</label>
                        <div class="custom-modal-value" id="modalRemarks" style="white-space:pre-wrap;">—</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn" onclick="closeConfirmModal()">Go Back</button>
            <button type="button" class="btn primary" onclick="confirmRegistration()">
                <i data-lucide="check-circle" style="width:16px;height:16px"></i> Confirm & Register
            </button>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none">@csrf</form>

<script>
    // Input validation - prevent invalid characters
    document.addEventListener('DOMContentLoaded', function() {
        const contactNumberInput = document.querySelector('[name="contact_number"]');
        const emergencyContactNumberInput = document.querySelector('[name="emergency_contact_number"]');
        const philsysNumberInput = document.querySelector('[name="philsys_number"]');
        const rrnNumberInput = document.querySelector('[name="rrn_number"]');

        function restrictToDigits(input, maxLength) {
            input.addEventListener('input', function(e) {
                // Remove non-digit characters
                let value = this.value.replace(/[^0-9]/g, '');
                // Truncate to max length
                if (value.length > maxLength) {
                    value = value.substring(0, maxLength);
                }
                // Update value only if changed
                if (this.value !== value) {
                    this.value = value;
                }
            });
        }

        restrictToDigits(contactNumberInput, 11);
        restrictToDigits(emergencyContactNumberInput, 11);
        restrictToDigits(philsysNumberInput, 12);
        restrictToDigits(rrnNumberInput, 29);

        if (document.getElementById('birthDate') && document.getElementById('birthDate').value) {
            calculateAge();
        }
        if (document.getElementById('barangay') && document.getElementById('barangay').value) {
            updateControlNumber();
        }
    });

    @if($seniorCreated ?? false)
        Swal.fire({title:'Success!',text:'Senior citizen registered successfully.',icon:'success',confirmButtonColor:'#1A237E',confirmButtonText:'OK',background:'#ffffff',customClass:{popup:'rounded-4 shadow-lg'}});
    @endif
    @if($errors->any())
        Swal.fire({title:'Error!',text:'{{ $errors->first() }}',icon:'error',confirmButtonColor:'#1A237E',confirmButtonText:'OK',background:'#ffffff',customClass:{popup:'rounded-4 shadow-lg'}});
    @endif

    function calculateAge(){
        const birthDate=document.getElementById('birthDate').value;
        const ageField=document.getElementById('age');
        const monthField=document.getElementById('month');
        if(birthDate){
            const birth=new Date(birthDate);const today=new Date();
            let age=today.getFullYear()-birth.getFullYear();
            const monthDiff=today.getMonth()-birth.getMonth();
            if(monthDiff<0||(monthDiff===0&&today.getDate()<birth.getDate()))age--;
            ageField.value=age;
            const months=['January','February','March','April','May','June','July','August','September','October','November','December'];
            monthField.value=months[birth.getMonth()];
        }else{ageField.value='';monthField.value='';}
    }

    function confirmSubmit(event){
        event.preventDefault();
        const age=parseInt(document.getElementById('age').value);
        if(!age||age<60){Swal.fire({title:'Age Requirement',text:'The age field must be at least 60 to register as a senior citizen.',icon:'warning',confirmButtonColor:'#1A237E',confirmButtonText:'OK',background:'#ffffff',customClass:{popup:'rounded-4 shadow-lg'}});return false;}
        const barangay=document.getElementById('barangay').value;
        if(!barangay){Swal.fire({title:'Barangay Required',text:'Please select a barangay before proceeding.',icon:'warning',confirmButtonColor:'#1A237E',confirmButtonText:'OK',background:'#ffffff',customClass:{popup:'rounded-4 shadow-lg'}});return false;}
        const sex=document.querySelector('[name="sex"]').value;
        if(!sex){Swal.fire({title:'Sex Required',text:'Please select a sex before proceeding.',icon:'warning',confirmButtonColor:'#1A237E',confirmButtonText:'OK',background:'#ffffff',customClass:{popup:'rounded-4 shadow-lg'}});return false;}

        // Populate modal with form data
        const f=document.getElementById('registrationForm');
        const get=(n)=>{const el=f.querySelector('[name="'+n+'"]');return el?(el.tagName==='SELECT'?el.options[el.selectedIndex].text:el.value):'-';};
        const v=(n)=>{const val=get(n);return val&&val!=='Select Barangay'&&val!=='Select Sex'?val:'Not provided';};

        document.getElementById('modalFullName').textContent=v('full_name');
        document.getElementById('modalSex').textContent=v('sex');
        document.getElementById('modalBirthDate').textContent=v('birth_date');
        document.getElementById('modalMonth').textContent=v('month');
        document.getElementById('modalAge').textContent=v('age');
        document.getElementById('modalAddress').textContent=v('address');
        document.getElementById('modalBarangay').textContent=v('barangay');
        document.getElementById('modalContactNumber').textContent=v('contact_number');
        document.getElementById('modalEmergencyContact').textContent=v('emergency_contact_name');
        document.getElementById('modalEmergencyPhone').textContent=v('emergency_contact_number');
        document.getElementById('modalControlNumber').textContent=v('control_number');
        document.getElementById('modalYearApplied').textContent=v('year_applied');
        document.getElementById('modalPhilsysNumber').textContent=v('philsys_number');
        document.getElementById('modalRrnNumber').textContent=v('rrn_number');
        document.getElementById('modalRemarks').textContent=f.querySelector('[name="remarks"]').value||'None';

        // Show custom modal
        const modal=document.getElementById('confirmModal');
        modal.classList.add('active');
        // Recreate icons after modal becomes visible
        setTimeout(()=>{lucide.createIcons();},50);
        return false;
    }

    function closeConfirmModal(){
        document.getElementById('confirmModal').classList.remove('active');
    }

    function confirmRegistration(){
        document.getElementById('registrationForm').submit();
    }

    function updateControlNumber(){
        const barangay=document.getElementById('barangay').value;
        const year=document.getElementById('year_applied').value||new Date().getFullYear();
        const controlNumberField=document.getElementById('controlNumber');
        const barangaySequences={!! json_encode($barangaySequences ?? []) !!};
        const barangayCodes={!! json_encode($barangayCodes ?? []) !!};
        if(barangay&&barangaySequences[barangay]&&barangayCodes[barangay]){
            const code=barangayCodes[barangay];const seq=String(barangaySequences[barangay]).padStart(6,'0');
            controlNumberField.value=`SC-${code}-${year}-${seq}`;
        }else{controlNumberField.value='';}
    }

    function toggleMobileMoreNav(){
        const extra=document.getElementById('mobileNavExtra');
        const icon=document.getElementById('mobileMoreIcon');
        if(!extra) return;
        if(extra.style.display==='none'||extra.style.display===''){
            extra.style.display='flex';
            if(icon){icon.setAttribute('data-lucide','chevron-down');lucide.createIcons();}
        } else {
            extra.style.display='none';
            if(icon){icon.setAttribute('data-lucide','chevron-up');lucide.createIcons();}
        }
    }

    lucide.createIcons();
</script>
</body>
</html>



