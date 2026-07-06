<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO – Add Officers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ── Design tokens ── */
        :root {
            --primary:      #1A237E;
            --primary-dark: #121858;
            --secondary:    #6B7280;
            --accent:       #FBC02D;
            --danger:       #D32F2F;
            --violet:       #1A237E;
            --background:   #F8FAFC;
            --cards:        #FFFFFF;
            --text:         #1F2937;
            --muted:        #6B7280;
            --sidebar-bg:   #1A237E;
            --border:       #E5E7EB;
        }

        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
        }

        /* ── Sidebar ── */
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
            font-size: 1.1rem;
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

        /* ── Main content ── */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top-bar ── */
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
        }
        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .breadcrumb-nav { font-size: .8rem; color: var(--muted); margin: 0; }
        .breadcrumb-nav a { color: var(--primary); text-decoration: none; }
        .btn-icon {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 38px; height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
        }
        .btn-icon:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Page body ── */
        .page-body { padding: 2rem; flex: 1; }

        /* ── Minimalist Form & Card ── */
        .form-card {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .4rem;
        }
        .form-control, .form-select {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .6rem .85rem;
            font-size: .875rem;
            color: var(--text);
            outline: none;
            transition: border-color .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: none;
            background: #fff;
        }

        /* ── Dropdown select – distinct look ── */
        .select-dropdown-wrap {
            position: relative;
        }
        .select-dropdown-wrap .form-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: linear-gradient(135deg, #EFF6FF 0%, #F1F5F9 100%);
            border: 1.5px solid #CBD5E1;
            border-left: 3px solid var(--primary);
            padding-right: 2.8rem;
            cursor: pointer;
            transition: all .25s ease;
        }
        .select-dropdown-wrap .form-select:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37, 99, 235, .12);
        }
        .select-dropdown-wrap .form-select:focus {
            border-color: var(--primary);
            border-left: 3px solid var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }
        /* Custom chevron icon */
        .select-dropdown-wrap::after {
            content: '';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid var(--primary);
            pointer-events: none;
            transition: transform .2s ease;
        }
        .select-dropdown-wrap:focus-within::after {
            transform: translateY(-50%) rotate(180deg);
        }

        .select-hint {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .3rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }
        .select-hint i {
            font-size: .65rem;
            color: var(--primary);
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

        /* Minimalist Table */
        .officers-table-wrap {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 1.5rem;
        }
        .gov-table-wrap {
            border: none;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
            overflow: hidden;
            margin-top: 1rem;
        }
        .gov-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
            margin: 0;
        }
        .gov-table thead tr.official-header th {
            background: #FFFFFF;
            color: #475569;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .9rem .75rem;
            border-bottom: 2px solid #E2E8F0;
            text-align: left;
            white-space: nowrap;
        }
        .gov-table tbody tr.brgy-row {
            border-bottom: 1px solid #F1F5F9;
            transition: background .1s ease;
        }
        .gov-table tbody tr.brgy-row:hover { background: #F8FAFC; }
        .gov-table tbody td {
            padding: .85rem .75rem;
            vertical-align: middle;
            border: none;
            color: var(--text);
        }
        .status-badge {
            display: inline-block;
            padding: .2rem .5rem;
            border-radius: 6px;
            font-size: .72rem;
            font-weight: 600;
        }
        .badge-active { background: rgba(20, 184, 166, 0.1); color: var(--secondary); }
        .badge-inactive { background: rgba(220, 38, 38, 0.1); color: var(--danger); }
        
        .avatar-initial {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: .82rem;
        }

        /* ── Password toggle eye ── */
        .pw-input-wrap {
            position: relative;
        }
        .pw-input-wrap .form-control {
            padding-right: 2.6rem;
        }
        .pw-toggle {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #94A3B8;
            font-size: .9rem;
            transition: color .2s ease;
            line-height: 1;
        }
        .pw-toggle:hover {
            color: var(--primary);
        }

        /* ── Password strength feedback ── */
        .pw-feedback {
            margin-top: .5rem;
            padding: .75rem 1rem;
            background: #F8FAFC;
            border: 1px solid var(--border);
            border-radius: 10px;
            transition: all .3s ease;
        }

        .pw-checklist {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .25rem .75rem;
        }
        .pw-checklist li {
            font-size: .74rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: .4rem;
            transition: color .25s ease;
        }
        .pw-checklist li i {
            font-size: .65rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .25s ease;
            background: #E2E8F0;
            color: #94A3B8;
            flex-shrink: 0;
        }
        .pw-checklist li.met {
            color: #16a34a;
            background: #f0fdf4;
            border-radius: 6px;
            padding: 0.15rem 0.35rem;
        }
        .pw-checklist li.met i {
            background: #86efac;
            color: #166534;
        }
        .pw-match-msg {
            font-size: .74rem;
            margin-top: .35rem;
            display: flex;
            align-items: center;
            gap: .3rem;
            transition: all .25s ease;
        }
        .pw-match-msg.match { color: #059669; }
        .pw-match-msg.no-match { color: var(--danger); }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .5s ease both; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .page-body { padding: 1rem; }
            .pw-checklist { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ======================== SIDEBAR ======================== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-building"></i>
        <span>MSWDO Admin</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="/admin/statistics"><i class="fas fa-chart-line"></i> Statistics</a></li>
        <li><a href="#"><i class="fas fa-hand-holding-usd"></i> Financial Assistance</a></li>
        <!-- <li><a href="#"><i class="fas fa-file-alt"></i> Social Case Study</a></li> -->
        <li><a href="#"><i class="fas fa-user-friends"></i> Senior Citizen</a></li>
        <li><a href="/admin/add-officers" class="active"><i class="fas fa-user-shield"></i> Add Officers</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- ======================== MAIN ======================== -->
<div class="main-content">

    <!-- Top-bar -->
    <nav class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-icon d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <p class="page-title">Officers Directory</p>
                <p class="breadcrumb-nav">
                    <a href="/admin/dashboard">Dashboard</a> / Add Officers
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div id="currentDateTime" class="text-muted small d-none d-md-block"></div>
            <button class="btn-icon" title="Refresh" onclick="location.reload()">
                <i class="fas fa-rotate-right"></i>
            </button>
        </div>
    </nav>

    <!-- Page Body -->
    <div class="page-body">

        <!-- Form Card -->
        <div class="form-card fade-up">
            <h2 class="h5 fw-bold mb-1">Create Officer Account</h2>
            <p class="text-muted small mb-4">Register a new social worker or administrator to access the MSWDO platform.</p>

            @if(session('success'))
                <div class="alert alert-success" id="successAlert">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" id="errorAlert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.officers.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email address" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role / Assignment</label>
                        <div class="select-dropdown-wrap">
                            <select class="form-select" name="role" id="roleSelect" required>
                                <option value="" disabled selected>▼ Select Role / Assignment</option>
                                <option value="Financial assistance officer" {{ old('role') == 'Financial assistance officer' ? 'selected' : '' }}>Financial assistance officer</option>
                                <option value="Senior Citizen officer" {{ old('role') == 'Senior Citizen officer' ? 'selected' : '' }}>Senior Citizen officer</option>
                            </select>
                        </div>
                        <p class="select-hint"><i class="fas fa-info-circle"></i> Click to open the dropdown and choose a role</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 0917XXXXXXX" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" id="passwordInput" name="password" class="form-control" placeholder="Create password" required oninput="checkPassword()">
                        <div id="pwFeedback" class="mt-2" style="display:none;">
                            <ul class="pw-checklist">
                                <li id="reqLength"><i class="fas fa-circle"></i> At least 8 characters</li>
                                <li id="reqUpper"><i class="fas fa-circle"></i> One uppercase letter</li>
                                <li id="reqLower"><i class="fas fa-circle"></i> One lowercase letter</li>
                                <li id="reqNumber"><i class="fas fa-circle"></i> One number</li>
                                <li id="reqSpecial"><i class="fas fa-circle"></i> One special character</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" id="confirmPasswordInput" name="password_confirmation" class="form-control" placeholder="Confirm password" required oninput="checkPassword()">
                        <div id="pwMatchMsg" class="pw-match-msg" style="display:none;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="button" class="btn btn-light me-2 border" onclick="location.href='/admin/dashboard'">Cancel</button>
                        <button type="submit" class="btn-submit">Add Officer</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Directory Table -->
        <div class="officers-table-wrap fade-up" style="animation-delay: 0.1s;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="h6 fw-bold mb-1">MSWDO Active Officers</h3>
                    <p class="text-muted small mb-0">Registered system accounts and status indicators.</p>
                </div>
                <div style="position:relative;">
                    <input type="text" class="form-control form-control-sm" placeholder="Search officer..." style="width:200px; padding-left: 2rem;" oninput="filterTable(this.value)">
                    <i class="fas fa-search text-muted" style="position:absolute; left: .7rem; top:50%; transform:translateY(-50%); font-size: .8rem;"></i>
                </div>
            </div>

            <div class="gov-table-wrap">
                <table class="gov-table" id="officersTable">
                    <thead>
                        <tr class="official-header">
                            <th>Officer</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="officersTableBody">
                        @forelse($officers as $officer)
                            <tr class="brgy-row">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-initial">{{ strtoupper(substr($officer->name ?? 'O', 0, 2)) }}</div>
                                        <span class="fw-semibold">{{ $officer->name ?? 'Officer' }}</span>
                                    </div>
                                </td>
                                <td>{{ $officer->email ?? '-' }}</td>
                                <td>{{ $officer->role ?? '-' }}</td>
                                <td>{{ $officer->phone ?? '-' }}</td>
                                <td><span class="status-badge badge-active">Active</span></td>
                                <td>
                                    <button class="btn btn-sm btn-link text-primary p-0 me-2" type="button"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-link text-danger p-0" type="button"><i class="fas fa-ban"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No officers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /page-body -->
</div><!-- /main-content -->

<!-- ======================== SCRIPTS ======================== -->
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

    function filterTable(query) {
        const q = query.toLowerCase().trim();
        const rows = document.querySelectorAll('#officersTableBody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }
    // ── Password strength checker ──
    function checkPassword() {
        const pw = document.getElementById('passwordInput').value;
        const feedback = document.getElementById('pwFeedback');

        if (pw.length === 0) {
            feedback.style.display = 'none';
            return;
        }
        feedback.style.display = 'block';

        const checks = {
            length:  pw.length >= 8,
            upper:   /[A-Z]/.test(pw),
            lower:   /[a-z]/.test(pw),
            number:  /[0-9]/.test(pw),
            special: /[^A-Za-z0-9]/.test(pw)
        };

        toggleReq('reqLength',  checks.length);
        toggleReq('reqUpper',   checks.upper);
        toggleReq('reqLower',   checks.lower);
        toggleReq('reqNumber',  checks.number);
        toggleReq('reqSpecial', checks.special);

        if (document.getElementById('confirmPasswordInput').value.length > 0) {
            checkPasswordMatch();
        }
    }

    function toggleReq(id, met) {
        const el = document.getElementById(id);
        const icon = el.querySelector('i');
        if (met) {
            el.classList.add('met');
            icon.className = 'fas fa-check';
        } else {
            el.classList.remove('met');
            icon.className = 'fas fa-circle';
        }
    }

    function checkPasswordMatch() {
        const pw = document.getElementById('passwordInput').value;
        const cpw = document.getElementById('confirmPasswordInput').value;
        const msg = document.getElementById('pwMatchMsg');

        if (cpw.length === 0) {
            msg.style.display = 'none';
            return;
        }
        msg.style.display = 'flex';

        if (pw === cpw) {
            msg.className = 'pw-match-msg match';
            msg.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
        } else {
            msg.className = 'pw-match-msg no-match';
            msg.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
        }
    }

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
            btn.title = 'Hide password';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
            btn.title = 'Show password';
        }
    }

    // Auto-hide success alert after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 500);
            }, 3000);
        }

        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(function() {
                errorAlert.style.transition = 'opacity 0.5s ease';
                errorAlert.style.opacity = '0';
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 500);
            }, 3000);
        }
    });
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
