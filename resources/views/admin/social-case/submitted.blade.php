@extends('admin.social-case.layout')
@section('title', 'Submitted Cases')

@section('content')
@php
$logo = null;
if(file_exists(public_path('images/mswdo-logo.png'))){
    $logo='mswdo-logo.png';
}else{
    $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
    if(!empty($files))
    $logo=basename($files[0]);
}
@endphp
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">Submitted Cases</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<style>
    html,body{overflow-x:hidden!important;overflow-y:auto!important}
    .app{min-height:auto!important}
    .main{display:flex!important;flex-direction:column!important;overflow-x:hidden!important;overflow-y:auto!important}
    @media (min-width:1200px){
        html,body{overflow:hidden!important}
        .main{overflow-y:auto!important}
    }
    #submittedTable{width:100%!important;table-layout:fixed;border-collapse:collapse;}
    .archive-table-wrap{overflow-x:auto;}
    #submittedTable th,#submittedTable td{
        padding:12px 14px;
        text-align:left;
        vertical-align:middle;
        white-space:normal;
        overflow-wrap:break-word;
        word-wrap:break-word;
    }
    #submittedTable th{
        padding:14px;
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:0.03em;
        color:var(--text-secondary);
    }
    #submittedTable td[data-label="Control No."]{
        font-size:13px;
    }
    #submittedTable td[data-label="Date Submitted"]{
        white-space:nowrap;
    }
    #submittedTable td[data-label="Action"] .btn{
        width:100%;
        justify-content:center;
        white-space:nowrap;
        padding:6px 10px;
    }

    /* Base empty state styles matching Archive page */
    .empty-row {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .empty-cell {
        padding: 2.5rem 1rem !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
    }
    .empty-cell::before { display: none !important; }
    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .empty-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #9CA3AF;
    }
    .empty-icon-wrap svg {
        width: 32px;
        height: 32px;
    }
    .empty-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 4px;
    }
    .empty-subtitle {
        font-size: 0.875rem;
        color: #6B7280;
    }

    /* Tablet (768-1199px): empty state stays a full-width centered row */
    @media (min-width: 768px) and (max-width: 1199px) {
        #submittedTable tbody tr.empty-row { display: table-row !important; background: transparent !important; border: none !important; box-shadow: none !important; margin: 0 !important; }
        #submittedTable tbody tr.empty-row td.empty-cell {
            display: table-cell !important;
            padding: 2.5rem 1.5rem !important;
            border: none !important;
            text-align: center !important;
        }
        #submittedTable tbody tr.empty-row td.empty-cell::before { display: none !important; }
        #submittedTable tbody tr.empty-row td.empty-cell .empty-state-content { align-items: center; justify-content: center; }
    }

    @media (min-width: 1200px) {
        #submittedTable tbody tr.empty-row { display: table-row !important; background: transparent !important; border: none !important; box-shadow: none !important; margin: 0 !important; }
        #submittedTable tbody tr.empty-row td.empty-cell {
            display: table-cell !important;
            padding: 3rem 1.5rem !important;
            border: none !important;
            text-align: center !important;
        }
        #submittedTable tbody tr.empty-row td.empty-cell::before { display: none !important; }

        .empty-icon-wrap {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            background: #EEF2FF;
            color: #1A237E;
        }
        .empty-icon-wrap svg {
            width: 40px !important;
            height: 40px !important;
        }
        .empty-title {
            font-size: 1.35rem !important;
            font-weight: 700 !important;
            color: #111827 !important;
            margin-bottom: 8px !important;
        }
        .empty-subtitle {
            font-size: 0.95rem !important;
            color: #6B7280 !important;
            max-width: 400px;
            line-height: 1.5;
        }
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>New case</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li><a href="#" onclick="return false" style="opacity:0.5;pointer-events:none;cursor:not-allowed" title="Not available for eligibility checker accounts"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @else
        <li><a href="/admin/social-case/submitted" class="active"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @endif
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <div class="mb-6">
        <p class="text-[#6B7280] text-sm m-0">Clients forwarded by the Eligibility Checker and waiting to be encoded.</p>
    </div>

    <!-- Search Bar -->
    <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px">
        <div style="max-width:320px;width:100%;flex-shrink:0;display:flex;flex-direction:column;justify-content:flex-end">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Search Client</label>
            <div style="display:flex;align-items:center;height:44px;">
                <input type="text" id="submittedSearch" placeholder="Search client name..."
                       oninput="filterSubmitted()"
                       style="flex:1;height:44px;border:1px solid #E5E7EB;border-right:none;border-radius:6px 0 0 6px;padding:0 1rem;font-size:0.875rem;color:#111827;background:#fff;transition:all .2s ease;outline:none;">
                <button type="button" onclick="filterSubmitted()" style="background:#1A237E;color:#fff;border:none;padding:0 1.25rem;border-radius:0 6px 6px 0;cursor:pointer;height:44px;display:flex;align-items:center;justify-content:center;transition:background .2s;">
                    <i data-lucide="search" style="width:16px;height:16px"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Panel -->
    <div class="panel archive-panel-wrap">
        <div class="archive-table-wrap">
            <table class="archive-table" id="submittedTable" style="table-layout:fixed;width:100%;border-collapse:collapse">
                <colgroup>
                    <col style="width:17%">
                    <col style="width:26%">
                    <col style="width:26%">
                    <col style="width:14%">
                    <col style="width:17%">
                </colgroup>
                <thead>
                    <tr>
                        <th>Control No.</th>
                        <th>Client</th>
                        <th>Forwarded By</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submitted as $c)
                    <tr data-name="{{ mb_strtolower($c->client ? $c->client->full_name : '') }}">
                        <td data-label="Control No."><span class="control-no" title="{{ $c->case_number }}">{{ $c->case_number ?: '—' }}</span></td>
                        <td data-label="Client" title="{{ $c->client ? $c->client->full_name : '' }}">{{ $c->client ? $c->client->full_name : 'Unnamed' }}</td>
                        <td data-label="Forwarded By" title="{{ $c->eligibleByUser ? $c->eligibleByUser->name : 'Eligibility Checker' }}">{{ $c->eligibleByUser ? $c->eligibleByUser->name : 'Eligibility Checker' }}</td>
                        <td data-label="Date Submitted">{{ $c->eligible_at ? \Carbon\Carbon::parse($c->eligible_at)->format('M d, Y') : '—' }}</td>
                        <td data-label="Action">
                            <button class="btn primary btn-sm" onclick="startEncodingFromQueue('{{ $c->id }}', '{{ $c->client ? addslashes($c->client->full_name) : '' }}')">
                                <i data-lucide="pen-line" style="width:14px;height:14px"></i> Encode
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="5" class="empty-cell">
                            <div class="empty-state-content">
                                <div class="empty-icon-wrap">
                                    <i data-lucide="send"></i>
                                </div>
                                <div class="empty-title">No submitted cases</div>
                                <div class="empty-subtitle">Submitted cases will appear here</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
    function filterSubmitted(){
        var query = (document.getElementById('submittedSearch').value || '').trim().toLowerCase();
        var dataRows = document.querySelectorAll('#submittedTable tbody tr:not(.empty-row)');
        var visibleCount = 0;

        dataRows.forEach(function(row){
            var name = (row.getAttribute('data-name') || '').toLowerCase();
            var match = (!query || name.indexOf(query) !== -1);
            row.style.display = match ? '' : 'none';
            if(match) visibleCount++;
        });

        var emptyRow = document.querySelector('#submittedTable tbody tr.empty-row');
        if (dataRows.length > 0) {
            if (visibleCount === 0) {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-row';
                    emptyRow.innerHTML = '<td colspan="5" class="empty-cell">' +
                        '<div class="empty-state-content">' +
                        '<div class="empty-icon-wrap"><i data-lucide="search-x"></i></div>' +
                        '<div class="empty-title">No matching submitted cases</div>' +
                        '<div class="empty-subtitle">Try adjusting your search</div>' +
                        '</div></td>';
                    document.querySelector('#submittedTable tbody').appendChild(emptyRow);
                } else {
                    emptyRow.style.display = '';
                    emptyRow.querySelector('.empty-title').textContent = 'No matching submitted cases';
                    emptyRow.querySelector('.empty-subtitle').textContent = 'Try adjusting your search';
                    var iconWrap = emptyRow.querySelector('.empty-icon-wrap');
                    if (iconWrap) iconWrap.innerHTML = '<i data-lucide="search-x"></i>';
                }
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endpush
