<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Reports</title>
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
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}
        .form-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);padding:32px;overflow:visible;}
        .report-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;box-shadow:var(--shadow);transition:all .2s ease;}
        .report-card:hover{box-shadow:0 15px 40px rgba(15,23,42,.12);transform:translateY(-2px);}
        .report-card h3{font-size:16px;font-weight:600;color:var(--primary);margin-bottom:16px;display:flex;align-items:center;gap:8px;}
        .report-card .btn{width:100%;justify-content:center;margin-bottom:8px;}
        .report-card .btn:last-child{margin-bottom:0;}
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'reports', 'mobileSubtitle' => 'Senior Citizen Reports'])

    <!-- Main Content -->
    <div class="main">
        <div class="main-scroll">
        <div class="form-card">
            <h2 class="text-lg font-bold mb-1">Senior Citizen Reports</h2>
            <p class="text-sm mb-6" style="color:var(--text-secondary)">Generate and export various reports from the senior citizen database.</p>

            <!-- Report Categories -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Masterlist Reports -->
                <div class="report-card">
                    <h3>
                        <i data-lucide="list" style="width:18px;height:18px;"></i>
                        Masterlist Reports
                    </h3>
                    <div class="flex flex-col gap-2">
                        <a href="/admin/senior/masterlist" class="btn">View Masterlist</a>
                        <a href="/admin/senior/export-pdf" target="_blank" class="btn primary">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Export Complete Masterlist (PDF)
                        </a>
                    </div>
                </div>

                <!-- Birthday Reports -->
                <div class="report-card">
                    <h3>
                        <i data-lucide="cake" style="width:18px;height:18px;"></i>
                        Birthday Reports
                    </h3>
                    <div class="flex flex-col gap-2">
                        <a href="/admin/senior/birthdays" class="btn">Birthday Beneficiaries</a>
                        <a href="/admin/senior/payouts-history" class="btn">Payout History</a>
                        <a href="/admin/senior/birthdays/export/pdf" target="_blank" class="btn primary">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Export Birthday Report (PDF)
                        </a>
                    </div>
                </div>

                <!-- ID Card Reports -->
                <div class="report-card">
                    <h3>
                        <i data-lucide="id-card" style="width:18px;height:18px;"></i>
                        ID Card Generation
                    </h3>
                    <div class="flex flex-col gap-2">
                        <a href="/admin/senior/bulk-id-cards" class="btn">Bulk ID Cards</a>
                        <button onclick="generateIndividualID()" class="btn primary">
                            <i data-lucide="user-plus" style="width:16px;height:16px"></i> Generate Individual ID
                        </button>
                    </div>
                </div>

                <!-- Statistics Reports -->
                <div class="report-card">
                    <h3>
                        <i data-lucide="bar-chart-3" style="width:18px;height:18px;"></i>
                        Statistics & Analytics
                    </h3>
                    <div class="flex flex-col gap-2">
                        <a href="/admin/senior/statistics" class="btn">View Statistics</a>
                        <button onclick="exportStatisticsPDF()" class="btn primary">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Export Statistics (PDF)
                        </button>
                    </div>
                </div>

                <!-- Archive Reports -->
                <div class="report-card">
                    <h3>
                        <i data-lucide="archive" style="width:18px;height:18px;"></i>
                        Archive Reports
                    </h3>
                    <div class="flex flex-col gap-2">
                        <a href="/admin/senior/archive" class="btn">View Archive</a>
                        <button onclick="exportArchivePDF()" class="btn primary">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Export Archive Report (PDF)
                        </button>
                    </div>
                </div>

                <!-- Demographic Reports -->
                <div class="report-card">
                    <h3>
                        <i data-lucide="users" style="width:18px;height:18px;"></i>
                        Demographic Reports
                    </h3>
                    <div class="flex flex-col gap-2">
                        <button onclick="exportAgeDistribution()" class="btn">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Age Distribution Report
                        </button>
                        <button onclick="exportBarangayReport()" class="btn">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Barangay Distribution Report
                        </button>
                        <button onclick="exportGenderReport()" class="btn">
                            <i data-lucide="download" style="width:16px;height:16px"></i> Gender Breakdown Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none">@csrf</form>

<script>
    function exportStatisticsPDF() {
        window.open('/admin/senior/statistics', '_blank');
    }

    function exportArchivePDF() {
        Swal.fire({title:'Archive Export',text:'To export archived records, please visit the Archive page and use the export function there.',icon:'info',confirmButtonColor:'#1A237E',confirmButtonText:'OK',background:'#ffffff',customClass:{popup:'rounded-4 shadow-lg'}});
    }

    function exportAgeDistribution() {
        window.open('/admin/senior/reports/age-distribution', '_blank');
    }

    function exportBarangayReport() {
        window.open('/admin/senior/reports/barangay-distribution', '_blank');
    }

    function exportGenderReport() {
        window.open('/admin/senior/reports/gender-breakdown', '_blank');
    }

    function generateIndividualID() {
        window.location.href = '/admin/senior/id-card';
    }

    lucide.createIcons();
</script>
</body>
</html>