@extends('admin.social-case.layout')
@section('title', 'All Social Case Studies')

@section('content')
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
                    <div class="sc-select-group">
                        <label class="sc-filter-label">Status</label>
                        <select id="statusFilter" class="sc-filter-select" onchange="applyFilters()">
                            <option value="All">All Status</option>
                            <option value="Draft">Draft</option>
                            <option value="Review">Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Printed">Printed</option>
                            <option value="Released">Released</option>
                        </select>
                    </div>
                    <div class="sc-select-group">
                        <label class="sc-filter-label">Assistance Type</label>
                        <select id="assistanceFilter" class="sc-filter-select" onchange="applyFilters()">
                            <option value="All">All Types</option>
                            <option value="Medical Assistance">Medical</option>
                            <option value="Burial Assistance">Burial</option>
                            <option value="Educational Assistance">Educational</option>
                            <option value="Financial Assistance">Financial</option>
                            <option value="Food / Relief Assistance">Food/Relief</option>
                            <option value="Livelihood Assistance">Livelihood</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="sc-select-group">
                        <label class="sc-filter-label">Barangay</label>
                        <select id="barangayFilter" class="sc-filter-select" onchange="applyFilters()">
                            <option value="All">All Barangays</option>
                            <option value="Biluso">Biluso</option>
                            <option value="Poblacion IV">Poblacion IV</option>
                            <option value="Tubuan">Tubuan</option>
                            <option value="Batas">Batas</option>
                            <option value="Bigaa">Bigaa</option>
                        </select>
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
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadCaseList();
    });
</script>
@endpush
