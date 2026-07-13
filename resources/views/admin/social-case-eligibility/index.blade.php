@extends('layouts.admin')

@section('title', 'New Social Case — Eligibility Validation')

@section('navbar-title', 'New Social Case')

@section('page-styles')
<style>
    /* ── Metric Cards ─────────────────────────────────────────── */
    .metric-card {
        color: white;
        border: none;
        border-radius: 16px;
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
    .metric-card.bg-primary-grad  { background: linear-gradient(135deg, var(--primary) 0%, #3b429f 100%); }
    .metric-card.bg-success-grad  { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); }
    .metric-card.bg-danger-grad   { background: linear-gradient(135deg, var(--danger)  0%, #b91c1c 100%); }
    .metric-card.bg-warning-grad  { background: linear-gradient(135deg, #F59E0B 0%, #d97706 100%); }
    .metric-card .mc-label { font-size: 0.8rem; font-weight: 500; opacity: 0.9; margin: 0; }
    .metric-card .mc-value { font-size: 2rem; font-weight: 800; margin: 0.4rem 0 0; letter-spacing: -0.03em; }
    .metric-card .mc-icon  { font-size: 1.4rem; opacity: 0.25; }

    /* ── Workflow Stepper ─────────────────────────────────────── */
    .wf-stepper {
        display: flex;
        align-items: center;
        overflow-x: auto;
        gap: 0;
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 1.75rem;
    }
    .wf-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
        flex: 1;
        min-width: 70px;
        position: relative;
    }
    .wf-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 18px;
        left: 55%;
        right: -45%;
        height: 2px;
        background: #E2E8F0;
        z-index: 0;
    }
    .wf-step.done:not(:last-child)::after  { background: var(--success); }
    .wf-step.active:not(:last-child)::after { background: linear-gradient(to right, var(--primary), #E2E8F0); }
    .wf-bubble {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 700;
        background: #E2E8F0; color: #64748B;
        border: 2px solid #E2E8F0;
        position: relative; z-index: 1;
        transition: all 0.3s ease;
    }
    .wf-step.done   .wf-bubble { background: var(--success); color: #fff; border-color: var(--success); }
    .wf-step.active .wf-bubble {
        background: var(--primary); color: #fff; border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(26,35,126,0.15);
        animation: stepPulse 2s infinite;
    }
    @keyframes stepPulse {
        0%, 100% { box-shadow: 0 0 0 4px rgba(26,35,126,0.15); }
        50%       { box-shadow: 0 0 0 7px rgba(26,35,126,0.08); }
    }
    .wf-label {
        font-size: 0.65rem; font-weight: 500;
        color: #94A3B8; text-align: center; white-space: nowrap;
    }
    .wf-step.active .wf-label { color: var(--primary); font-weight: 700; }
    .wf-step.done   .wf-label { color: var(--success); }

    /* ── Panel cards (no thick borders!) ─────────────────────── */
    .panel-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: box-shadow 0.2s ease;
    }
    .panel-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.09); }
    .panel-card .panel-header {
        padding: 1.25rem 1.5rem 1rem;
        border-bottom: 1px solid #F1F5F9;
    }
    .panel-card .panel-header h6 {
        font-size: 1rem; font-weight: 700;
        color: var(--primary); margin: 0;
    }
    .panel-card .panel-header p {
        font-size: 0.8rem; color: var(--text-muted); margin: 0.2rem 0 0;
    }
    .panel-body { padding: 1.25rem 1.5rem; }

    /* ── Search input ─────────────────────────────────────────── */
    .search-wrap { position: relative; }
    .search-wrap .search-icon {
        position: absolute; left: 1rem; top: 50%;
        transform: translateY(-50%);
        color: var(--primary); font-size: 0.95rem; pointer-events: none;
    }
    .search-wrap input {
        padding-left: 2.6rem;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        font-size: 0.95rem;
        height: 46px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-wrap input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26,35,126,0.1);
        outline: none;
    }

    /* ── Client result cards ──────────────────────────────────── */
    .client-result-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.65rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
    }
    .client-result-card:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(26,35,126,0.1);
        transform: translateX(2px);
    }
    .client-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), #3b429f);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem;
        flex-shrink: 0;
    }
    .client-meta-name { font-weight: 700; font-size: 0.95rem; color: var(--text); }
    .client-meta-sub  { font-size: 0.78rem; color: var(--text-muted); margin-top: 0.1rem; }

    /* ── Eligibility result ───────────────────────────────────── */
    .eligibility-waiting {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--text-muted);
    }
    .eligibility-waiting .ew-icon {
        width: 60px; height: 60px; border-radius: 50%;
        background: #F1F5F9;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; color: #94A3B8;
        margin: 0 auto 1rem;
    }
    .eligibility-result-card {
        border-radius: 12px;
        padding: 1.25rem;
        display: flex; align-items: flex-start; gap: 1rem;
        animation: fadeSlideIn 0.4s ease;
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .eligibility-result-card.eligible   { background: #ECFDF5; border: 1px solid #A7F3D0; }
    .eligibility-result-card.ineligible { background: #FEF2F2; border: 1px solid #FECACA; }
    .er-icon {
        width: 46px; height: 46px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .eligible   .er-icon { background: #D1FAE5; color: #065F46; }
    .ineligible .er-icon { background: #FEE2E2; color: #991B1B; }
    .er-title { font-weight: 800; font-size: 1.05rem; }
    .eligible   .er-title { color: #065F46; }
    .ineligible .er-title { color: #991B1B; }
    .er-detail { font-size: 0.82rem; color: #374151; margin-top: 0.3rem; line-height: 1.5; }

    /* ── Policy reminder ──────────────────────────────────────── */
    .policy-box {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        font-size: 0.82rem;
        color: #1E40AF;
        margin-top: 1rem;
    }
    .policy-box i { margin-right: 0.4rem; }

    /* ── Recent checks table ──────────────────────────────────── */
    .check-row {
        display: flex; align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #F1F5F9;
        gap: 0.75rem;
    }
    .check-row:last-child { border-bottom: none; }
    .check-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.8rem; flex-shrink: 0;
    }
    .eligible-av   { background: #D1FAE5; color: #065F46; }
    .ineligible-av { background: #FEE2E2; color: #991B1B; }
    .check-info { flex: 1; }
    .check-name { font-weight: 600; font-size: 0.88rem; color: var(--text); }
    .check-sub  { font-size: 0.75rem; color: var(--text-muted); }
    .check-badge {
        font-size: 0.72rem; font-weight: 700;
        padding: 0.3rem 0.7rem; border-radius: 20px;
    }
    .badge-eligible   { background: #D1FAE5; color: #065F46; }
    .badge-ineligible { background: #FEE2E2; color: #991B1B; }
</style>
@endsection

@section('content')

{{-- ── Workflow Stepper ────────────────────────────────────── --}}
<div class="wf-stepper">
    @php
    $wfSteps = [
        ['icon'=>'fa-magnifying-glass','label'=>'Search'],
        ['icon'=>'fa-shield-check','label'=>'Eligibility'],
        ['icon'=>'fa-file-alt','label'=>'Intake'],
        ['icon'=>'fa-clipboard-list','label'=>'Requirements'],
        ['icon'=>'fa-comments','label'=>'Interview'],
        ['icon'=>'fa-users','label'=>'Family'],
        ['icon'=>'fa-stethoscope','label'=>'Assessment'],
        ['icon'=>'fa-file-medical-alt','label'=>'Report'],
        ['icon'=>'fa-print','label'=>'Print'],
        ['icon'=>'fa-paper-plane','label'=>'Release'],
        ['icon'=>'fa-hand-holding-heart','label'=>'Assistance'],
        ['icon'=>'fa-lock','label'=>'Close'],
    ];
    @endphp
    @foreach($wfSteps as $i => $s)
        <div class="wf-step {{ $i === 0 ? 'active' : '' }}">
            <div class="wf-bubble">
                @if($i === 0)
                    <i class="fas {{ $s['icon'] }}"></i>
                @else
                    {{ $i + 1 }}
                @endif
            </div>
            <span class="wf-label">{{ $s['label'] }}</span>
        </div>
    @endforeach
</div>

{{-- ── Metrics Row ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="metric-card bg-primary-grad shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="mc-label"><i class="fas fa-search me-1"></i>Today's Checks</p>
                    <p class="mc-value">{{ $metrics['checksToday'] }}</p>
                </div>
                <i class="fas fa-search mc-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="metric-card bg-success-grad shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="mc-label"><i class="fas fa-check-circle me-1"></i>Eligible</p>
                    <p class="mc-value">{{ $metrics['eligibleToday'] }}</p>
                </div>
                <i class="fas fa-check-circle mc-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="metric-card bg-danger-grad shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="mc-label"><i class="fas fa-times-circle me-1"></i>Ineligible</p>
                    <p class="mc-value">{{ $metrics['notEligibleToday'] }}</p>
                </div>
                <i class="fas fa-times-circle mc-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="metric-card bg-warning-grad shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="mc-label"><i class="fas fa-clock me-1"></i>Pending Requirements</p>
                    <p class="mc-value">{{ $metrics['waitingRequirements'] }}</p>
                </div>
                <i class="fas fa-clock mc-icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- ── Main Panels ─────────────────────────────────────────── --}}
<div class="row g-4">

    {{-- Search Panel --}}
    <div class="col-xl-5">
        <div class="panel-card">
            <div class="panel-header">
                <h6><i class="fas fa-user-search me-2"></i>Steps 1 & 2 — Find Client</h6>
                <p>Search by name, contact number, address, or Client ID.</p>
            </div>
            <div class="panel-body">
                <form id="searchForm" action="{{ route('admin.social-case-eligibility.search') }}" method="POST">
                    @csrf
                    <div class="search-wrap mb-3">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            id="searchQuery"
                            name="query"
                            class="form-control"
                            placeholder="Name, phone, address, or Client ID…"
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" id="searchButton" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Find Client
                    </button>
                    <div id="searchResultMessage" class="text-muted small mt-2 text-center"></div>
                </form>

                <div id="searchResults" class="mt-3"></div>

                {{-- New Client CTA (hidden until no results) --}}
                <div id="newClientCta" class="d-none mt-3">
                    <div class="policy-box" style="background:#FFFBEB;border-color:#FCD34D;color:#92400E;">
                        <i class="fas fa-info-circle"></i>
                        No matching record found. Register the client first.
                    </div>
                    <a href="/admin/social-case-eligibility/register" class="btn btn-outline-primary w-100 mt-2">
                        <i class="fas fa-user-plus me-2"></i>Create New Client Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Eligibility Panel --}}
    <div class="col-xl-7">
        <div class="panel-card h-100">
            <div class="panel-header">
                <h6><i class="fas fa-shield-alt me-2"></i>Step 3 — Eligibility Validation</h6>
                <p>6-month assistance check will run automatically on client selection.</p>
            </div>
            <div class="panel-body">
                <div id="eligibilityContent">
                    <div class="eligibility-waiting">
                        <div class="ew-icon"><i class="fas fa-user-clock"></i></div>
                        <p class="fw-600 mb-1" style="font-weight:600;color:#374151;">Waiting for Client Selection</p>
                        <p class="small mb-0">Search and select a client on the left to run the eligibility check.</p>
                    </div>
                </div>

                {{-- Policy reminder always visible --}}
                <div class="policy-box">
                    <i class="fas fa-balance-scale"></i>
                    <strong>Policy Reminder:</strong> A client is <strong>not eligible</strong> if they received
                    the same type of assistance within the last <strong>6 months</strong>.
                    No supervisor approval needed — the system checks automatically.
                </div>

                {{-- Recent checks --}}
                <div class="mt-4">
                    <p class="fw-bold mb-2" style="font-size:0.85rem;color:var(--text);">
                        <i class="fas fa-history me-1 text-muted"></i>Recent Eligibility Checks
                    </p>
                    @forelse($metrics['recentChecks'] as $log)
                    <div class="check-row">
                        <div class="check-avatar {{ $log->result === 'eligible' ? 'eligible-av' : 'ineligible-av' }}">
                            {{ strtoupper(substr($log->client_name, 0, 1)) }}
                        </div>
                        <div class="check-info">
                            <div class="check-name">{{ $log->client_name }}</div>
                            <div class="check-sub">
                                <i class="fas fa-user-tie me-1"></i>{{ $log->officer_name ?? 'Unknown' }}
                                &nbsp;·&nbsp;
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="check-badge {{ $log->result === 'eligible' ? 'badge-eligible' : 'badge-ineligible' }}">
                            {{ $log->result === 'eligible' ? '✔ Eligible' : '✖ Ineligible' }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted small text-center mt-2">No checks recorded today.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
document.getElementById('searchForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const query = document.getElementById('searchQuery').value.trim();
    const resultArea   = document.getElementById('searchResults');
    const messageArea  = document.getElementById('searchResultMessage');
    const newClientCta = document.getElementById('newClientCta');
    const searchButton = document.getElementById('searchButton');

    resultArea.innerHTML = '';
    messageArea.textContent = '';
    newClientCta.classList.add('d-none');

    if (query.length < 2) {
        messageArea.textContent = 'Please enter at least 2 characters.';
        return;
    }

    searchButton.disabled = true;
    searchButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching…';

    let payload;
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ query }),
        });

        if (!response.ok) throw new Error('Search failed.');
        payload = await response.json();
    } catch (err) {
        messageArea.textContent = err.message;
        return;
    } finally {
        searchButton.disabled = false;
        searchButton.innerHTML = '<i class="fas fa-search me-2"></i>Find Client';
    }

    const clients = payload.clients || [];

    if (!clients.length) {
        messageArea.textContent = 'No client found matching "' + query + '".';
        newClientCta.classList.remove('d-none');
        return;
    }

    messageArea.textContent = clients.length + ' client(s) found. Select to validate eligibility.';

    resultArea.innerHTML = clients.map(c => {
        const initials = (c.first_name?.[0] || '') + (c.last_name?.[0] || '');
        const fullName  = [c.first_name, c.middle_name, c.last_name].filter(Boolean).join(' ');
        const lastHelp  = c.last_assistance_type
            ? `${c.last_assistance_type} — ${c.last_request_date}`
            : 'No previous assistance on record';

        return `
            <div class="client-result-card" onclick="checkEligibility(${c.id})" title="Click to validate eligibility">
                <div class="client-avatar">${initials.toUpperCase()}</div>
                <div class="flex-grow-1">
                    <div class="client-meta-name">${fullName}</div>
                    <div class="client-meta-sub">
                        <i class="fas fa-id-badge me-1"></i>ID #${c.id}
                        ${c.contact_number ? ` &nbsp;·&nbsp; <i class="fas fa-phone me-1"></i>${c.contact_number}` : ''}
                    </div>
                    <div class="client-meta-sub mt-1">
                        <i class="fas fa-history me-1"></i>${lastHelp}
                    </div>
                </div>
                <button class="btn btn-sm btn-primary px-3" onclick="checkEligibility(${c.id}); event.stopPropagation();">
                    Continue <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        `;
    }).join('');
});

async function checkEligibility(clientId) {
    // Show loading state in eligibility panel
    document.getElementById('eligibilityContent').innerHTML = `
        <div class="eligibility-waiting">
            <div class="ew-icon"><span class="spinner-border" style="width:28px;height:28px;"></span></div>
            <p class="fw-600 mb-0" style="font-weight:600;">Checking eligibility…</p>
        </div>
    `;

    const response = await fetch(`/admin/social-case-eligibility/${clientId}/check`, {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    const data = await response.json();

    if (data.eligible) {
        document.getElementById('eligibilityContent').innerHTML = `
            <div class="eligibility-result-card eligible mb-2">
                <div class="er-icon"><i class="fas fa-check-circle"></i></div>
                <div class="flex-grow-1">
                    <div class="er-title">✔ Client is Eligible</div>
                    <div class="er-detail">
                        No disqualifying assistance found within the last 6 months.<br>
                        You may proceed to Beneficiary Intake.
                    </div>
                </div>
            </div>
            <a href="/admin/social-case-eligibility/${clientId}" class="btn btn-success w-100 mt-2">
                <i class="fas fa-arrow-right me-2"></i>Proceed to Beneficiary Intake
            </a>
        `;
    } else {
        document.getElementById('eligibilityContent').innerHTML = `
            <div class="eligibility-result-card ineligible mb-2">
                <div class="er-icon"><i class="fas fa-times-circle"></i></div>
                <div class="flex-grow-1">
                    <div class="er-title">✖ Client is Not Eligible</div>
                    <div class="er-detail">
                        <strong>Reason:</strong> ${data.reason}<br>
                        <strong>Last Assistance:</strong> ${data.assistance_date} — ${data.assistance_type}<br>
                        <strong>Next Eligible Date:</strong> ${data.next_eligible_date}
                    </div>
                </div>
            </div>
            <a href="/admin/social-case-eligibility/${clientId}" class="btn btn-outline-secondary w-100 mt-2">
                <i class="fas fa-file-alt me-2"></i>View Client Record & Rejection Notice
            </a>
        `;
    }
}
</script>
@endsection
