@extends('layouts.admin-tailwind')

@section('title', 'User Activity Logs - iSERVE SILANG Admin')

@push('styles')
<style>
    /* Prevent page scrolling - only table should scroll */
    html, body { overflow: hidden !important; height: 100vh !important; }
    #mainContent { height: 100vh !important; overflow: hidden !important; }
    .user-logs-action-cell {
        width: 28%;
        min-width: 0;
        overflow: hidden;
    }
    .user-logs-action-cell > div {
        min-width: 0;
        align-items: flex-start;
    }
    .user-logs-action-label {
        flex: 1 1 0%;
        min-width: 0;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    @media (max-width: 40rem) {
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

</style>
@endpush

@section('content')

<!-- Top Header -->
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-[#C41E3A] border-b border-[#C41E3A] shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            @if(auth()->user()?->isDepartmentAdmin())
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-white">User Activity Logs</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-white/80 hidden sm:block">System activity monitoring</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('admin-tailwind.components.notifications')
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-white/80">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 lg:pt-8 pb-4 flex flex-col h-[calc(100vh-80px)] overflow-hidden">
    
    <!-- Search and Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 flex-shrink-0">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">System Activity Logs</h3>
                    <p class="text-sm text-gray-500">Track all user activities</p>
                </div>
            </div>
            
            <!-- Search -->
            <form method="GET" action="{{ url()->current() }}" class="w-full sm:w-auto">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="log-search" name="search" value="{{ request('search') }}" placeholder="Search logs..."
                        class="w-full sm:w-72 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
            </form>
        </div>
    </div>
    
    <div id="logs-results" class="flex flex-col flex-1 min-h-0">
    <!-- Logs Table (Desktop) -->
    <div class="hidden md:flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex-1 min-h-0">
        <div class="overflow-auto flex-1 min-h-0">
            <table class="w-full table-fixed" style="width: 100% !important; min-width: 0 !important; table-layout: fixed !important;">
                <colgroup>
                    <col style="width: 12%">
                    <col style="width: 10%">
                    <col style="width: 28%">
                    <col style="width: 30%">
                    <col style="width: 20%">
                </colgroup>
                <thead class="bg-purple-700 text-white sticky top-0">
                    <tr>
                        <th class="w-[12%] px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Date</th>
                        <th class="w-[10%] px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Time</th>
                        <th class="w-[28%] px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Action</th>
                        <th class="w-[30%] px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">User</th>
                        <th class="w-[20%] px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="logs-table-body">
                    @forelse($activities as $log)
                    @php
                        $userDisplay = data_get($log, 'user', 'System');
                        $role = data_get($log, 'role', 'System');
                        $causerId = data_get($log, 'causer.id');
                        $avatarName = str_replace($causerId ? ($causerId . ' - ') : '', '', $userDisplay);
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($avatarName) . '&background=6a1b9a&color=fff&size=32';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors log-item">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ data_get($log, 'date', 'N/A') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ data_get($log, 'time', 'N/A') }}</td>
                        <td class="user-logs-action-cell px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fas {{ data_get($log, 'icon', 'fa-info-circle') }} text-purple-600"></i>
                                <span class="user-logs-action-label text-sm text-gray-800">{{ data_get($log, 'action', 'Unknown Action') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $avatarUrl }}" alt="User" class="w-8 h-8 rounded-full flex-shrink-0">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $userDisplay }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($role) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button type="button" class="view-details-btn inline-flex w-11 h-11 items-center justify-center bg-[#C41E3A] text-white rounded-lg hover:bg-[#A01830] transition-colors text-xs font-medium"
                                data-details='@json(data_get($log, "full_details", []), JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG)'
                                aria-haspopup="dialog" aria-controls="activityModal">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-history text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500">No activity logs found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        @if($activities->hasPages())
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="text-sm text-gray-600">
                Showing {{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} logs
            </div>
            <div class="flex items-center gap-2">
                {{-- Previous --}}
                @if($activities->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 border border-gray-200 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-chevron-left text-gray-400"></i>
                </span>
                @else
                <a href="{{ $activities->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </a>
                @endif
                
                {{-- Page Numbers --}}
                @php
                    $currentPage = $activities->currentPage();
                    $lastPage = $activities->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $start + 4);
                    if ($end - $start < 4) {
                        $start = max(1, $end - 4);
                    }
                @endphp
                
                @for($i = $start; $i <= $end; $i++)
                    @if($i == $currentPage)
                    <span class="inline-flex items-center justify-center w-9 h-9 bg-[#C41E3A] text-white border border-[#C41E3A] rounded-lg text-sm font-medium">{{ $i }}</span>
                    @else
                    <a href="{{ $activities->url($i) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm text-gray-600">{{ $i }}</a>
                    @endif
                @endfor
                
                {{-- Next --}}
                @if($activities->hasMorePages())
                <a href="{{ $activities->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </a>
                @else
                <span class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 border border-gray-200 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>
    
    <!-- Logs Cards (Mobile) -->
    <div class="md:hidden flex flex-col flex-1 min-h-0">
        <div id="logs-cards-mobile" class="space-y-4 overflow-auto flex-1">
            @forelse($activities as $log)
            @php
                $userDisplay = data_get($log, 'user', 'System');
                $role = data_get($log, 'role', 'System');
                $avatarName = str_replace(data_get($log, 'causer.id') ? (data_get($log, 'causer.id') . ' - ') : '', '', $userDisplay);
                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($avatarName) . '&background=6a1b9a&color=fff&size=40';
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 log-item cursor-pointer hover:border-purple-300 transition-colors"
                data-details='@json(data_get($log, "full_details", []), JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG)'
                role="button" tabindex="0" aria-haspopup="dialog" aria-controls="activityModal">
                <div class="flex items-start gap-3">
                    <img src="{{ $avatarUrl }}" alt="User" class="w-10 h-10 rounded-full flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $userDisplay }}</p>
                            <span class="text-xs text-gray-500 whitespace-nowrap">{{ data_get($log, 'time', '') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ ucfirst($role) }} • {{ data_get($log, 'date', 'N/A') }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas {{ data_get($log, 'icon', 'fa-info-circle') }} text-purple-600"></i>
                            <span class="truncate">{{ data_get($log, 'action', 'Unknown Action') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-history text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500">No activity logs found</p>
            </div>
            @endforelse
        </div>
        
        <!-- Mobile Pagination -->
        @if($activities->hasPages())
        <div class="mt-4 px-4 py-3 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="text-sm text-gray-600">
                Page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}
            </div>
            <div class="flex items-center gap-2">
                @if($activities->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 border border-gray-200 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-chevron-left text-gray-400"></i>
                </span>
                @else
                <a href="{{ $activities->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </a>
                @endif
                
                @if($activities->hasMorePages())
                <a href="{{ $activities->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </a>
                @else
                <span class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 border border-gray-200 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>
    </div>
</main>

<!-- Activity Details Modal -->
<div id="activityModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="modalOverlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden relative">
            <!-- Modal Header -->
            <div class="bg-purple-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Activity Details
                </h3>
                <button id="closeModal" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]" id="activityDetails">
                <!-- Content populated by JS -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('activityModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');
    const activityDetails = document.getElementById('activityDetails');
    const searchInput = document.getElementById('log-search');
    
    // Open modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.view-details-btn');
        const mobileCard = e.target.closest('.log-item[data-details]');
        
        if (btn || (mobileCard && window.innerWidth < 768)) {
            const target = btn || mobileCard;
            try {
                const details = JSON.parse(target.getAttribute('data-details'));
                showDetails(details);
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error parsing activity details:', error);
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        const mobileCard = e.target.closest('.log-item[data-details]');
        if (mobileCard && window.innerWidth < 768 && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            mobileCard.click();
        }
    });
    
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
    
    // Show details in modal
    function showDetails(details) {
        let html = '<div class="space-y-4">';
        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        
        const fields = [
            { key: 'action', label: 'Action', icon: 'fa-bolt' },
            { key: 'user', label: 'User', icon: 'fa-user' },
            { key: 'role', label: 'Role', icon: 'fa-user-tag' },
            { key: 'date', label: 'Date', icon: 'fa-calendar' },
            { key: 'time', label: 'Time', icon: 'fa-clock' },
            { key: 'description', label: 'Description', icon: 'fa-align-left' }
        ];
        
        fields.forEach(field => {
            const value = details[field.key];
            if (value) {
                html += `
                    <div class="flex items-start gap-3 py-3 border-b border-gray-100">
                        <i class="fas ${field.icon} text-purple-600 mt-0.5 w-5"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">${field.label}</p>
                            <p class="text-sm text-gray-800 break-words">${escapeHtml(value)}</p>
                        </div>
                    </div>
                `;
            }
        });
        
        html += '</div>';
        activityDetails.innerHTML = html;
    }
    
});
</script>
@if(auth()->user()->defaultCaseDepartment() === 'BCPC')
<script>
(() => {
    const refreshLogs = async (url) => {
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) return;
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const replacement = page.querySelector('#logs-results');
        const current = document.querySelector('#logs-results');
        if (!replacement || !current) return;
        current.innerHTML = replacement.innerHTML;
        history.replaceState({}, '', url);
    };

    document.addEventListener('input', (event) => {
        if (event.target.id !== 'log-search') return;
        const form = event.target.closest('form');
        const url = new URL(form.action, location.origin);
        url.search = new URLSearchParams(new FormData(form)).toString();
        refreshLogs(url);
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('#logs-results a[href]');
        if (!link) return;
        const url = new URL(link.href, location.origin);
        if (url.origin !== location.origin || !url.searchParams.has('page')) return;
        event.preventDefault();
        refreshLogs(url);
    });
})();
</script>
@endif
@if(auth()->user()->isVawcAdmin())
<script>
(() => {
    const refreshLogs = async (url) => {
        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!response.ok) return;
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const replacement = page.querySelector('#logs-results');
        const current = document.querySelector('#logs-results');
        if (!replacement || !current) return;
        current.innerHTML = replacement.innerHTML;
        history.replaceState({}, '', url);
    };

    document.addEventListener('input', (event) => {
        if (event.target.id !== 'log-search') return;
        const form = event.target.closest('form');
        const url = new URL(form.action, location.origin);
        url.search = new URLSearchParams(new FormData(form)).toString();
        refreshLogs(url);
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('#logs-results a[href]');
        if (!link) return;
        const url = new URL(link.href, location.origin);
        if (url.origin !== location.origin || !url.searchParams.has('page')) return;
        event.preventDefault();
        refreshLogs(url);
    });
})();
</script>
@endif
@endpush

@endsection
