@extends('layouts.admin-tailwind')

@section('title', 'Archived Users - iSERVE SILANG Admin')
@push('styles')
<style>
    .archived-static-table { table-layout: fixed; }
    .archived-static-table th:last-child, .archived-static-table td:last-child { width: 12rem; white-space: nowrap; }
    #userModal .archived-user-modal-card { max-height: calc(100dvh - 2rem); }
    #userModal #modalContent { min-height: 0; overflow-y: auto; overscroll-behavior: contain; }
    @media (max-width: 47.9375rem) {
        #users-cards-mobile .archived-user-card-actions {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr));
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
</style>
@endpush

@section('content')

<!-- Top Header -->
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Users</h2>
            <p class="text-sm text-gray-500 hidden sm:block">Manage inactive accounts</p>
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
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
                                <button type="button" class="view-btn inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-[#C41E3A] text-white border border-[#C41E3A] hover:bg-[#A01830] rounded-lg text-xs font-medium transition-colors"
                                    title="View Details" data-user-id="{{ $user->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="restore-btn table-action-control inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 rounded-lg text-xs font-medium transition-colors"
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
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
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
                <button type="button" class="view-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-white bg-[#C41E3A] rounded-lg hover:bg-[#A01830] transition-colors"
                    data-user-id="{{ $user->id }}">
                    <i class="fas fa-eye"></i>
                    View
                </button>
                <button type="button" class="restore-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
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
        <div class="flex justify-center pt-4">
            {{ $users->links() }}
        </div>
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
                    // Make API call to restore
                    fetch(`/admin/users/api/${userId}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
