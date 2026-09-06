<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen ID Card - {{ $senior->full_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
        body { background-color: #f0f2f5; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; gap: 20px; padding: 20px; }
        .cards-container { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
        .id-card { width: 85.6mm; height: 53.98mm; background-color: #ffffff; border-radius: 3.18mm; border: 2px solid #1A237E; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); position: relative; overflow: hidden; display: flex; flex-direction: column; padding: 3mm; color: #1a1a1a; page-break-after: always; }
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
            body { background: white; padding: 0; gap: 0; } 
            .id-card { border: 0.2pt solid #aaa; box-shadow: none; margin: 0; } 
            .no-print { display: none; }
            @page { margin: 0; size: auto; }
        }
    </style>
</head>
<body>
    <div class="cards-container">
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
                <div class="field"><span class="label">ID No.:</span><span class="value">{{ $senior->control_number ?? 'N/A' }}</span></div>
                <div class="field"><span class="label">Date Issued:</span><span class="value">{{ $currentDate }}</span></div>
                <div class="field"><span class="label">Name:</span><span class="value">{{ $senior->full_name ?? 'N/A' }}</span></div>
                <div class="field"><span class="label">Address:</span><span class="value">{{ $senior->address ?? 'N/A' }}</span></div>
                <div class="field"><span class="label">Date of Birth:</span><span class="value">{{ $formattedBirthDate }}</span></div>
                <div class="row">
                    <div class="field" style="width: 50%;"><span class="label">Gender:</span><span class="value">{{ $senior->sex ?? 'N/A' }}</span></div>
                    <div class="field" style="width: 50%;"><span class="label">Status:</span><span class="value">{{ ucfirst($senior->status->value ?? 'Active') }}</span></div>
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
    
    <div class="no-print" style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
        <a href="{{ route('admin.senior.id-card') }}" class="print-btn" style="background: #1A237E; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Back to List</a>
        <button class="print-btn" onclick="printAndNotify()">Print ID Card</button>
        <button class="print-btn" onclick="window.close()" style="background: white; border: 2px solid #DC2626; color: #DC2626;">Close</button>
    </div>
    
    <script>
        function printAndNotify() {
            window.print();
            
            // Show SweetAlert after print dialog closes
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
    </script>
</body>
</html>
