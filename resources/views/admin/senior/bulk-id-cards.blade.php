<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bulk ID Cards</title>
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

        .bulk-cards-container { display: flex; flex-direction: row; align-items: flex-start; padding: 20px; gap: 20px; position: relative; }
        .cards-wrapper { flex: 1; display: flex; flex-direction: column; align-items: flex-start; gap: 20px; }
        .action-buttons { display: flex; flex-direction: row; gap: 10px; align-items: flex-end; position: fixed; bottom: 20px; right: 20px; }
        .print-btn { padding: 12px 24px; font-size: 16px; cursor: pointer; background: #1A237E; color: white; border: none; border-radius: 8px; margin-bottom: 20px; width: 120px; text-align: center; }
        .page { width: 210mm; min-height: 297mm; background: white; padding: 10mm; display: flex; flex-direction: column; gap: 5mm; page-break-after: always; margin-bottom: 20px; }
        .cards-row { display: flex; flex-wrap: wrap; gap: 5mm; justify-content: center; }
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
        .details { flex: 1; display: flex; flex-direction: column; gap: 1mm; font-size: 6.5pt; }
        .field { display: flex; align-items: baseline; line-height: 1.1; }
        .label { font-weight: bold; color: #333; margin-right: 1.5mm; white-space: nowrap; font-size: 6pt; width: 15mm; text-align: left; }
        .value { flex: 1; font-weight: 600; color: #000; padding-left: 0.5mm; font-size: 6.5pt; min-width: 0; word-wrap: break-word; }
        .row { display: flex; gap: 2mm; }
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
        .print-btn { padding: 12px 24px; font-size: 16px; cursor: pointer; background: #1A237E; color: white; border: none; border-radius: 8px; margin-bottom: 20px; }
        .print-btn:hover { background: #121858; }
        @media print { 
            .sidebar, .mobile-header, .sidebar-overlay, .no-print { display: none !important; }
            .main { margin-left: 0; padding: 0; }
            .page { margin: 0; padding: 5mm; border: none; box-shadow: none; }
            .id-card { border: 0.2pt solid #aaa; box-shadow: none; } 
            @page { margin: 5mm; size: A4; }
        }
    </style>
</head>
<body>
@include('admin.senior.partials.navigation', ['active' => 'id-card', 'mobileSubtitle' => 'Bulk ID Cards'])

<div class="main">
    <div class="main-scroll">
        <div class="bulk-cards-container">
            <div class="cards-wrapper">
                @php
                    $cardsPerPage = 6;
                    $chunks = array_chunk($cardData, $cardsPerPage);
                @endphp
                
                @foreach($chunks as $chunk)
                    <div class="page">
                        <div class="cards-row">
                            @foreach($chunk as $card)
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
                                                <div class="field"><span class="label">ID No.:</span><span class="value">{{ $card['senior']->control_number ?? 'N/A' }}</span></div>
                                                <div class="field"><span class="label">Date Issued:</span><span class="value">{{ $card['currentDate'] }}</span></div>
                                                <div class="field"><span class="label">Name:</span><span class="value">{{ $card['senior']->full_name ?? 'N/A' }}</span></div>
                                                <div class="field"><span class="label">Address:</span><span class="value">{{ $card['senior']->address ?? 'N/A' }}</span></div>
                                                <div class="field"><span class="label">Date of Birth:</span><span class="value">{{ $card['formattedBirthDate'] }}</span></div>
                                                <div class="row">
                                                    <div class="field" style="width: 50%;"><span class="label">Gender:</span><span class="value">{{ $card['senior']->sex ?? 'N/A' }}</span></div>
                                                    <div class="field" style="width: 50%;"><span class="label">Status:</span><span class="value">{{ ucfirst($card['senior']->status->value ?? 'Active') }}</span></div>
                                                </div>
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
                                                <div class="field"><span class="label">Name:</span><span class="value"></span></div>
                                                <div class="field"><span class="label">Contact:</span><span class="value"></span></div>
                                                <div class="field"><span class="label">Address:</span><span class="value"></span></div>
                                            </div>
                                        </div>
                                        <div class="footer-note">If found, please return to the nearest OSCA Office or Police Station.</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="action-buttons no-print">
                <button class="print-btn" onclick="window.print()" style="background: #10B981;">Print</button>
                <button class="print-btn" onclick="window.close()" style="background: white; border: 2px solid #DC2626; color: #DC2626;">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
