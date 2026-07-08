<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --secondary: #6B7280;
            --accent: #FBC02D;
            --danger: #D32F2F;
            --background: #F8FAFC;
            --cards: #FFFFFF;
            --text: #1F2937;
            --muted: #6B7280;
            --sidebar-bg: #1A237E;
            --border: #E5E7EB;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            background: var(--sidebar-bg);
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sidebar-brand i { font-size: 1.3rem; color: var(--accent); }
        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 1rem 0;
            flex: 1;
        }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a {
            color: rgba(255,255,255,.75);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            font-size: .9rem;
            border-left: 3px solid transparent;
            transition: all .2s ease;
        }
        .sidebar-menu a:hover { 
            background: rgba(255,255,255,.1); 
            color: var(--accent); 
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,.1);
            color: var(--accent);
            border-left-color: var(--accent);
        }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        /* Main content */
        .main-content {
            margin-left: 260px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Top-bar */
        .top-navbar {
            background-color: var(--cards);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .breadcrumb-nav { font-size: .8rem; color: var(--muted); margin: 0; }
        .breadcrumb-nav a { color: var(--primary); text-decoration: none; }

        /* Page body */
        .page-body { padding: 2rem; flex: 1; overflow: hidden; display: flex; flex-direction: column; }

        /* Form & Card */
        .form-card {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 2rem;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }
        .form-label {
            font-size: .95rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: .5rem;
            letter-spacing: 0.3px;
        }
        .form-control, .form-select {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .75rem 1rem;
            font-size: .95rem;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            background-size: 1rem;
            padding-right: 2.5rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
            background: #fff;
        }

        .btn-submit {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .65rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            transition: all .2s;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .page-body { padding: 1rem; }
        }
    </style>
</head>
<body>

<!-- ======================== SIDEBAR ======================== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-user-friends"></i>
        <span>Senior Citizen</span>
    </div>
    <ul class="sidebar-menu">
        <!-- <li><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li> -->
        <li><a href="/admin/senior"><i class="fas fa-user-friends"></i> Dashboard</a></li>
        <li><a href="/admin/senior/registration" class="active"><i class="fas fa-user-plus"></i> Registration</a></li>
        <li><a href="/admin/senior/masterlist"><i class="fas fa-list"></i> Masterlist</a></li>
        <li><a href="/admin/senior/birthdays"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
        <li><a href="/admin/senior/birthday-payouts"><i class="fas fa-money-bill-wave"></i> Birthday Payouts</a></li>
        <li><a href="/admin/senior/birthday-payouts/history"><i class="fas fa-history"></i> Payout History</a></li>
        <li><a href="/admin/senior/statistics"><i class="fas fa-chart-bar"></i> Statistics</a></li>
        <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="/admin/senior/archive"><i class="fas fa-archive"></i> Archive</a></li>
        
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- ======================== MAIN ======================== -->
<div class="main-content">

    <!-- Top-bar -->
    <nav class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <p class="page-title">Senior Citizen Registration</p>
                <p class="breadcrumb-nav">
                    <a href="/admin/senior">Dashboard</a> / Registration
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div id="currentDateTime" class="text-muted small d-none d-md-block"></div>
            <div style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%; margin-left: 1rem;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
        </div>
    </nav>

    <!-- Page Body -->
    <div class="page-body">

        <!-- Form Card -->
        <div class="form-card">
            <h2 class="h5 fw-bold mb-1">Register Senior Citizen</h2>
            <p class="text-muted small mb-4">Fill in the details below to register a new senior citizen.</p>

            <form method="POST" action="{{ route('admin.senior.registration.store') }}" onsubmit="return validateAge(event)">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Year Applied</label>
                        <input type="number" name="year_applied" id="year_applied" class="form-control" placeholder="e.g. 2026" value="{{ old('year_applied') ?? date('Y') }}" required onchange="updateControlNumber()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Control Number</label>
                        <input type="text" name="control_number" id="controlNumber" class="form-control" placeholder="Auto-generated" value="{{ old('control_number') }}" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Enter full name" value="{{ old('full_name') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Enter complete address" value="{{ old('address') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Barangay</label>
                        <select class="form-select" name="barangay" id="barangay" required onchange="updateControlNumber()">
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
                    <div class="col-md-6">
                        <label class="form-label">Birth Date</label>
                        <input type="date" name="birth_date" id="birthDate" class="form-control" value="{{ old('birth_date') }}" required onchange="calculateAge()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Month</label>
                        <input type="text" name="month" id="month" class="form-control" required readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sex</label>
                        <select class="form-select" name="sex" required>
                            <option value="">Select Sex</option>
                            <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" id="age" class="form-control" placeholder="Auto-calculated" value="{{ old('age') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" placeholder="e.g. 0917XXXXXXX" value="{{ old('contact_number') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PhilSys Number</label>
                        <input type="text" name="philsys_number" class="form-control" placeholder="Enter PhilSys number" value="{{ old('philsys_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RRN Number</label>
                        <input type="text" name="rrn_number" class="form-control" placeholder="Enter RRN number" value="{{ old('rrn_number') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Enter any additional remarks">{{ old('remarks') }}</textarea>
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="button" class="btn btn-light me-2 border" onclick="location.href='/admin/senior'">Cancel</button>
                        <button type="submit" class="btn-submit">Register Senior Citizen</button>
                    </div>
                </div>
            </form>
        </div>

    </div><!-- /page-body -->
</div><!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }

    function updateDateTime() {
        const now = new Date();
        const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' };
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = now.toLocaleDateString('en-PH', opts);
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Show success popup if senior was just created
    @if($seniorCreated ?? false)
        Swal.fire({
            title: 'Success!',
            text: 'Senior citizen registered successfully.',
            icon: 'success',
            confirmButtonColor: '#1A237E',
            confirmButtonText: 'OK',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        });
    @endif

    // Show error popup if there are validation errors
    @if($errors->any())
        Swal.fire({
            title: 'Error!',
            text: '{{ $errors->first() }}',
            icon: 'error',
            confirmButtonColor: '#1A237E',
            confirmButtonText: 'OK',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        });
    @endif

    function calculateAge() {
        const birthDate = document.getElementById('birthDate').value;
        const ageField = document.getElementById('age');
        const monthField = document.getElementById('month');
        
        if (birthDate) {
            const birth = new Date(birthDate);
            const today = new Date();
            
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            ageField.value = age;

            // Auto-populate month based on birth date
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const birthMonth = months[birth.getMonth()];
            monthField.value = birthMonth;
        } else {
            ageField.value = '';
            monthField.value = '';
        }
    }

    function validateAge(event) {
        const age = parseInt(document.getElementById('age').value);
        
        if (!age || age < 60) {
            event.preventDefault();
            Swal.fire({
                title: 'Age Requirement',
                text: 'The age field must be at least 60 to register as a senior citizen.',
                icon: 'warning',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
            return false;
        }
        
        return true;
    }

    function updateControlNumber() {
        const barangay = document.getElementById('barangay').value;
        const year = document.getElementById('year_applied').value || new Date().getFullYear();
        const controlNumberField = document.getElementById('controlNumber');
        
        // Get barangay sequences and codes from PHP
        const barangaySequences = {!! json_encode($barangaySequences ?? []) !!};
        const barangayCodes = {!! json_encode($barangayCodes ?? []) !!};
        
        if (barangay && barangaySequences[barangay] && barangayCodes[barangay]) {
            const barangayCode = barangayCodes[barangay];
            // Get the next sequence for this specific barangay
            const nextSequence = barangaySequences[barangay];
            // Format sequence as 6-digit number with leading zeros
            const sequence = String(nextSequence).padStart(6, '0');
            controlNumberField.value = `SC-${barangayCode}-${year}-${sequence}`;
        } else {
            controlNumberField.value = '';
        }
    }
</script>

<!-- Hidden form for secure POST logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
</body>
</html>
