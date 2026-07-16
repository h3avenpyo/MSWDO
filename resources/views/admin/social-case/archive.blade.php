@extends('admin.social-case.layout')
@section('title', 'Archive - Social Case Study')

@section('content')
<style>
    html,body{overflow:hidden!important;height:100vh!important}
    .app{min-height:auto!important;height:100vh!important;overflow:hidden!important}
    .main{display:flex!important;flex-direction:column!important;overflow:hidden!important}
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
    <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8"
            style="margin-top: calc(-1 * var(--content-padding)); margin-left: calc(-1 * var(--content-padding)); margin-right: calc(-1 * var(--content-padding));">
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

    <div class="panel" style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-height:0;margin-bottom:0">
        <div style="flex:1;overflow:auto;min-height:0;border-radius:8px">
            <table>
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
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadArchive();
    });
</script>
@endpush
