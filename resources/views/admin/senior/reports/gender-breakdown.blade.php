<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gender Breakdown Report</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={corePlugins:{preflight:false}}</script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root{--primary:#1A237E;--primary-hover:#121858;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;}
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);height:44px;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);}
        .form-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);padding:32px;}
        .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;text-align:center;}
        .stat-value{font-size:48px;font-weight:700;color:var(--primary);}
        .stat-label{font-size:16px;color:var(--text-secondary);margin-top:8px;}
        .gender-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:6;}
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'reports', 'mobileSubtitle' => 'Gender Breakdown Report'])

    <div class="main">
        <div class="main-scroll">
        <div class="form-card">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold mb-1">Gender Breakdown Report</h2>
                    <p class="text-sm" style="color:var(--text-secondary)">Distribution of senior citizens by gender</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="btn primary">
                        <i data-lucide="printer" style="width:16px;height:16px"></i> Print Report
                    </button>
                    <button onclick="history.back()" class="btn">
                        <i data-lucide="arrow-left" style="width:16px;height:16px"></i> Back
                    </button>
                </div>
            </div>

            <div class="gender-grid">
                @php $total = $genderData->sum('count'); @endphp
                @foreach($genderData as $data)
                <div class="stat-card">
                    <div class="stat-value">{{ $data->count }}</div>
                    <div class="stat-label">{{ $data->sex }}</div>
                    <div class="text-sm mt-2" style="color:var(--text-secondary)">
                        {{ $total > 0 ? round(($data->count / $total) * 100, 1) : 0 }}%
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t" style="border-color:var(--border)">
                <div class="text-sm" style="color:var(--text-secondary)">
                    <strong>Total Active Seniors:</strong> {{ $total }}
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>