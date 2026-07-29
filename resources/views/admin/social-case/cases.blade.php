@extends('admin.social-case.layout')
@section('title', 'All Social Case Studies')

@section('content')
<!-- Mobile Header (visible only on mobile) -->
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
            <p class="mobile-brand-subtitle">All Social Case Studies</p>
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
</style>
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases" class="active"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <!-- Modern Page Header -->
    @php
        $userName = 'Social Case Study Officer';
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
            <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">All Social Case Studies</h1>
        </div>
        <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Thursday, July 16, 2026 at 01:51 PM</div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                {{ $initials }}
            </div>
        </div>
    </header>

    <!-- Unified Table Card (matches Senior Masterlist design) -->
    <div class="sc-table-card">
        <h2 class="sc-table-card-title">Registered Social Case Studies</h2>

        <!-- Filter Section -->
        <div class="sc-filter-section">
            <div class="sc-filter-row">
                <!-- Left: Search + Filter Dropdowns -->
                <div class="sc-filter-left">
                    <div class="sc-search-group">
                        <label class="sc-filter-label">Search</label>
                        <div class="sc-input-group">
                            <input type="text" id="searchInput" placeholder="Search by name, control no..." class="sc-search-input">
                            <button type="button" class="sc-search-btn" onclick="applyFilters()">
                                <i data-lucide="search" style="width:16px;height:16px"></i>
                            </button>
                        </div>
                    </div>
                    <div class="sc-filter-row-inline">
                        <div class="sc-select-group" id="statusDropdown">
                            <label class="sc-filter-label">Status</label>
                            <div onclick="toggleStatusMenu()" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="statusBtn">
                                <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                                <span id="statusLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Status</span>
                                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
                            </div>
                            <div id="statusMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px">
                                <!-- Options populated by JavaScript -->
                            </div>
                        </div>
                        <div class="sc-select-group" id="assistanceDropdown">
                            <label class="sc-filter-label">Assistance Type</label>
                            <div onclick="toggleAssistanceMenu()" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="assistanceBtn">
                                <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                                <span id="assistanceLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Types</span>
                                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
                            </div>
                            <div id="assistanceMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px">
                                <!-- Options populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    <div class="sc-select-group" id="barangayDropdown">
                        <label class="sc-filter-label">Barangay</label>
                        <div onclick="toggleBarangayMenu()" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="barangayBtn">
                            <i data-lucide="map-pin" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                            <span id="barangayLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Barangays</span>
                            <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
                        </div>
                        <div id="barangayMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px">
                            <!-- Options populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="sc-filter-right">
                    <button class="sc-action-btn sc-action-muted" onclick="resetFilters()">
                        <i data-lucide="x" style="width:14px;height:14px"></i> Reset
                    </button>
                </div>
            </div>
        </div>


        <!-- Table -->
        <div class="sc-table-responsive">
            <table class="sc-data-table" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:14%">Control No.</th>
                        <th style="width:20%">Client</th>
                        <th style="width:16%">Assistance Type</th>
                        <th style="width:13%">Barangay</th>
                        <th style="width:11%">Status</th>
                        <th style="width:12%">Created</th>
                        <th style="width:14%">Action</th>
                    </tr>
                </thead>
                <tbody id="casesTableBody"></tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="sc-empty-state" style="display:none;">
            <i data-lucide="folder-open" style="width:56px;height:56px;color:#D1D5DB;margin-bottom:12px"></i>
            <h3>No Social Case Studies Found</h3>
            <p>Create your first Social Case Study to begin managing case records.</p>
            <a href="/admin/social-case/new" class="sc-action-btn sc-action-primary" style="display:inline-flex;margin-top:8px">
                <i data-lucide="plus" style="width:16px;height:16px"></i> Create New Case
            </a>
        </div>

        <!-- Pagination -->
        <div class="sc-pagination">
            <div class="sc-pagination-info" id="paginationInfo">Showing 0 of 0 Social Case Studies</div>
            <div class="sc-pagination-controls" id="paginationControls">
                <button class="sc-page-btn" id="prevBtn" disabled>
                    <i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous
                </button>
                <button class="sc-page-btn active" id="page1">1</button>
                <button class="sc-page-btn" id="page2">2</button>
                <button class="sc-page-btn" id="page3">3</button>
                <button class="sc-page-btn" id="nextBtn">
                    Next <i data-lucide="chevron-right" style="width:14px;height:14px"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    // Filter state
    var filterState = {
        status: 'All',
        assistance: 'All',
        barangay: 'All'
    };

    // Populate dropdown menus
    function populateDropdowns() {
        // Populate Status dropdown
        const statusMenu = document.getElementById('statusMenu');
        if(statusMenu && typeof STATUSES !== 'undefined') {
            statusMenu.innerHTML = '<div class="status-opt" data-value="All" onclick="selectStatus(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Status</div>';
            STATUSES.forEach(status => {
                statusMenu.innerHTML += `<div class="status-opt" data-value="${status}" onclick="selectStatus(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${status}</div>`;
            });
        }

        // Populate Assistance Type dropdown
        const assistanceMenu = document.getElementById('assistanceMenu');
        if(assistanceMenu && typeof PURPOSES !== 'undefined') {
            assistanceMenu.innerHTML = '<div class="assistance-opt" data-value="All" onclick="selectAssistance(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Types</div>';
            PURPOSES.forEach(purpose => {
                assistanceMenu.innerHTML += `<div class="assistance-opt" data-value="${purpose}" onclick="selectAssistance(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${purpose}</div>`;
            });
        }

        // Populate Barangay dropdown
        const barangayMenu = document.getElementById('barangayMenu');
        if(barangayMenu && typeof BARANGAYS !== 'undefined') {
            barangayMenu.innerHTML = '<div class="barangay-opt" data-value="All" onclick="selectBarangay(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Barangays</div>';
            BARANGAYS.forEach(barangay => {
                barangayMenu.innerHTML += `<div class="barangay-opt" data-value="${barangay}" onclick="selectBarangay(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${barangay}</div>`;
            });
        }
    }

    function toggleStatusMenu(){
        var menu=document.getElementById('statusMenu');
        var arrow=document.querySelector('#statusBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightStatusOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }

    function selectStatus(el){
        var val=el.getAttribute('data-value');
        filterState.status=val;
        document.getElementById('statusLabel').textContent=el.textContent;
        document.getElementById('statusMenu').style.display='none';
        var arrow=document.querySelector('#statusBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightStatusOpt();
        
        var btn=document.getElementById('statusBtn');
        if(val && val !== 'All'){
            btn.classList.add('active');
            btn.setAttribute('data-filter', val);
        } else {
            btn.classList.remove('active');
            btn.removeAttribute('data-filter');
        }
        
        applyFilters();
    }

    function highlightStatusOpt(){
        var opts=document.querySelectorAll('.status-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===filterState.status) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    function toggleAssistanceMenu(){
        var menu=document.getElementById('assistanceMenu');
        var arrow=document.querySelector('#assistanceBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightAssistanceOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }

    function selectAssistance(el){
        var val=el.getAttribute('data-value');
        filterState.assistance=val;
        document.getElementById('assistanceLabel').textContent=el.textContent;
        document.getElementById('assistanceMenu').style.display='none';
        var arrow=document.querySelector('#assistanceBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightAssistanceOpt();
        
        var btn=document.getElementById('assistanceBtn');
        if(val && val !== 'All'){
            btn.classList.add('active');
            btn.setAttribute('data-filter', val);
        } else {
            btn.classList.remove('active');
            btn.removeAttribute('data-filter');
        }
        
        applyFilters();
    }

    function highlightAssistanceOpt(){
        var opts=document.querySelectorAll('.assistance-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===filterState.assistance) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    function toggleBarangayMenu(){
        var menu=document.getElementById('barangayMenu');
        var arrow=document.querySelector('#barangayBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightBarangayOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }

    function selectBarangay(el){
        var val=el.getAttribute('data-value');
        filterState.barangay=val;
        document.getElementById('barangayLabel').textContent=el.textContent;
        document.getElementById('barangayMenu').style.display='none';
        var arrow=document.querySelector('#barangayBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightBarangayOpt();
        
        var btn=document.getElementById('barangayBtn');
        if(val && val !== 'All') btn.classList.add('active');
        else btn.classList.remove('active');
        
        applyFilters();
    }

    function highlightBarangayOpt(){
        var opts=document.querySelectorAll('.barangay-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===filterState.barangay) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    // Close menus when clicking outside
    document.addEventListener('click',function(e){
        var statusDD=document.getElementById('statusDropdown');
        var assistanceDD=document.getElementById('assistanceDropdown');
        var barangayDD=document.getElementById('barangayDropdown');
        
        if(statusDD && !statusDD.contains(e.target)){
            var menu=document.getElementById('statusMenu');
            var arrow=document.querySelector('#statusBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
        if(assistanceDD && !assistanceDD.contains(e.target)){
            var menu=document.getElementById('assistanceMenu');
            var arrow=document.querySelector('#assistanceBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
        if(barangayDD && !barangayDD.contains(e.target)){
            var menu=document.getElementById('barangayMenu');
            var arrow=document.querySelector('#barangayBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        populateDropdowns();
        loadCaseList();
    });
</script>
@endpush
