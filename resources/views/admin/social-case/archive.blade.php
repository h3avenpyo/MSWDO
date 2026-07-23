@extends('admin.social-case.layout')
@section('title', 'Archive - Social Case Study')

@section('content')
<style>
    html,body{overflow-x:hidden!important;overflow-y:auto!important}
    .app{min-height:auto!important}
    .main{display:flex!important;flex-direction:column!important;overflow-x:hidden!important;overflow-y:auto!important}
    #archiveSearch:focus{border-color:#1A237E;box-shadow:0 0 0 3px rgba(26,35,126,.08)}
    .archive-type-opt.selected,.archive-brgy-opt.selected{background:#F3F4F6;font-weight:600}
    .archive-type-opt:not(.selected):hover,.archive-brgy-opt:not(.selected):hover{background:#F3F4F6}
    
    /* Color-coded filter buttons when active */
    #archiveBrgyBtn.active {
        border-color: #059669;
        background: #ECFDF5;
        color: #065F46;
    }
    #archiveBrgyBtn.active i[data-lucide="map-pin"] { color: #059669; }
    
    #archiveTypeBtn.active {
        border-color: #1A237E;
        background: #EEF2FF;
    }
    #archiveTypeBtn.active i[data-lucide="filter"] { color: #1A237E; }
    
    /* Type-specific button colors when active */
    #archiveTypeBtn.active[data-filter="Medical Assistance"]      { border-color:#2563EB; background:#DBEAFE; color:#1E40AF; }
    #archiveTypeBtn.active[data-filter="Burial Assistance"]       { border-color:#DC2626; background:#FEE2E2; color:#991B1B; }
    #archiveTypeBtn.active[data-filter="Educational Assistance"]  { border-color:#D97706; background:#FEF3C7; color:#92400E; }
    #archiveTypeBtn.active[data-filter="Financial Assistance"]    { border-color:#059669; background:#D1FAE5; color:#065F46; }
    #archiveTypeBtn.active[data-filter="Food / Relief Assistance"]{ border-color:#4F46E5; background:#E0E7FF; color:#3730A3; }
    #archiveTypeBtn.active[data-filter="Livelihood Assistance"]   { border-color:#DB2777; background:#FCE7F3; color:#9D174D; }
    #archiveTypeBtn.active[data-filter="Other"]                   { border-color:#6B7280; background:#F3F4F6; color:#374151; }
    
    #archiveTypeBtn.active[data-filter] i[data-lucide="filter"]   { color: inherit; }
    #archiveTypeBtn.active[data-filter] #archiveTypeLabel         { color: inherit; }

    /* Color-coded filter dropdown options */
    .archive-type-opt[data-value="Medical Assistance"].selected { background:#DBEAFE; color:#1E40AF; }
    .archive-type-opt[data-value="Burial Assistance"].selected { background:#FEE2E2; color:#991B1B; }
    .archive-type-opt[data-value="Educational Assistance"].selected { background:#FEF3C7; color:#92400E; }
    .archive-type-opt[data-value="Financial Assistance"].selected { background:#D1FAE5; color:#065F46; }
    .archive-type-opt[data-value="Food / Relief Assistance"].selected { background:#E0E7FF; color:#3730A3; }
    .archive-type-opt[data-value="Livelihood Assistance"].selected { background:#FCE7F3; color:#9D174D; }
    .archive-type-opt[data-value="Other"].selected { background:#F3F4F6; color:#374151; }

    @media (max-width: 767px) {
        /* Archive: card layout for table */
        .archive-panel-wrap { padding: 1rem !important; margin-bottom: 1rem !important; border-radius: 12px; background: var(--surface); border: 1px solid var(--border); }
        .archive-filter-bar { flex-direction: column !important; gap: 10px !important; padding: 12px !important; }
        .archive-filter-bar > div { min-width: 0 !important; max-width: none !important; width: 100% !important; }
        .archive-table-wrap { border: none !important; overflow: visible !important; }
        .archive-table { width: 100%; }
        .archive-table thead { display: none; }
        .archive-table tbody tr {
            display: block;
            background: var(--surface);
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            margin-bottom: 10px;
            padding: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .archive-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border: none;
            font-size: 0.82rem;
            gap: 8px;
        }
        .archive-table tbody td:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }
        .archive-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            flex-shrink: 0;
            min-width: 70px;
        }
        .archive-table tbody td[data-label="Action"] {
            justify-content: flex-end;
            padding-top: 8px;
            border-bottom: none;
        }
        .archive-table tbody td[data-label="Action"]::before { display: none; }
        .archive-table tbody td:not([data-label]) { justify-content: center; text-align: center; }
        .archive-table tbody td:not([data-label])::before { display: none; }
        .archive-table tbody td .actions { justify-content: flex-end; }
        .archive-table tbody td .badge { font-size: 0.7rem; }
        .sc-pagination { gap: 8px; margin-top: 1rem; }
        .sc-page-btn { height: 34px; min-width: 34px; font-size: 0.8rem; padding: 0 0.5rem; }
    }
    @media (max-width: 479px) {
        .archive-panel-wrap { padding: 0.75rem !important; }
        .archive-table tbody td::before { min-width: 60px; font-size: 0.68rem; }
        .archive-table tbody td { font-size: 0.78rem; }
    }
</style>
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="/admin/social-case/archive" class="active"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <!-- Modern Page Header -->
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
    <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
        <div class="flex items-center">
            <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Archived Cases</h1>
        </div>
        <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Thursday, July 16, 2026 at 01:51 PM</div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                {{ $initials }}
            </div>
        </div>
    </header>

    <!-- Page Sub-Header -->
    <div class="mb-6">
        <p class="text-[#6B7280] text-sm m-0">View and manage archived social case study records.</p>
    </div>

    <!-- Search and Filter Bar -->
    <div class="archive-filter-bar" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px">
        <div style="max-width:280px;width:100%;flex-shrink:0">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Search by Name</label>
            <div style="display:flex;align-items:center;height:44px;">
                <input type="text" id="archiveSearch" placeholder="Search by name..."
                       oninput="view.archiveSearch=this.value;view.archivePage=1;renderArchive()"
                       style="flex:1;height:44px;border:1px solid #E5E7EB;border-right:none;border-radius:6px 0 0 6px;padding:0 1rem;font-size:0.875rem;color:#111827;background:#fff;transition:all .2s ease;outline:none;">
                <button type="button" onclick="renderArchive()" style="background:#1A237E;color:#fff;border:none;padding:0 1.25rem;border-radius:0 6px 6px 0;cursor:pointer;height:44px;display:flex;align-items:center;justify-content:center;transition:background .2s;">
                    <i data-lucide="search" style="width:16px;height:16px"></i>
                </button>
            </div>
        </div>
        <div style="position:relative;min-width:180px" id="archiveBrgyDropdown">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Filter by Barangay</label>
            <div onclick="toggleArchiveBrgyMenu()" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="archiveBrgyBtn">
                <i data-lucide="map-pin" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="archiveBrgyLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Barangays</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
            </div>
            <div id="archiveBrgyMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px">
                <div class="archive-brgy-opt" data-value="" onclick="selectArchiveBrgy(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Barangays</div>
            </div>
        </div>
        <div style="position:relative;min-width:180px" id="archiveTypeDropdown">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Filter by Type</label>
            <div onclick="toggleArchiveTypeMenu()" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="archiveTypeBtn">
                <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="archiveTypeLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Types</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
            </div>
            <div id="archiveTypeMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px">
                <div class="archive-type-opt" data-value="" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Types</div>
                <div class="archive-type-opt" data-value="Medical Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Medical Assistance</div>
                <div class="archive-type-opt" data-value="Burial Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Burial Assistance</div>
                <div class="archive-type-opt" data-value="Educational Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Educational Assistance</div>
                <div class="archive-type-opt" data-value="Financial Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Financial Assistance</div>
                <div class="archive-type-opt" data-value="Food / Relief Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Food / Relief Assistance</div>
                <div class="archive-type-opt" data-value="Livelihood Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Livelihood Assistance</div>
                <div class="archive-type-opt" data-value="Other" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Other</div>
            </div>
        </div>
    </div>

    <div class="panel archive-panel-wrap" style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-height:0;margin-bottom:0">
        <div class="archive-table-wrap" style="flex:1;overflow:auto;min-height:0;border-radius:8px">
            <table class="archive-table">
                <thead><tr><th>Control No</th><th>Client</th><th>Assistance Type</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody id="archiveTable"></tbody>
            </table>
        </div>

        <div class="sc-pagination">
            <div class="sc-pagination-info" id="archivePaginationInfo">Showing 0 of 0 Archived Cases</div>
            <div class="sc-pagination-controls" id="archivePaginationControls"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    function toggleArchiveTypeMenu(){
        var menu=document.getElementById('archiveTypeMenu');
        var btn=document.getElementById('archiveTypeBtn');
        var arrow=btn.querySelector('[data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightArchiveTypeOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }
    function selectArchiveType(el){
        var val=el.getAttribute('data-value');
        view.archiveFilter=val;
        view.archivePage=1;
        document.getElementById('archiveTypeLabel').textContent=el.textContent;
        document.getElementById('archiveTypeMenu').style.display='none';
        var arrow=document.querySelector('#archiveTypeBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightArchiveTypeOpt();
        
        // Add active class and data-filter color to button
        var btn=document.getElementById('archiveTypeBtn');
        if(val){
            btn.classList.add('active');
            btn.setAttribute('data-filter', val);
        } else {
            btn.classList.remove('active');
            btn.removeAttribute('data-filter');
        }
        
        renderArchive();
    }
    function highlightArchiveTypeOpt(){
        var opts=document.querySelectorAll('.archive-type-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===view.archiveFilter) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }
    function toggleArchiveBrgyMenu(){
        var menu=document.getElementById('archiveBrgyMenu');
        var arrow=document.querySelector('#archiveBrgyBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightArchiveBrgyOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }
    function selectArchiveBrgy(el){
        var val=el.getAttribute('data-value');
        view.archiveBarangay=val;
        view.archivePage=1;
        document.getElementById('archiveBrgyLabel').textContent=el.textContent;
        document.getElementById('archiveBrgyMenu').style.display='none';
        var arrow=document.querySelector('#archiveBrgyBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightArchiveBrgyOpt();
        
        // Add active class to button if filter is selected
        var btn=document.getElementById('archiveBrgyBtn');
        if(val) btn.classList.add('active');
        else btn.classList.remove('active');
        
        renderArchive();
    }
    function highlightArchiveBrgyOpt(){
        var opts=document.querySelectorAll('.archive-brgy-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===view.archiveBarangay) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }
    document.addEventListener('click',function(e){
        var typeDD=document.getElementById('archiveTypeDropdown');
        var brgyDD=document.getElementById('archiveBrgyDropdown');
        if(typeDD && !typeDD.contains(e.target)){
            var menu=document.getElementById('archiveTypeMenu');
            var arrow=document.querySelector('#archiveTypeBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
        if(brgyDD && !brgyDD.contains(e.target)){
            var menu=document.getElementById('archiveBrgyMenu');
            var arrow=document.querySelector('#archiveBrgyBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadArchive();
    });
</script>
@endpush
