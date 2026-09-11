<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Between Birthday Benefit - {{ $senior->full_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;
            --primary-hover:#121858;
            --primary-dark:#121858;
            --sidebar-bg:#1A237E;
            --accent-yellow:#FBC02D;
            --background:#F1F5F9;
            --surface:#FFFFFF;
            --border:#E5E7EB;
            --text-primary:#111827;
            --text-secondary:#6B7280;
            --text-muted:#9CA3AF;
            --success:#16A34A;
            --success-bg:#ECFDF5;
            --danger:#DC2626;
            --danger-bg:#FEF2F2;
            --info:#3B82F6;
            --info-bg:#EEF2FF;
            --purple:#7C3AED;
            --purple-bg:#F3E8FF;
            --icon-blue:#3B82F6;
            --icon-green:#16A34A;
            --icon-purple:#7C3AED;
            --sidebar-width:260px;
            --content-padding:32px;
            --shadow:0 10px 30px rgba(15,23,42,.08);
            --shadow-hover:0 20px 40px rgba(15,23,42,.12);
            --font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        .page-header{margin-bottom:24px;}
        .page-header h1{font-size:28px;font-weight:700;margin:0 0 8px 0;color:var(--text-primary);}
        .page-header p{margin:0;color:var(--text-secondary);}
        .analytics-card{background:var(--surface);border-radius:16px;padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);margin-bottom:20px;}
        .quick-actions-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;}
        .quick-action-card{background:var(--surface);border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:var(--shadow);border:1px solid var(--border);text-decoration:none;color:inherit;transition:all .3s ease;}
        .quick-action-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
        .quick-action-icon{width:48px;height:48px;border-radius:12px;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .quick-action-title{font-weight:600;font-size:16px;margin-bottom:4px;}
        .quick-action-description{font-size:13px;color:var(--text-secondary);}
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'in-between', 'mobileSubtitle' => 'Senior Card'])
    <div class="main">
        <div class="main-scroll">
            <div class="page-header">
                <h1>In-Between Birthday Benefit - {{ $senior->full_name }}</h1>
                <p class="text-gray-600">Senior Citizen Profile: {{ $senior->control_number ?? $senior->senior_id_number }}</p>
            </div>

        <!-- Benefit Card -->
        <div class="analytics-card" style="margin-bottom:20px;">
            <div class="flex items-center justify-between mb-4">
                <h3><i data-lucide="gift" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-purple)"></i>In-Between Birthday Benefit Status</h3>
            </div>
            
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Current Age</div>
                    <div style="font-size:24px;font-weight:700;color:var(--text-primary);">{{ $senior->age }}</div>
                </div>
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Eligibility Group</div>
                    <div style="font-size:24px;font-weight:700;color:var(--text-primary);">{{ $interval ?? 'N/A' }}</div>
                </div>
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Benefit Amount</div>
                    <div style="font-size:24px;font-weight:700;color:var(--text-primary);">₱1,000</div>
                </div>
                <div style="background:{{ $isEligible ? 'var(--success-bg)' : 'var(--info-bg)' }};border-radius:12px;padding:16px;">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Status</div>
                    <div style="font-size:24px;font-weight:700;color:{{ $isEligible ? 'var(--success)' : 'var(--info)' }};">
                        {{ $isEligible ? 'Eligible' : ($hasClaimed ? 'Already Claimed' : 'Not Eligible') }}
                    </div>
                </div>
            </div>
        </div>

        @if($hasClaimed && $currentClaim)
        <!-- Current Claim Details -->
        <div class="analytics-card" style="margin-bottom:20px;">
            <div class="flex items-center justify-between mb-4">
                <h3><i data-lucide="check-circle" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-green)"></i>Current Claim Details</h3>
            </div>
            
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Claim Date</div>
                    <div style="font-size:16px;font-weight:600;color:var(--text-primary);">{{ $currentClaim->application_date->format('M d, Y') }}</div>
                </div>
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Amount</div>
                    <div style="font-size:16px;font-weight:600;color:var(--text-primary);">₱{{ number_format($currentClaim->amount, 2) }}</div>
                </div>
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Reference No.</div>
                    <div style="font-size:16px;font-weight:600;color:var(--text-primary);font-family:monospace;">{{ $currentClaim->reference_number }}</div>
                </div>
                <div style="background:var(--background);border-radius:12px;padding:16px;border:1px solid var(--border);">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">Processed By</div>
                    <div style="font-size:16px;font-weight:600;color:var(--text-primary);">{{ $currentClaim->processedBy->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        @endif

        @if(count($previousClaims) > 0)
        <!-- Previous Claims -->
        <div class="analytics-card" style="margin-bottom:20px;">
            <div class="flex items-center justify-between mb-4">
                <h3><i data-lucide="history" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Previous Claims</h3>
            </div>
            
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--background);">
                            <th style="padding:12px;text-align:left;font-weight:600;color:var(--text-secondary);border-bottom:1px solid var(--border);">Interval</th>
                            <th style="padding:12px;text-align:left;font-weight:600;color:var(--text-secondary);border-bottom:1px solid var(--border);">Amount</th>
                            <th style="padding:12px;text-align:left;font-weight:600;color:var(--text-secondary);border-bottom:1px solid var(--border);">Claim Date</th>
                            <th style="padding:12px;text-align:left;font-weight:600;color:var(--text-secondary);border-bottom:1px solid var(--border);">Reference No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previousClaims as $claim)
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:12px;">{{ $claim['interval'] }}</td>
                                <td style="padding:12px;">₱{{ number_format($claim['amount'], 2) }}</td>
                                <td style="padding:12px;">{{ $claim['claim_date'] ? \Carbon\Carbon::parse($claim['claim_date'])->format('M d, Y') : 'N/A' }}</td>
                                <td style="padding:12px;font-family:monospace;">{{ $claim['reference_number'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="quick-actions-grid">
            @if($isEligible)
                <button onclick="processClaim({{ $senior->id }})" class="quick-action-card" style="cursor:pointer;">
                    <div class="quick-action-icon">
                        <i data-lucide="gift"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Process Birthday Cash Gift</div>
                        <div class="quick-action-description">Claim in-between benefit</div>
                    </div>
                </button>
            @endif
            <a href="/admin/senior/in-between/eligibility-list" class="quick-action-card">
                <div class="quick-action-icon">
                    <i data-lucide="list"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Back to Eligibility List</div>
                    <div class="quick-action-description">View all eligible seniors</div>
                </div>
            </a>
            <a href="/admin/senior/in-between/history" class="quick-action-card">
                <div class="quick-action-icon">
                    <i data-lucide="history"></i>
                </div>
                <div class="quick-action-content">
                    <div class="quick-action-title">Benefit History</div>
                    <div class="quick-action-description">View all transactions</div>
                </div>
            </a>
        </div>
        </div>
    </div>
</div>

<script>
    function processClaim(seniorId) {
        Swal.fire({
            title: 'Process In-Between Birthday Cash Gift',
            html: `
                <div style="text-align:left;">
                    <p><strong>Senior ID:</strong> {{ $senior->id }}</p>
                    <p><strong>Name:</strong> {{ $senior->full_name }}</p>
                    <p><strong>Age:</strong> {{ $senior->age }}</p>
                    <p><strong>Eligibility Interval:</strong> {{ $interval ?? 'N/A' }}</p>
                    <p><strong>Amount:</strong> ₱1,000</p>
                    <p><strong>Previous Claim:</strong> {{ count($previousClaims) > 0 ? 'Yes' : 'None' }}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Process Claim',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/admin/senior/in-between/process-claim/${seniorId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ confirmation: true })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message);
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(error);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Claim processed successfully',
                    icon: 'success'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    lucide.createIcons();
</script>
</body>
</html>
