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
        .form-label{font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px;display:block;text-transform:uppercase;letter-spacing:.3px;}
        .form-input{width:100%;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-size:14px;color:var(--text-primary);outline:none;transition:border-color .2s,box-shadow .2s;font-family:var(--font-family);height:44px;}
        .form-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.1);}
        .form-input::placeholder{color:var(--text-muted);}
        select.form-input{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right .75rem center;background-size:1rem;padding-right:2.5rem;}
        textarea.form-input{resize:vertical;height:auto;min-height:80px;}
        input[type="date"].form-input{min-height:44px;appearance:none;-webkit-appearance:none;position:relative;background-image:none;padding-right:14px;}
        .btn{border:1px solid var(--border);background:var(--surface);color:var(--text-primary);padding:10px 20px;border-radius:10px;font-size:14px;font-weight:500;display:inline-flex;align-items:center;gap:8px;box-shadow:var(--shadow);transition:all .2s ease;height:42px;cursor:pointer;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn.primary{background:var(--primary);color:#fff;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);}

        input[type="date"].form-input{background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;}
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'registration', 'mobileSubtitle' => 'Senior Citizen Registration'])

    <!-- Main Content -->
    <div class="main">
        @php
            $userName = session('admin_user_name') ?? 'Admin User';
            $words = explode(' ', $userName);
            $initials = count($words) >= 2 ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($userName, 0, 2));
        @endphp

        <div class="main-scroll">
        <div class="desktop-datetime-container" style="display:none">
            <div class="flex items-center gap-5 justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="desktopDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                    {{ $initials }}
                </div>
            </div>
        </div>

        <!-- <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Senior Citizen Registration</h1>
            </div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">

                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                    {{ $initials }}
                </div>
            </div>
        </header> -->

        <div class="form-card">
            <h2 class="text-lg font-bold mb-1">Register Senior Citizen</h2>
            <p class="text-sm mb-6" style="color:var(--text-secondary)">Fill in the details below to register a new senior citizen.</p>

            <form method="POST" action="{{ route('admin.senior.registration.store') }}" onsubmit="return confirmSubmit(event)" id="registrationForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="form-label">Year Applied</label>
                        <input type="number" name="year_applied" id="year_applied" class="form-input" placeholder="e.g. 2026" value="{{ old('year_applied') ?? date('Y') }}" required onchange="updateControlNumber()">
                    </div>
                    <div>
                        <label class="form-label">Control Number</label>
                        <input type="text" name="control_number" id="controlNumber" class="form-input" placeholder="Auto-generated" value="{{ old('control_number') }}" readonly>
                    </div>
                    <div class="md:col-span-2 lg:col-span-1">
                        <label class="form-label">Full Name <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                        <input type="text" name="full_name" class="form-input" placeholder="Enter full name" value="{{ old('full_name') }}" required>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
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
                    <div>
                        <label class="form-label">Birth Date <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                        <input type="date" name="birth_date" id="birthDate" class="form-input" value="{{ old('birth_date') }}" required onchange="calculateAge()">
                    </div>
                    <div>
                        <label class="form-label">Month</label>
                        <input type="text" name="month" id="month" class="form-input" required readonly>
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
                        <label class="form-label">Age</label>
                        <input type="number" name="age" id="age" class="form-input" placeholder="Auto-calculated" value="{{ old('age') }}" readonly>
                    </div>
                    <div>
                        <label class="form-label">Contact Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                        <input type="text" name="contact_number" class="form-input" placeholder="e.g. 09171234567" pattern="[0-9]{11}" maxlength="11" value="{{ old('contact_number') }}" required>
                    </div>
                    <div>
                        <label class="form-label">PhilSys Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                        <input type="text" name="philsys_number" class="form-input" placeholder="Enter 12-digit PhilSys number" pattern="[0-9]{12}" maxlength="12" value="{{ old('philsys_number') }}">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="form-label">RRN Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
                        <input type="text" name="rrn_number" class="form-input" placeholder="Enter 29-digit RRN number" pattern="[0-9]{29}" maxlength="29" value="{{ old('rrn_number') }}">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-input" rows="3" placeholder="Enter any additional remarks">{{ old('remarks') }}</textarea>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3 flex justify-end gap-3 mt-4">
                        <button type="button" class="btn" style="height:44px;" onclick="location.href='/admin/senior'">Cancel</button>
                        <button type="button" class="btn primary" style="height:44px;" onclick="confirmSubmit(event)">
                            <i data-lucide="user-plus" style="width:16px;height:16px"></i> Register Senior Citizen
                        </button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>


<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none">@csrf</form>

<script>
    // Input validation - prevent invalid characters
    document.addEventListener('DOMContentLoaded', function() {
        const contactNumberInput = document.querySelector('[name="contact_number"]');
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
        restrictToDigits(philsysNumberInput, 12);
        restrictToDigits(rrnNumberInput, 29);
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

        const f=document.getElementById('registrationForm');
        const get=(n)=>{const el=f.querySelector('[name="'+n+'"]');return el?(el.tagName==='SELECT'?el.options[el.selectedIndex].text:el.value):'-';};
        const v=(n)=>{const val=get(n);return val&&val!=='Select Barangay'&&val!=='Select Sex'?val:'<span style="color:#9CA3AF">Not provided</span>';};
        const summary=`
<div style="text-align:left;font-size:16px;line-height:1.7">
<div style="background:var(--primary);color:#fff;padding:16px 24px;border-radius:10px 10px 0 0;display:flex;align-items:center;gap:12px">
<i data-lucide="user-check" style="width:24px;height:24px"></i>
<strong style="font-size:18px">Registration Summary</strong>
</div>
<div style="border:1px solid var(--border);border-top:none;border-radius:0 0 10px 10px;padding:20px 24px;background:#fff">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Control No.</div><div style="font-weight:500;color:var(--text-primary);font-size:16px">${v('control_number')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Year Applied</div><div style="color:var(--text-primary);font-size:16px">${v('year_applied')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Full Name</div><div style="font-weight:500;color:var(--text-primary);font-size:16px">${v('full_name')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Address</div><div style="color:var(--text-primary);font-size:16px">${v('address')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Barangay</div><div style="color:var(--text-primary);font-size:16px">${v('barangay')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Birth Date</div><div style="color:var(--text-primary);font-size:16px">${v('birth_date')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Month</div><div style="color:var(--text-primary);font-size:16px">${v('month')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Sex</div><div style="color:var(--text-primary);font-size:16px">${v('sex')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Age</div><div style="font-weight:700;color:var(--primary);font-size:18px">${v('age')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Contact Number</div><div style="color:var(--text-primary);font-size:16px">${v('contact_number')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">PhilSys Number</div><div style="color:var(--text-primary);font-size:16px">${v('philsys_number')}</div></div>
<div><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">RRN Number</div><div style="color:var(--text-primary);font-size:16px">${v('rrn_number')}</div></div>
<div style="grid-column:1/-1"><div style="font-weight:600;color:var(--text-secondary);font-size:13px;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px">Remarks</div><div style="color:var(--text-primary);font-size:16px">${f.querySelector('[name="remarks"]').value||'<span style="color:#9CA3AF">None</span>'}</div></div>
</div>
</div>
</div>`;

        Swal.fire({
            title:'<span style="display:flex;align-items:center;gap:8px"><i data-lucide="clipboard-check" style="width:22px;height:22px;color:var(--primary)"></i> Confirm Registration</span>',
            html:summary,
            showCancelButton:true,
            confirmButtonText:'<i data-lucide="check-circle" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px"></i> Confirm & Register',
            cancelButtonText:'<i data-lucide="arrow-left" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px"></i> Go Back',
            confirmButtonColor:'#1A237E',
            cancelButtonColor:'#EF4444',
            background:'#ffffff',
            width:1200,
            customClass:{popup:'rounded-4 shadow-lg'},
            didOpen:()=>{lucide.createIcons();}
        }).then((result)=>{
            if(result.isConfirmed){f.submit();}
        });
        return false;
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
