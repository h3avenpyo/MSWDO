<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen ID Card - {{ $senior->full_name }}</title>
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
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4,h5{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* Detail Header (same design as social-case/detail) */
        .detail-header {
            background: white;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .detail-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .case-info h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 700;
            color: #1E3A8A;
            letter-spacing: -0.5px;
        }
        .case-info .client-name {
            font-size: 15px;
            color: #64748B;
            font-weight: 500;
            margin-bottom: 12px;
        }
        .case-meta {
            display: flex;
            gap: 24px;
            font-size: 13px;
            color: #64748B;
        }
        .case-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-completed {
            background: #D1FAE5;
            color: #065F46;
        }
        .header-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .header-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid #E2E8F0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            text-decoration: none;
        }
        .header-btn:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
        }
        .header-btn.primary {
            background: #1E3A8A;
            color: white;
            border-color: #1E3A8A;
        }
        .header-btn.primary:hover {
            background: #1E40AF;
        }

        @media (max-width: 900px) {
            .detail-header-top { flex-direction: column; gap: 16px; }
            .header-actions { flex-wrap: wrap; }
            .case-meta { flex-wrap: wrap; gap: 12px; }
        }
        @media (max-width: 768px) {
            .detail-header { padding: 16px; }
            .case-info h1 { font-size: 18px; }
            .header-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        }
        @media (max-width: 480px) {
            .detail-header { padding: 12px; }
            .case-info h1 { font-size: 16px; word-break: break-word; }
            .header-actions { display: flex; gap: 8px; }
            .header-actions .header-btn { flex: 1; justify-content: center; font-size: 13px; padding: 8px 12px; }
            .case-meta { font-size: 12px; gap: 8px; }
            .status-badge { font-size: 11px; padding: 5px 10px; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .bulk-cards-container { display: flex; flex-direction: row; align-items: flex-start; padding: 0; gap: 20px; position: relative; }
        .cards-wrapper { flex: 1; display: flex; flex-direction: column; align-items: flex-start; gap: 20px; }
        .page { width: 210mm; min-height: 297mm; background: white; padding: 10mm; display: flex; flex-direction: column; gap: 5mm; page-break-after: always; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-radius: 8px; }
        .cards-row { display: flex; flex-wrap: wrap; gap: 5mm; justify-content: flex-start; padding-left: 10mm; }
        .card-pair { display: flex; flex-direction: row; gap: 2mm; }
        .id-card { width: 85.6mm; height: 53.98mm; background-color: #ffffff; border-radius: 3.18mm; border: 2px solid #1A237E; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); position: relative; overflow: hidden; display: flex; flex-direction: column; padding: 3mm; color: #1a1a1a; }
        .header { text-align: center; border-bottom: 2px solid #1A237E; padding-bottom: 1mm; margin-bottom: 2mm; background: linear-gradient(to bottom, #1A237E, #121858); padding: 2mm 0; margin: -3mm -3mm 2mm -3mm; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .header-content { display: flex; justify-content: space-between; align-items: center; gap: 2mm; }
        .header-content img { height: 8mm; width: auto; }
        .header-content img:first-child { margin-left: 2mm; }
        .header-content img:last-child { margin-right: 2mm; }
        .header-text { flex: 1; text-align: center; }
        .header h1 { font-size: 5.5pt; text-transform: uppercase; color: #ffffff; font-weight: bold; letter-spacing: 0.2pt; }
        .header h2 { font-size: 6.5pt; text-transform: uppercase; color: #FBC02D; font-weight: 800; margin-top: 0.5mm; }
        .front-body { display: flex; gap: 2.5mm; flex: 1; align-items: center; justify-content: center; }
        .photo-box { width: 25mm; height: 25mm; border: 1px dashed #666; background-color: #f9f9f9; display: flex; align-items: center; justify-content: center; font-size: 6pt; color: #777; text-align: center; flex-shrink: 0; }
        .details { flex: 1; display: flex; flex-direction: column; gap: 0.8mm; font-size: 6.5pt; }
        .field { display: flex; align-items: baseline; line-height: 1.1; }
        .label { font-weight: bold; color: #333; margin-right: 1.5mm; white-space: nowrap; font-size: 6pt; width: 15mm; text-align: left; }
        .value { flex: 1; font-weight: 600; color: #000; padding-left: 0.5mm; font-size: 6.5pt; min-width: 0; word-wrap: break-word; }
        .signatures { margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; padding-top: 1mm; }
        .sig-block { text-align: center; width: 45%; }
        .sig-line { border-top: 0.5pt solid #333; margin-top: 3.5mm; font-size: 4.5pt; font-weight: bold; color: #444; }
        .back-title { font-size: 7pt; font-weight: bold; text-align: center; color: #003366; text-transform: uppercase; border-bottom: 1px solid #003366; padding-bottom: 0.5mm; margin-bottom: 1.5mm; }
        .benefits-list { font-size: 6pt; padding-left: 3mm; line-height: 1.4; color: #222; }
        .benefits-list li { margin-bottom: 1mm; }
        .emergency-section { margin-top: auto; border-top: 0.5pt dashed #003366; padding-top: 1mm; }
        .emergency-title { font-size: 6pt; font-weight: bold; color: #a00000; text-align: center; margin-bottom: 1mm; text-transform: uppercase; }
        .emergency-section .details { flex: 1; display: flex; flex-direction: column; gap: 0.8mm; font-size: 6pt; }
        .emergency-section .field { display: flex; align-items: baseline; line-height: 1.1; }
        .emergency-section .label { font-weight: bold; color: #333; margin-right: 1.5mm; white-space: nowrap; font-size: 5.5pt; width: 13mm; text-align: left; }
        .emergency-section .value { flex: 1; font-weight: 600; color: #000; padding-left: 0.5mm; font-size: 6pt; min-width: 0; word-wrap: break-word; }
        .footer-note { font-size: 5pt; text-align: center; color: #555; font-style: italic; margin-top: 1mm; }
        @media print { 
            .sidebar, .mobile-header, .sidebar-overlay, .no-print, .detail-header, #editSeniorModal { display: none !important; }
            .main { margin-left: 0 !important; padding: 0 !important; }
            .main-scroll { padding: 0 !important; margin: 0 !important; }
            .bulk-cards-container { padding: 0 !important; margin: 0 !important; }
            .page { box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; }
            .id-card { border: 0.2pt solid #aaa; box-shadow: none; } 
            @page { margin: 5mm; size: auto; }
        }
    </style>
</head>
<body>
@include('admin.senior.partials.navigation', ['active' => 'masterlist', 'mobileSubtitle' => 'ID Card'])

<div class="main">
    <div class="main-scroll">
        <!-- Page Sub-Header -->
        <div class="mb-6 no-print">
            <p class="text-[#6B7280] text-sm m-0">View and manage a specific senior citizen ID card record.</p>
        </div>

        <!-- Detail Header (like in admin/social-case/detail) -->
        <div class="detail-header no-print">
            <div class="detail-header-top">
                <div class="case-info">
                    <h1 id="headerControlNo">{{ $senior->control_number ?? ($senior->senior_id_number ?? 'SC-'.$senior->id) }}</h1>
                    <div class="client-name" id="headerFullName">{{ $senior->full_name }}</div>
                    <div class="case-meta">
                        <div class="case-meta-item">
                            <i data-lucide="calendar" style="width:16px;height:16px;"></i>
                            <span id="headerDate">{{ $senior->created_at ? $senior->created_at->format('M j, Y') : ($senior->date_issued ? \Carbon\Carbon::parse($senior->date_issued)->format('M j, Y') : now()->format('M j, Y')) }}</span>
                        </div>
                        <div class="case-meta-item">
                            <i data-lucide="user" style="width:16px;height:16px;"></i>
                            <span id="headerOfficer">{{ $senior->createdBy?->name ?? (session('admin_user_name') ?? (auth()->user()?->name ?? 'Maria Cruz')) }}</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <button type="button" class="header-btn" style="background:#059669;color:white;border-color:#059669;width:120px;flex-shrink:0;" onclick="printAndNotify()">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i>
                        Print
                    </button>
                    <button type="button" class="header-btn primary" style="width:120px;flex-shrink:0;" onclick="reprintSeniorCard({{ $senior->id }})">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i>
                        Reprint
                    </button>
                    <button type="button" class="header-btn" style="background:#1A237E;color:white;border-color:#1A237E;width:120px;flex-shrink:0;" onclick="openEditSeniorModal()">
                        <i data-lucide="edit" style="width:16px;height:16px;"></i>
                        Edit
                    </button>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="status-badge status-completed">
                    <i data-lucide="check-circle" style="width:14px;height:14px;margin-right:4px;"></i>
                    <span id="headerStatus">{{ ucfirst($senior->status->value ?? $senior->status ?? 'Active') }}</span>
                </span>
            </div>
        </div>

        <div class="bulk-cards-container">
            <div class="cards-wrapper">
                <div class="page">
                    <div class="cards-row">
                        <div class="card-pair">
                            <div class="id-card">
                                <div class="header">
                                    <div class="header-content">
                                        <img src="{{ asset('images/silang.png') }}" alt="Silang Logo">
                                        <div class="header-text">
                                            <h1>Republic of the Philippines • Municipality of Silang</h1>
                                            <h2>Office for Senior Citizens Affairs (OSCA)</h2>
                                        </div>
                                        <img src="{{ asset('images/dswd.png') }}" alt="DSWD Logo">
                                    </div>
                                </div>
                                <div class="front-body">
                                    <div class="photo-box">1x1<br>PHOTO</div>
                                    <div class="details">
                                        <div class="field"><span class="label">ID No.:</span><span class="value" id="cardControlNo">{{ $senior->control_number ?? ($senior->senior_id_number ?? 'N/A') }}</span></div>
                                        <div class="field"><span class="label">Date Issued:</span><span class="value" id="cardDateIssued">{{ $currentDate }}</span></div>
                                        <div class="field"><span class="label">Name:</span><span class="value" id="cardFullName">{{ $senior->full_name ?? 'N/A' }}</span></div>
                                        <div class="field"><span class="label">Address:</span><span class="value" id="cardAddress">{{ $senior->address ?? 'N/A' }}</span></div>
                                        <div class="field"><span class="label">Date of Birth:</span><span class="value" id="cardBirthDate">{{ $formattedBirthDate }}</span></div>
                                        <div class="field"><span class="label">Gender:</span><span class="value" id="cardGender">{{ $senior->sex ?? 'N/A' }}</span></div>
                                    </div>
                                </div>
                                <div class="signatures">
                                    <div class="sig-block"><div class="sig-line">Signature</div></div>
                                    <div class="sig-block"><div class="sig-line">OSCA Head / Mayor</div></div>
                                </div>
                            </div>
                            
                            <div class="id-card">
                                <div class="back-title">Card Benefits & Legal Notice</div>
                                <ul class="benefits-list">
                                    <li>Proof of entitlement to statutory privileges & discounts under R.A. 9994 (Expanded Senior Citizens Act).</li>
                                    <li>Non-transferable. Unauthorized use or reproduction is punishable by law.</li>
                                    <li>Must be presented alongside purchase booklets where applicable.</li>
                                </ul>
                                <div class="emergency-section">
                                    <div class="emergency-title">In Case of Emergency</div>
                                    <div class="details">
                                        <div class="field"><span class="label">Name:</span><span class="value" id="cardEmergencyName">{{ $senior->emergency_contact_name ?? '' }}</span></div>
                                        <div class="field"><span class="label">Contact:</span><span class="value" id="cardEmergencyContact">{{ $senior->emergency_contact_number ?? '' }}</span></div>
                                        <div class="field"><span class="label">Address:</span><span class="value" id="cardEmergencyAddress">{{ $senior->emergency_contact_relationship ?? '' }}</span></div>
                                    </div>
                                </div>
                                <div class="footer-note">If found, please return to the nearest OSCA Office or Police Station.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Senior Modal -->
<div id="editSeniorModal" class="no-print" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:640px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid #E5E7EB;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#EEF2FF;display:flex;align-items:center;justify-content:center;color:#1A237E;">
                    <i data-lucide="edit" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <h3 style="margin:0;font-size:18px;font-weight:700;color:#111827;">Edit Senior Citizen</h3>
                    <p style="margin:2px 0 0 0;font-size:12px;color:#6B7280;">Update senior details and ID card information</p>
                </div>
            </div>
            <button type="button" onclick="closeEditSeniorModal()" style="background:transparent;border:none;color:#9CA3AF;cursor:pointer;padding:4px;display:flex;align-items:center;justify-content:center;border-radius:6px;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        
        <form id="editSeniorForm" onsubmit="submitEditSenior(event)" style="padding:24px;overflow-y:auto;display:flex;flex-direction:column;gap:16px;margin:0;">
            <input type="hidden" name="id" value="{{ $senior->id }}">
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Control / ID Number</label>
                    <input type="text" name="control_number" value="{{ $senior->control_number ?? $senior->senior_id_number }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Status</label>
                    <select name="status" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;background:#fff;">
                        <option value="active" {{ ($senior->status->value ?? $senior->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ ($senior->status->value ?? $senior->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="archived" {{ ($senior->status->value ?? $senior->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">First Name <span style="color:#DC2626;">*</span></label>
                    <input type="text" name="first_name" required value="{{ $senior->first_name }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ $senior->middle_name }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Last Name <span style="color:#DC2626;">*</span></label>
                    <input type="text" name="last_name" required value="{{ $senior->last_name }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Date of Birth</label>
                    <input type="date" name="birth_date" value="{{ $senior->birth_date ? $senior->birth_date->format('Y-m-d') : '' }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Gender</label>
                    <select name="sex" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;background:#fff;">
                        <option value="Male" {{ $senior->sex === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $senior->sex === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Barangay</label>
                    <select name="barangay" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;background:#fff;">
                        @foreach($allBarangays ?? [] as $b)
                            <option value="{{ $b }}" {{ $senior->barangay === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ $senior->contact_number }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                </div>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.03em;">Address</label>
                <input type="text" name="address" value="{{ $senior->address }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
            </div>

            <div style="border-top:1px solid #E5E7EB;padding-top:12px;">
                <h4 style="margin:0 0 12px 0;font-size:13px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.03em;">Emergency Contact</h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#6B7280;margin-bottom:4px;">Contact Person Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ $senior->emergency_contact_name }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;color:#6B7280;margin-bottom:4px;">Emergency Phone Number</label>
                        <input type="text" name="emergency_contact_number" value="{{ $senior->emergency_contact_number }}" style="width:100%;height:40px;border:1px solid #D1D5DB;border-radius:8px;padding:0 12px;font-size:14px;color:#111827;outline:none;">
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px;">
                <button type="button" onclick="closeEditSeniorModal()" style="padding:10px 20px;border:1px solid #D1D5DB;background:#fff;color:#374151;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;">Cancel</button>
                <button type="submit" id="saveEditBtn" style="padding:10px 24px;border:none;background:#1A237E;color:#fff;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    <i data-lucide="check" style="width:16px;height:16px;"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    
    function printAndNotify() {
        window.print();
        
        setTimeout(function() {
            Swal.fire({
                title: 'Success!',
                text: 'ID Card has been sent to printer.',
                icon: 'success',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        }, 500);
    }

    function reprintSeniorCard(id) {
        Swal.fire({
            title: 'Reprint ID Card',
            text: 'Are you sure you want to reprint this ID card?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1E3A8A',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Reprint',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/senior/id-card/${id}/reprint`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    window.print();
                    Swal.fire({
                        title: 'Reprint Successful',
                        text: 'ID card has been recorded and sent to printer.',
                        icon: 'success',
                        confirmButtonColor: '#1A237E',
                        confirmButtonText: 'OK'
                    });
                })
                .catch(err => {
                    console.error('Reprint error:', err);
                    window.print();
                });
            }
        });
    }

    function openEditSeniorModal() {
        const modal = document.getElementById('editSeniorModal');
        if (modal) {
            modal.style.display = 'flex';
            lucide.createIcons();
        }
    }

    function closeEditSeniorModal() {
        const modal = document.getElementById('editSeniorModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Close modal on background click
    document.getElementById('editSeniorModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditSeniorModal();
        }
    });

    function submitEditSenior(e) {
        e.preventDefault();
        const form = document.getElementById('editSeniorForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('saveEditBtn');
        const originalBtnHtml = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;margin-right:6px;"></span> Saving...';

        fetch(`/admin/senior/update/{{ $senior->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(json => { throw json; });
            }
            return res.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            closeEditSeniorModal();

            if (data.senior) {
                // Update top header
                if (document.getElementById('headerControlNo')) document.getElementById('headerControlNo').textContent = data.senior.control_number || '-';
                if (document.getElementById('headerFullName')) document.getElementById('headerFullName').textContent = data.senior.full_name || '-';
                if (document.getElementById('headerStatus')) document.getElementById('headerStatus').textContent = data.senior.status || 'Active';

                // Update ID card front
                if (document.getElementById('cardControlNo')) document.getElementById('cardControlNo').textContent = data.senior.control_number || 'N/A';
                if (document.getElementById('cardFullName')) document.getElementById('cardFullName').textContent = data.senior.full_name || 'N/A';
                if (document.getElementById('cardAddress')) {
                    const addr = data.senior.address ? (data.senior.address + (data.senior.barangay ? ', ' + data.senior.barangay : '')) : (data.senior.barangay || 'N/A');
                    document.getElementById('cardAddress').textContent = addr;
                }
                if (document.getElementById('cardBirthDate')) document.getElementById('cardBirthDate').textContent = data.senior.birth_date || 'N/A';
                if (document.getElementById('cardGender')) document.getElementById('cardGender').textContent = data.senior.sex || 'N/A';

                // Update ID card back
                if (document.getElementById('cardEmergencyName')) document.getElementById('cardEmergencyName').textContent = data.senior.emergency_contact_name || '';
                if (document.getElementById('cardEmergencyContact')) document.getElementById('cardEmergencyContact').textContent = data.senior.emergency_contact_number || '';
            }

            Swal.fire({
                title: 'Updated!',
                text: 'Senior citizen record updated successfully.',
                icon: 'success',
                confirmButtonColor: '#1A237E'
            });
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            console.error('Update error:', err);
            let msg = 'Failed to update record. Please check the inputs.';
            if (err && err.errors) {
                msg = Object.values(err.errors).flat().join('<br>');
            } else if (err && err.message) {
                msg = err.message;
            }
            Swal.fire({
                title: 'Error',
                html: msg,
                icon: 'error',
                confirmButtonColor: '#DC2626'
            });
        });
    }
</script>
</body>
</html>