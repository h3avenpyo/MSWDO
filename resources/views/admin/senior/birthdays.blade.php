<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Birthday Beneficiaries</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--purple:#7C3AED;--purple-bg:#F3E8FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);min-height:44px;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}

        /* ── Badges ── */
        .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;}

        /* ── Filter Section ── */
        .filter-section{background:var(--surface);border-radius:16px;border:1px solid var(--border);padding:20px;box-shadow:var(--shadow);}
        .section-spacing{margin-bottom:28px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;}
        .filter-select{width:100%;height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        #filterGrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;align-items:start;}

        /* ── Budget Overview ── */
        .budget-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.2);}
        .budget-stat{background:rgba(255,255,255,0.1);border-radius:8px;padding:10px;min-width:0;}
        .budget-stat-label{font-size:11px;opacity:0.9;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .budget-stat-value{font-size:20px;font-weight:700;}

        /* ── Barangay Card Grid ── */
        .barangay-cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,350px),1fr));gap:24px;width:100%;max-width:100%;}
        .brgy-card{background:var(--background);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;min-height:200px;height:100%;min-width:0;width:100%;max-width:100%;}
        .brgy-card-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px;min-width:0;}
        .brgy-card-head strong{font-size:15px;color:var(--text-primary);min-width:0;overflow-wrap:anywhere;word-break:break-word;}
        .brgy-count-badge{flex-shrink:0;white-space:nowrap;}
        .brgy-card-body{display:flex;flex-direction:column;gap:6px;font-size:13px;flex:1;min-width:0;}
        .brgy-total-row{display:flex;justify-content:space-between;align-items:center;gap:8px;min-width:0;}
        .brgy-total-row strong{white-space:nowrap;flex-shrink:0;}
        .celebrants-wrap{margin-top:8px;min-width:0;}
        .celebrants-table-scroll{overflow-x:auto;width:100%;max-width:100%;-webkit-overflow-scrolling:touch;}
        .celebrants-table{width:100%;border-collapse:collapse;font-size:14px;}
        .celebrants-table th{padding:10px 12px;text-align:left;background:#e5e7eb;border-bottom:2px solid #9ca3af;color:#374151;font-weight:700;font-size:13px;white-space:nowrap;}
        .celebrants-table td{padding:10px 12px;vertical-align:middle;}
        .celebrants-table td:first-child{color:var(--text-secondary);white-space:nowrap;}
        .celebrants-table td:last-child{color:var(--text-primary);overflow-wrap:anywhere;word-break:break-word;min-width:0;}
        .brgy-card-actions{margin-top:auto;padding-top:16px;display:flex;gap:10px;min-width:0;}
        .brgy-card-actions .btn,.brgy-card-actions > div{flex:1;min-width:0;}
        .view-all-btn{background:none;border:none;color:var(--primary);cursor:pointer;font-size:12px;margin-top:8px;padding:0;}

        /* ── Modal ── */
        .modal-overlay{display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px;}
        .modal-overlay.show{display:flex;}
        .modal-box{background:var(--surface);border-radius:16px;width:100%;max-width:800px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);}
        .modal-header-bar{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--border);background:var(--primary);color:white;border-radius:16px 16px 0 0;}
        .modal-header-bar h4{font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px;margin:0;}
        .modal-header-bar h4 svg{width:20px;height:20px;}
        .modal-close-btn{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:white;cursor:pointer;transition:all .15s ease;}
        .modal-close-btn:hover{background:rgba(255,255,255,.25);}
        .modal-close-btn svg{width:16px;height:16px;}
        .modal-body-scroll{padding:24px;overflow-y:auto;max-height:60vh;}
        .modal-body-scroll table{width:100%;border-collapse:collapse;}
        .modal-body-scroll th{padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);white-space:nowrap;}
        .modal-body-scroll td{padding:16px;border-bottom:1px solid var(--border);font-size:15px;}

        .spinner{width:40px;height:40px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .6s linear infinite;margin:0 auto;}
        @keyframes spin{to{transform:rotate(360deg);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Desktop (1400px+): 3 cards per row ── */
        @media (min-width:1400px){
            .barangay-cards-grid{gap:28px;}
            .modal-overlay{padding:24px !important;}
            .modal-box{max-width:900px !important;max-height:85vh !important;}
            .modal-header-bar{padding:24px 28px !important;}
            .modal-header-bar h4{font-size:18px !important;}
            .modal-header-bar h4 svg{width:24px !important;height:24px !important;}
            .modal-body-scroll{padding:28px !important;max-height:70vh !important;}
            .modal-body-scroll th{font-size:15px !important;padding:16px !important;}
            .modal-body-scroll td{font-size:15px !important;padding:16px !important;}
        }

        /* ── Laptop (1200–1399px): 3 cards per row, slightly smaller gaps ── */
        @media (min-width:1200px) and (max-width:1399px){
            .barangay-cards-grid{gap:20px;}
            .modal-overlay{padding:20px !important;}
            .modal-box{max-width:850px !important;max-height:82vh !important;}
            .modal-header-bar{padding:20px 24px !important;}
            .modal-header-bar h4{font-size:17px !important;}
            .modal-header-bar h4 svg{width:22px !important;height:22px !important;}
            .modal-body-scroll{padding:24px !important;max-height:68vh !important;}
            .modal-body-scroll th{font-size:14px !important;padding:14px !important;}
            .modal-body-scroll td{font-size:14px !important;padding:14px !important;}
        }

        /* ── Desktop / Laptop shared typography (keeps existing look) ── */
        @media (min-width:1200px){
            header{margin-bottom:0.75rem !important;}
            #filterGrid{grid-template-columns:160px 110px !important;}
            .filter-section h2{font-size:22px !important;}
            .filter-section p{font-size:15px !important;}
            .filter-section div[style*="font-size:28px"]{font-size:36px !important;}
            .filter-section div[style*="font-size:12px"]{font-size:14px !important;}
            .filter-section div[style*="font-size:11px"]{font-size:13px !important;}
            .filter-section div[style*="font-size:20px"]{font-size:26px !important;}
            .filter-section button{font-size:14px !important;padding:12px 20px !important;}
            .filter-section h3{font-size:18px !important;}
            .filter-section label{font-size:14px !important;}
            .filter-section select{font-size:14px !important;}
            .filter-section div[style*="font-size:13px"]{font-size:15px !important;}
            .filter-section strong{font-size:16px !important;}
            .brgy-card{padding:20px !important;min-height:220px !important;}
            .brgy-card strong{font-size:17px !important;}
            .brgy-card .badge{font-size:14px !important;padding:6px 12px !important;}
            .brgy-card div[style*="font-size:14px"]{font-size:16px !important;}
            .brgy-card button{font-size:14px !important;padding:12px 18px !important;}
            .brgy-card div[style*="font-size:12px"]{font-size:14px !important;}
            .brgy-card table{table-layout:fixed;width:100%;}
            .brgy-card table th,.brgy-card table td{height:44px !important;padding:10px 12px !important;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:middle;}
            .brgy-card table th:first-child,.brgy-card table td:first-child{width:42%;}
        }

        /* ── Tablet (768–1199px): 2 cards per row, compact header ── */
        @media (min-width:768px) and (max-width:1199px){
            .section-spacing{margin-bottom:20px;}
            .filter-section{padding:16px !important;}
            .barangay-cards-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;}
            .brgy-card{padding:14px !important;}
            .celebrants-table{font-size:13px !important;}
            .celebrants-table th{font-size:12px !important;padding:8px 10px !important;}
            .celebrants-table td{padding:8px 10px !important;}
            header{margin-bottom:0.75rem !important;}
            header h1{font-size:20px !important;}
            #currentDateTime{font-size:12px !important;}
            .modal-overlay{padding:16px !important;}
            .modal-box{max-width:700px !important;max-height:80vh !important;}
            .modal-header-bar{padding:16px 20px !important;}
            .modal-header-bar h4{font-size:16px !important;}
            .modal-header-bar h4 svg{width:20px !important;height:20px !important;}
            .modal-body-scroll{padding:20px !important;max-height:65vh !important;}
            .modal-body-scroll th{font-size:13px !important;padding:12px !important;}
            .modal-body-scroll td{font-size:13px !important;padding:12px !important;}
        }

        /* ── Mobile (<768px): 1 card per row, stacked filters ── */
        @media (max-width:767px){
            .section-spacing{margin-bottom:16px;}
            .filter-section{padding:14px !important;}
            .barangay-cards-grid{grid-template-columns:1fr;gap:14px;}
            .brgy-card{padding:14px !important;}
            .brgy-card-actions{padding-top:14px;}
            .brgy-card-actions .btn,.brgy-card-actions > div{min-height:44px;}
            #filterGrid{grid-template-columns:1fr !important;}
            header{margin-bottom:0.75rem !important;}
            .modal-overlay{padding:12px !important;}
            .modal-box{max-width:100% !important;border-radius:12px !important;max-height:90vh !important;}
            .modal-header-bar{padding:14px 16px !important;border-radius:12px 12px 0 0 !important;}
            .modal-header-bar h4{font-size:14px !important;}
            .modal-header-bar h4 svg{width:18px !important;height:18px !important;}
            .modal-body-scroll{padding:16px !important;max-height:75vh !important;}
            .modal-body-scroll th{font-size:12px !important;padding:10px !important;}
            .modal-body-scroll td{font-size:12px !important;padding:10px !important;}
        }

        /* ── Small mobile (<480px): full-width cards, compact badge ── */
        @media (max-width:479px){
            .barangay-cards-grid{gap:12px;}
            .brgy-card{padding:12px !important;}
            .brgy-card-head strong{font-size:14px !important;}
            .brgy-count-badge{font-size:11px !important;padding:3px 8px !important;}
            .brgy-card-actions{padding-top:12px;}
            .modal-overlay{padding:8px !important;}
            .modal-box{max-width:100% !important;border-radius:10px !important;max-height:92vh !important;}
            .modal-header-bar{padding:12px 14px !important;border-radius:10px 10px 0 0 !important;}
            .modal-header-bar h4{font-size:13px !important;}
            .modal-header-bar h4 svg{width:16px !important;height:16px !important;}
            .modal-body-scroll{padding:14px !important;max-height:78vh !important;}
            .modal-body-scroll th{font-size:11px !important;padding:8px !important;}
            .modal-body-scroll td{font-size:11px !important;padding:8px !important;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'birthdays', 'mobileSubtitle' => 'Birthday Beneficiaries'])

    @php
        $userName = session('admin_user_name') ?? 'Admin User';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp

    <div class="main">
        <div class="main-scroll">
            {{-- Budget Overview Card --}}
            <div class="filter-section budget-card section-spacing" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-hover) 100%);color:white;padding:14px">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
                    <div>
                        <h2 style="font-size:18px;font-weight:700;margin:0 0 4px 0">Budget Overview - {{ $months[$selectedMonth] }} {{ $selectedYear }}</h2>
                        <p style="margin:0;font-size:13px;opacity:0.9">Total budget needed for all barangays</p>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:28px;font-weight:700;margin:0">₱{{ number_format($grandTotal, 2) }}</div>
                        <div style="font-size:12px;opacity:0.9">Grand Total</div>
                    </div>
                </div>
                <div class="budget-stats">
                    <div class="budget-stat">
                        <div class="budget-stat-label">Total Seniors</div>
                        <div class="budget-stat-value">{{ $barangayBreakdown->sum('total_seniors') }}</div>
                    </div>
                    <div class="budget-stat">
                        <div class="budget-stat-label">Released</div>
                        <div class="budget-stat-value">{{ $barangayBreakdown->sum('released_count') }}</div>
                    </div>
                    <div class="budget-stat">
                        <div class="budget-stat-label">Remaining</div>
                        <div class="budget-stat-value">{{ $barangayBreakdown->sum('remaining_count') }}</div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:12px">
                    <button class="btn" style="background:var(--success);color:white;padding:10px 18px;font-weight:600;font-size:13px" onclick="releaseAllPayouts()"><i data-lucide="check-circle" style="width:14px;height:14px"></i> Release All & Download</button>
                </div>
            </div>

            {{-- Barangay Budget Breakdown --}}
            <div class="filter-section section-spacing">
                <div style="margin-bottom:16px">
                    <h3 style="font-size:16px;font-weight:600;margin:0 0 12px 0;display:flex;align-items:center;gap:8px">
                        <i data-lucide="landmark" style="width:20px;height:20px;color:var(--primary)"></i>
                        Barangay Budget Breakdown
                    </h3>
                    <div id="filterGrid">
                        <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                            <label class="filter-label">Barangay</label>
                            <select id="barangayFilter" class="filter-select" style="height:44px" onchange="filterByDate()">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
                            <label class="filter-label">Date</label>
                            <input type="month" id="dateFilter" style="width:100%;height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;" value="{{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}" onchange="filterByDate()">
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:16px;font-size:13px;margin-bottom:16px">
                    <div><strong style="color:var(--text-primary)">Grand Total:</strong> <span style="color:var(--primary);font-weight:700">₱{{ number_format($grandTotal, 2) }}</span></div>
                </div>
                <div class="barangay-cards-grid">
                    @foreach($barangayBreakdown as $barangay)
                    <div class="brgy-card" style="background:var(--background);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;min-height:200px">
                        <div class="brgy-card-head">
                            <strong style="font-size:15px;color:var(--text-primary)">{{ $barangay['barangay'] }}</strong>
                            <span class="badge brgy-count-badge" style="background:var(--primary);color:white;font-size:12px;padding:4px 10px;border-radius:8px">{{ $barangay['total_seniors'] }}</span>
                        </div>
                        <div class="brgy-card-body">
                            <div class="brgy-total-row">
                                <span style="color:var(--text-secondary);font-weight:500">Total Budget:</span>
                                <strong style="color:var(--text-primary);font-size:14px">₱{{ number_format($barangay['total_amount'], 2) }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-secondary);font-weight:500">Celebrants:</span>
                                <div id="celebrants-{{ str_replace(' ', '-', $barangay['barangay']) }}" class="celebrants-wrap">
                                    @php
                                        $displayLimit = 3;
                                        $showAll = false;
                                        $celebrantsList = $barangay['celebrants'];
                                    @endphp
                                    <div class="celebrants-table-scroll">
                                        <table class="celebrants-table" style="width:100%;border-collapse:collapse;font-size:14px;">
                                            <thead>
                                                <tr>
                                                    <th style="padding:10px 12px;text-align:left;background:#e5e7eb;border-bottom:2px solid #9ca3af;color:#374151;font-weight:700;font-size:13px;">Control No.</th>
                                                    <th style="padding:10px 12px;text-align:left;background:#e5e7eb;border-bottom:2px solid #9ca3af;color:#374151;font-weight:700;font-size:13px;">Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($celebrantsList as $celebrant)
                                                    @if($loop->index < $displayLimit)
                                                        <tr style="border-bottom:1px solid var(--border);">
                                                            <td style="padding:10px 12px;color:var(--text-secondary);">{{ $celebrant['control_number'] }}</td>
                                                            <td style="padding:10px 12px;color:var(--text-primary);">{{ $celebrant['full_name'] }}</td>
                                                        </tr>
                                                    @elseif($loop->index == $displayLimit)
                                                        @php $showAll = true; @endphp
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($showAll && $celebrantsList->count() > $displayLimit)
                                        <div id="more-{{ str_replace(' ', '-', $barangay['barangay']) }}" style="display:none">
                                            <div class="celebrants-table-scroll">
                                                <table class="celebrants-table" style="width:100%;border-collapse:collapse;font-size:14px;">
                                                    <tbody>
                                                        @foreach($celebrantsList as $celebrant)
                                                            @if($loop->index >= $displayLimit)
                                                                <tr style="border-bottom:1px solid var(--border);" data-control="{{ $celebrant['control_number'] }}" data-name="{{ $celebrant['full_name'] }}" data-birthday="{{ $celebrant['birth_date'] ?? '' }}">
                                                                    <td style="padding:10px 12px;color:var(--text-secondary);">{{ $celebrant['control_number'] }}</td>
                                                                    <td style="padding:10px 12px;color:var(--text-primary);">{{ $celebrant['full_name'] }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Store all celebrants data for modal in a script tag -->
                                        <script type="application/json" id="data-{{ str_replace(' ', '-', $barangay['barangay']) }}">
                                            {!! $celebrantsList->toJson() !!}
                                        </script>
                                        <button class="view-all-btn" onclick="openCelebrantsModal('{{ str_replace(' ', '-', $barangay['barangay']) }}', '{{ addslashes($barangay['barangay']) }}')">View All ({{ $celebrantsList->count() }} total)</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($barangay['pending_count'] > 0 || $barangay['remaining_count'] > 0)
                        <div class="brgy-card-actions">
                            <button class="btn" style="background:var(--success);color:white;padding:10px 16px;font-weight:600;font-size:13px" onclick="releaseBarangayPayouts('{{ $barangay['barangay'] }}')"><i data-lucide="check-circle" style="width:16px;height:16px"></i> Release & Download</button>
                        </div>
                        @else
                        <div class="brgy-card-actions">
                            <div style="background:var(--text-muted);color:white;padding:10px 16px;font-weight:600;font-size:13px;text-align:center;border-radius:6px;opacity:0.5"><i data-lucide="check-circle" style="width:16px;height:16px"></i> Already Released</div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Celebrants Modal -->
<div id="celebrantsModal" class="modal-overlay" style="padding:20px;">
    <div class="modal-box" style="width:800px !important;max-width:800px !important;">
        <div class="modal-header-bar" style="padding:24px 28px !important;">
            <h4 style="font-size:18px !important;"><i data-lucide="users" style="width:24px;height:24px"></i> <span id="modalBarangayName">Barangay</span> - All Celebrants</h4>
            <button class="modal-close-btn" onclick="closeCelebrantsModal()">
                <i data-lucide="x" style="width:18px;height:18px"></i>
            </button>
        </div>
        <div class="modal-body-scroll" id="modalBody" style="padding:28px !important;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>

    // Resolve selected month/year from the date filter (falls back to today)
    function getSelectedMonthYear() {
        const el = document.getElementById('dateFilter');
        if (el && el.value) {
            const parts = el.value.split('-');
            return { month: parseInt(parts[1], 10), year: parseInt(parts[0], 10) };
        }
        const now = new Date();
        return { month: now.getMonth() + 1, year: now.getFullYear() };
    }

    // Filter by date / barangay (navigate)
    function filterByDate() {
        const dateValue = document.getElementById('dateFilter').value;
        const barangay = document.getElementById('barangayFilter').value;
        const base = "{{ route('admin.senior.birthdays') }}";
        if (dateValue) {
            const [year, month] = dateValue.split('-');
            window.location.href = `${base}?month=${month}&year=${year}&barangay=${barangay}`;
        } else {
            window.location.href = `${base}?barangay=${barangay}`;
        }
    }

    function releaseAllPayouts() {
        Swal.fire({
            title: 'Release All Payouts',
            text: 'Release all pending payouts and download?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Release All & Download',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const { month, year } = getSelectedMonthYear();
                const formData = new FormData();
                formData.append('month', month);
                formData.append('year', year);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.release-all") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.released_payout_ids && res.released_payout_ids.length > 0) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("admin.senior.birthdays.print-bulk") }}';
                        form.target = '_blank';

                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(token);

                        res.released_payout_ids.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'payout_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                    }

                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#16A34A',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then(() => {
                        window.location.href = `{{ route('admin.senior.birthdays') }}?month=${month}&year=${year}`;
                    });
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to release payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function releaseBarangayPayouts(barangay) {
        Swal.fire({
            title: 'Release Payouts',
            text: `Release pending payouts for ${barangay} and download?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Release & Download',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const { month, year } = getSelectedMonthYear();
                const formData = new FormData();
                formData.append('barangay', barangay);
                formData.append('month', month);
                formData.append('year', year);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.release-barangay") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.released_payout_ids && res.released_payout_ids.length > 0) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("admin.senior.birthdays.print-bulk") }}';
                        form.target = '_blank';

                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(token);

                        res.released_payout_ids.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'payout_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                    }

                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#16A34A',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then(() => {
                        window.location.href = `{{ route('admin.senior.birthdays') }}?month=${month}&year=${year}`;
                    });
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to release payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function openCelebrantsModal(barangayId, barangayName) {
        const dataScript = document.getElementById(`data-${barangayId}`);
        const modal = document.getElementById('celebrantsModal');
        const modalBarangayName = document.getElementById('modalBarangayName');
        const modalBody = document.getElementById('modalBody');

        if (!dataScript) {
            console.error('Could not find script with id: data-' + barangayId);
            alert('Error: Could not load celebrant data');
            return;
        }

        modalBarangayName.textContent = barangayName;

        try {
            const celebrantsData = JSON.parse(dataScript.textContent);

            let tableHTML = `
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);">Control Number</th>
                            <th style="padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);">Name</th>
                            <th style="padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);">Birthday</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            celebrantsData.forEach(celebrant => {
                const controlNumber = celebrant.control_number || '-';
                const fullName = celebrant.full_name || '-';
                const birthday = celebrant.birth_date || '-';

                tableHTML += `
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:16px;color:var(--text-secondary);font-size:15px;">${controlNumber}</td>
                        <td style="padding:16px;color:var(--text-primary);font-size:15px;font-weight:500;">${fullName}</td>
                        <td style="padding:16px;color:var(--text-muted);font-size:15px;">${birthday}</td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            modalBody.innerHTML = tableHTML;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        } catch (e) {
            console.error('Error parsing celebrant data:', e);
            alert('Error loading celebrant data: ' + e.message);
        }
    }

    function closeCelebrantsModal() {
        const modal = document.getElementById('celebrantsModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    document.getElementById('celebrantsModal').addEventListener('click', function(e) {
        if (e.target === this) closeCelebrantsModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCelebrantsModal();
    });

    lucide.createIcons();
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
