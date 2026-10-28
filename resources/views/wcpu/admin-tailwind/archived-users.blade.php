@extends('layouts.admin-tailwind')

@section('title', 'Archived Users - iSERVE SILANG Admin')
@push('styles')
<style>
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 47.9375rem) {
        html body.portal-ui #archived-users-mobile-pagination { display: none !important; }
        html body.portal-ui #archived-users-tablet-pagination { display: block !important; background: #fff; }
        html body.portal-ui #archived-users-tablet-pagination > [data-pagination-label] {
            flex-direction: column; justify-content: center; gap: .5rem; padding: .75rem; text-align: center;
        }
        html body.portal-ui #archived-users-tablet-pagination [data-pagination-label] > div:last-child {
            justify-content: center; gap: clamp(.25rem, 2vw, .625rem); max-width: 100%;
        }
        html body.portal-ui #archived-users-tablet-pagination a {
            flex: 0 0 auto; width: clamp(2rem, 10vw, 2.75rem) !important; min-width: 0 !important;
            height: 2.75rem !important; padding: 0 !important; margin: 0 !important;
            border: 1px solid #e5e7eb !important; border-radius: .5rem;
            background: #fff !important; color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        html body.portal-ui #archived-users-tablet-pagination a[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
        }
        html body.portal-ui #archived-users-tablet-pagination a > i { color: inherit !important; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui #archived-users-mobile-pagination,
        html body.portal-ui .archived-users-legacy-pagination { display: none !important; }
        html body.portal-ui #archived-users-tablet-pagination,
        html body.portal-ui .archived-users-desktop-tablet-pagination { display: block !important; grid-column: 1 / -1; background: #fff; }
        html body.portal-ui :is(#archived-users-tablet-pagination, .archived-users-desktop-tablet-pagination) > [data-pagination-label] { padding: 1rem 1.5rem; }
        html body.portal-ui :is(#archived-users-tablet-pagination, .archived-users-desktop-tablet-pagination) > [data-pagination-label] > div:last-child { margin-left: auto; justify-content: flex-end; flex-wrap: wrap; }
    }
    @media (max-width: 85.375rem) {
        html body.portal-ui #users-cards-mobile .archived-user-card-actions > :is(.view-btn, .restore-btn) {
            font-size: 0 !important;
            gap: 0 !important;
        }
        html body.portal-ui #users-cards-mobile .archived-user-card-actions > :is(.view-btn, .restore-btn) > span {
            font-size: 0 !important;
            margin: 0 !important;
        }
        html body.portal-ui #users-cards-mobile .archived-user-card-actions > :is(.view-btn, .restore-btn) > i {
            font-size: .875rem !important;
        }
    }
    @endif
    .archived-static-table { table-layout: fixed; }
    .archived-static-table th:last-child, .archived-static-table td:last-child { width: 12rem; white-space: nowrap; }
    #userModal .archived-user-modal-card { max-height: calc(100dvh - 2rem); }
    #userModal #modalContent { min-height: 0; overflow-y: auto; overscroll-behavior: contain; }
    #archived-users-tablet-pagination { display: none; }
    @if(auth()->user()->isDepartmentAdmin())
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        #archived-users-mobile-pagination { display: none !important; }
        #archived-users-tablet-pagination { display: block; }
        #archived-users-tablet-pagination > div { padding: 1rem; }
        #archived-users-tablet-pagination > div > div:last-child { flex-wrap: wrap; }
    }
    @endif
    @media (max-width: 47.9375rem)@if(auth()->user()->isDepartmentAdmin()), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)@endif {
        #users-cards-mobile .user-row > div:first-child .flex.items-center.justify-between > span {
            max-width: 45%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        #users-cards-mobile .archived-user-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        #users-cards-mobile .archived-user-card-actions > button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
        }
        #users-cards-mobile .archived-user-card-actions :is(.restore-btn, .delete-btn),
        #users-cards-mobile .archived-user-card-actions :is(.restore-btn, .delete-btn):hover {
            background-color: #C41E3A !important;
            color: #ffffff !important;
        }
        #users-cards-mobile .archived-user-card-actions :is(.restore-btn, .delete-btn) i { color: #ffffff !important; }
    }
    @if(auth()->user()->isDepartmentAdmin())
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        #archived-users-table-view { display: none !important; }
        #users-cards-mobile { display: block !important; }
    }
    @endif
</style>
@if(auth()->user()->isDepartmentAdmin())
<style>
    @media (max-width: 40rem) {
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div {
            column-gap: .75rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div > .admin-reference-mobile-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-title {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header #dashboardMenuToggle {
            display: inline-flex !important;
            position: relative !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
    }

    /* Archived Users tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header,
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header h2,
        html body.portal-ui #mainContent > header.sticky h2 {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header p,
        html body.portal-ui #mainContent > header.sticky p {
            color: #6b7280 !important;
        }
        html body.portal-ui #sidebar {
            inset: 0 auto 0 0 !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            right: auto !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            background: #C41E3A !important;
            border-radius: 0 !important;
            z-index: 40 !important;
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
            transition: width 0.3s ease !important;
        }
        html body.portal-ui #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui #sidebar .sidebar-header .sidebar-text,
        html body.portal-ui #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }
        html body.portal-ui #sidebar ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed-init ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            min-width: 0 !important;
        }
        html body.portal-ui #sidebarToggleBtn {
            display: none !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
        }
    }
</style>
@endif
@endpush

@section('content')

<!-- Top Header -->
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Users</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">Manage inactive accounts</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('admin-tailwind.components.notifications')
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="px-4 pt-4 pb-4 sm:p-6 lg:p-8">
    
    <!-- Info Banner -->
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
            <p class="text-sm text-amber-800">
                Archived users are inactive accounts that have been removed from the active users list. 
                These accounts can only be viewed and restored.
            </p>
        </div>
    </div>
    
    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-slash text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Archived Users List</h3>
                    <p class="text-sm text-gray-500">{{ $users->total() }} archived {{ Str::plural('account', $users->total()) }}</p>
                </div>
            </div>
            
            <!-- Search -->
            <div class="w-full sm:w-auto">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="user-search" placeholder="Search archived users..."
                        class="w-full sm:w-72 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Table (Desktop) -->
    <div id="archived-users-table-view" class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="archived-static-table w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Former Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barangay</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Archived Date</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="users-table-body">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors user-row" data-user-id="{{ $user->id }}" data-user-role="{{ $user->position ?? 'N/A' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Avatar" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ auth()->user()->isVawcAdmin() ? 'SWD' : (auth()->user()->isBcpcAdmin() ? 'BCPC' : strtoupper(substr($user->department ?? 'N/A', 0, 3))) }}-{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->position === 'Administrator' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->position ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->barangay ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->deleted_at ? $user->deleted_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 {{ auth()->user()->isBcpcAdmin() ? 'align-middle' : '' }}">
                            <div class="flex {{ auth()->user()->isBcpcAdmin() ? 'w-full' : '' }} items-center justify-center gap-2">
                                <button @if(auth()->user()?->isDepartmentAdmin()) style="background: #2563EB !important; border-color: #2563EB !important; color: #fff !important;" @endif type="button" class="view-btn inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-[#C41E3A] text-white border border-[#C41E3A] hover:bg-[#A01830] rounded-lg text-xs font-medium transition-colors"
                                    title="View Details" data-user-id="{{ $user->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button @if(auth()->user()?->isDepartmentAdmin()) style="background: #059669 !important; border-color: #059669 !important; color: #fff !important;" @endif type="button" class="restore-btn table-action-control inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 rounded-lg text-xs font-medium transition-colors"
                                    title="Restore User" data-user-id="{{ $user->id }}" data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No Archived Users</p>
                                <p class="text-sm text-gray-400 mt-1">When users are archived, they will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        @if(auth()->user()?->isDepartmentAdmin())
        <div class="archived-users-desktop-tablet-pagination hidden">
            @include('components.archived-pagination', ['paginator' => $users, 'label' => 'archived users', 'pageLinkLimit' => 4])
        </div>
        @endif
        <div class="archived-users-legacy-pagination px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600">
                    Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} archived users
                </p>
                <div class="flex items-center gap-1">
                    @if ($users->onFirstPage())
                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif
                    
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm rounded-lg transition-colors
                            {{ $page == $users->currentPage() ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50' }}">
                            {{ $page }}
                        </a>
                    @endforeach
                    
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Users Cards (Mobile) -->
    <div class="md:hidden space-y-4" id="users-cards-mobile">
        @forelse($users as $user)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 user-row" data-user-id="{{ $user->id }}" data-user-role="{{ $user->position ?? 'N/A' }}">
            <div class="flex items-start gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Avatar" class="w-12 h-12 rounded-full object-cover bg-gray-100 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                            {{ $user->position === 'Administrator' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $user->position ?? 'N/A' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $user->username ?? $user->email }}</p>
                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                        <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $user->barangay ?? 'N/A' }}</span>
                        <span><i class="fas fa-calendar mr-1"></i>{{ $user->deleted_at ? $user->deleted_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="archived-user-card-actions flex gap-2 mt-3 pt-3 border-t border-gray-100">
                <button @if(auth()->user()?->isDepartmentAdmin()) style="background: #2563EB !important; border-color: #2563EB !important; color: #fff !important;" @endif type="button" class="view-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-white bg-[#C41E3A] rounded-lg hover:bg-[#A01830] transition-colors"
                    data-user-id="{{ $user->id }}">
                    <i class="fas fa-eye"></i>
                    View
                </button>
                <button @if(auth()->user()?->isDepartmentAdmin()) style="background: #059669 !important; border-color: #059669 !important; color: #fff !important;" @endif type="button" class="restore-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
                    data-user-id="{{ $user->id }}" data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                    <i class="fas fa-undo"></i>
                    Restore
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No Archived Users</p>
            <p class="text-sm text-gray-400 mt-1">When users are archived, they will appear here.</p>
        </div>
        @endforelse
        
        <!-- Mobile Pagination -->
        @if($users->hasPages())
        <div id="archived-users-mobile-pagination" class="flex justify-center pt-4">
            {{ $users->links() }}
        </div>
        @if(auth()->user()->isDepartmentAdmin())
        <div id="archived-users-tablet-pagination">
            @include('components.archived-pagination', ['paginator' => $users, 'label' => 'archived users', 'pageLinkLimit' => 4])
        </div>
        @endif
        @endif
    </div>
</main>

<!-- User Details Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="modalOverlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="archived-user-modal-card bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden relative flex flex-col">
            <div class="shrink-0 bg-purple-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-user-circle"></i>
                    <span id="modalUserName">Archived User Details</span>
                </h3>
            </div>
            <div id="modalContent" class="min-h-0 flex-1 overflow-y-auto p-6">
                <!-- Content populated by JS -->
            </div>
            <div class="shrink-0 px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button id="closeModal" class="portal-modal-close px-4 py-2 text-sm font-medium text-white bg-purple-700 border border-purple-700 rounded-lg hover:bg-purple-800 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()?->isDepartmentAdmin())
    let paginationRequest;
    document.addEventListener('click', async event => {
        const link = event.target.closest('#archived-users-tablet-pagination a[href], #archived-users-table-view a[href*="page="]');
        if (!matchMedia('(max-width: 85.375rem)').matches || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.classList.contains('pointer-events-none')) return;
        event.preventDefault();
        paginationRequest?.abort();
        const controller = new AbortController();
        paginationRequest = controller;
        const cards = document.getElementById('users-cards-mobile');
        cards.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(link.href, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok || response.redirected) throw new Error('Unable to load archived users');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const replacements = ['#users-cards-mobile', '#archived-users-table-view']
                .map(selector => [document.querySelector(selector), page.querySelector(selector)]);
            if (replacements.some(([current, next]) => !current || !next)) throw new Error('Missing archived users');
            const top = cards.getBoundingClientRect().top;
            replacements.forEach(([current, next]) => current.replaceWith(next));
            history.replaceState(null, '', link.href);
            searchInput.dispatchEvent(new Event('input'));
            window.scrollBy({ top: document.getElementById('users-cards-mobile').getBoundingClientRect().top - top, behavior: 'instant' });
        } catch (error) {
            if (error.name !== 'AbortError') location.assign(link.href);
        } finally {
            cards.removeAttribute('aria-busy');
        }
    });
    @endif
    const modal = document.getElementById('userModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');
    const modalContent = document.getElementById('modalContent');
    const modalUserName = document.getElementById('modalUserName');
    const searchInput = document.getElementById('user-search');
    
    // View user details
    document.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-btn');
        if (viewBtn) {
            const userId = viewBtn.dataset.userId;
            const row = document.querySelector(`[data-user-id="${userId}"]`);
            if (row) {
                showUserDetails(row);
            }
        }
    });
    
    function showUserDetails(row) {
        // Extract user data from the row
        const userName = row.querySelector('.text-sm.font-medium, .text-sm.font-semibold')?.textContent?.trim() || 'N/A';
        const email = row.querySelector('td:nth-child(2)')?.textContent?.trim() || 
                     row.querySelector('.text-xs.text-gray-500.truncate')?.textContent?.trim() || 'N/A';
        const role = row.dataset.userRole || 'N/A';
        
        modalUserName.textContent = userName;
        modalContent.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Full Name</p>
                        <p class="text-sm font-medium text-gray-800 break-words">${userName}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Role</p>
                        <p class="text-sm font-medium text-gray-800 break-words">${role}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Email Address</p>
                        <p class="text-sm font-medium text-gray-800 break-all">${email}</p>
                    </div>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Close modal
    function hideModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    closeModal.addEventListener('click', hideModal);
    modalOverlay.addEventListener('click', hideModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hideModal();
    });
    
    // Restore user
    document.addEventListener('click', function(e) {
        const restoreBtn = e.target.closest('.restore-btn');
        if (restoreBtn) {
            const userId = restoreBtn.dataset.userId;
            const userName = restoreBtn.dataset.userName;
            
            Swal.fire({
                title: 'Restore User?',
                html: `<p>Are you sure you want to restore <strong>${userName}</strong>?</p><p class="text-sm text-gray-500 mt-2">The user will be able to log in again.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, restore',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    @if(auth()->user()?->isDepartmentAdmin())
                    Swal.fire({
                        title: 'Restoring User...',
                        text: 'Please wait while the user account is restored.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading(),
                    });
                    @endif
                    // Make API call to restore
                    fetch(`/admin/users/api/${userId}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            @if(auth()->user()?->isDepartmentAdmin())
                            'X-Skip-Loading': '1',
                            @endif
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message || 'Failed to restore user.');
                        return data;
                    })
                    .then(data => {
                        Swal.fire('Restored!', data.message || `${userName} has been restored.`, 'success')
                            .then(() => window.location.reload());
                    })
                    .catch(error => Swal.fire('Error', error.message || 'Failed to restore user. Please try again.', 'error'));
                }
            });
        }
    });
    
    // Search functionality
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (!deleteBtn) return;
        const userId = deleteBtn.dataset.userId;
        const userName = deleteBtn.dataset.userName;
        Swal.fire({ title: 'Permanently delete user?', text: `${userName} cannot be restored after this.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#b91c1c', confirmButtonText: 'Delete permanently' }).then(result => {
            if (!result.isConfirmed) return;
            fetch(`/admin/users/api/${userId}/force-delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Failed to permanently delete user.');
                    return data;
                }).then(data => {
                    Swal.fire('Deleted!', data.message || `${userName} has been permanently deleted.`, 'success')
                        .then(() => window.location.reload());
                }).catch(error => Swal.fire('Error', error.message || 'Failed to permanently delete user.', 'error'));
        });
    });

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.user-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
@endpush

@endsection
