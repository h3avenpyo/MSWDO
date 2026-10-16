@extends('layouts.admin-tailwind')

@section('title', 'User Information - iSERVE SILANG Admin')

@push('styles')
<style>
    #mainContent .inline-flex.items-center { justify-content: center; }
    .admin-user-modal-card {
        box-sizing: border-box;
        width: min(32rem, calc(100vw - 2rem));
        max-width: min(32rem, calc(100vw - 2rem));
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .admin-user-modal-card > :first-child { flex: 0 0 auto; }
    #userModalContent,
    #editModalContent { min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior-y: contain; }
    /* Phone-only profile cards. The existing desktop/tablet table remains
       unchanged from the md breakpoint upward. */
    @media (max-width: 47.9375rem) {
        #users-desktop-table { display: none !important; }
        #users-mobile-view {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr);
            gap: .75rem;
            padding: .75rem;
            align-content: start;
            background-color: #f9fafb;
        }
        #users-mobile-view .user-profile-card {
            min-width: 0;
            padding: .875rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / .05);
        }
        #users-mobile-view .user-profile-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        #users-mobile-view .user-profile-card-actions > button {
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
        }
        #users-mobile-view .user-profile-card-actions .archive-user-action,
        #users-mobile-view .user-profile-card-actions .archive-user-action:hover {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }
        #users-mobile-view .user-profile-card-actions .archive-user-action i { color: #ffffff !important; }
    }
    @media (min-width: 40.0625rem) and (max-width: 47.9375rem) {
        #users-mobile-view { grid-template-columns: repeat(auto-fit, minmax(min(100%, 20rem), 1fr)); padding: 1rem; gap: 1rem; }
        #users-mobile-view .user-profile-card { padding: 1rem; }
        #users-mobile-view .user-profile-card-actions { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        /* Prevent page scrolling on desktop - only table should scroll */
        html, body { overflow: hidden !important; height: 100vh !important; }
        #mainContent { height: 100vh !important; overflow: hidden !important; }
    }
</style>
@endpush

@section('content')

@php($adminCaseDepartments = Auth::user()->configuredCaseDepartments())

<!-- Top Header -->
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">User Information</h2>
            <p class="text-sm text-gray-500 hidden sm:block">Manage user accounts and activity</p>
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

<!-- Tab Navigation -->
<div class="bg-white border-b border-gray-200 overflow-x-auto">
    <div class="px-4 sm:px-6 lg:px-8">
        <nav class="flex gap-4 sm:gap-6 min-w-max" aria-label="Tabs">
            <a 
                href="{{ route('admin.users.index') }}" 
                class="inline-flex items-center gap-2 px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap {{ !request('tab') || request('tab') == 'all-users' ? 'border-[#C41E3A] text-[#C41E3A]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-colors"
            >
                <i class="fas fa-users"></i>
                <span class="hidden sm:inline">All Users</span>
                <span class="sm:hidden">Users</span>
            </a>
            <a 
                href="{{ route('admin.users.index', ['tab' => 'password-requests']) }}" 
                class="inline-flex items-center gap-2 px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap {{ request('tab') == 'password-requests' ? 'border-[#C41E3A] text-[#C41E3A]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-colors"
            >
                <i class="fas fa-key"></i>
                <span class="hidden sm:inline">Password Requests</span>
                <span class="sm:hidden">Requests</span>
                @if(isset($pendingRequestsCount) && $pendingRequestsCount > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $pendingRequestsCount }}</span>
                @endif
            </a>
            <a 
                href="{{ route('admin.users.index', ['tab' => 'account-settings']) }}" 
                class="inline-flex items-center gap-2 px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap {{ request('tab') == 'account-settings' ? 'border-[#C41E3A] text-[#C41E3A]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-colors"
            >
                <i class="fas fa-user-cog"></i>
                <span class="hidden sm:inline">Account Settings</span>
                <span class="sm:hidden">Settings</span>
            </a>
        </nav>
    </div>
</div>

<!-- Main Content -->
<main class="px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 lg:pt-8 pb-4 flex flex-col h-[calc(100vh-130px)] overflow-hidden">
    
    @if(!request('tab') || request('tab') == 'all-users')
    {{-- ALL USERS TAB --}}
    @if(isset($passwordResetRequest) && $passwordResetRequest && !session('dismissed_password_request_' . $passwordResetRequest->id))
    <!-- Password Reset Request Alert -->
    <div id="password-reset-alert" class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-key text-white text-lg"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-amber-800 font-semibold flex items-center gap-2 mb-1">
                    <i class="fas fa-exclamation-circle"></i>
                    Password Reset Request
                </h3>
                <p class="text-amber-700 text-sm mb-3">
                    User <strong>{{ $passwordResetRequest->username }}</strong> has requested a password reset.
                    <span class="block text-amber-600 text-xs mt-1">
                        <i class="far fa-clock"></i> Requested {{ $passwordResetRequest->requested_at->diffForHumans() }}
                    </span>
                </p>
                <div class="flex gap-3 flex-wrap">
                    <button 
                        type="button"
                        data-request-id="{{ $passwordResetRequest->id }}"
                        onclick="openPasswordResetModal({{ $passwordResetRequest->id }}, '{{ $passwordResetRequest->username }}', {{ $passwordResetRequest->user_id }})"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-green-700 transition-all shadow-sm"
                    >
                        <i class="fas fa-check-circle"></i> Approve & Reset
                    </button>
                    <button 
                        type="button"
                        data-request-id="{{ $passwordResetRequest->id }}"
                        onclick="openRejectModal({{ $passwordResetRequest->id }}, '{{ $passwordResetRequest->username }}')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-red-600 border-2 border-red-500 rounded-lg text-sm font-medium hover:bg-red-500 hover:text-white transition-all"
                    >
                        <i class="fas fa-times-circle"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Search and Filter Section -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6 flex-shrink-0">
        <!-- Search Box -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input 
                type="text" 
                id="user-search" 
                placeholder="Search users by name or email..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C41E3A] focus:border-[#C41E3A] transition-colors text-sm"
            >
        </div>
        
        <!-- Filter and Actions -->
        <div class="flex gap-3">
            <!-- Status Filter -->
            <select 
                id="status-filter"
                class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C41E3A] text-sm bg-white"
            >
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            
            <button id="clear-user-filters" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#C41E3A] text-white rounded-lg hover:bg-[#A01830] transition-colors text-sm font-medium shadow-sm">
                <i class="fas fa-filter-circle-xmark"></i><span>Clear filters</span>
            </button>
        </div>
    </div>
    
    <!-- Users Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col flex-1 min-h-0">
        <!-- Desktop Table -->
        <div id="users-desktop-table" class="hidden md:block overflow-auto flex-1">
            <table id="users-table" class="w-full table-fixed">
                <thead class="bg-[#C41E3A] text-white sticky top-0">
                    <tr>
                        <th class="w-[25%] px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">User</th>
                        <th class="w-[25%] px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
                        <th class="w-[20%] px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Barangay</th>
                        <th class="w-[10%] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">Status</th>
                        <th class="w-[20%] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="divide-y divide-gray-100">
                    <!-- Loading State -->
                    <tr id="loading-row">
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-10 h-10 border-4 border-red-200 border-t-[#C41E3A] rounded-full animate-spin mb-3"></div>
                                <p class="text-gray-500 text-sm">Loading users...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Card View -->
        <div id="users-mobile-view" class="md:hidden divide-y divide-gray-100 overflow-auto flex-1">
            <!-- Mobile cards will be rendered here -->
        </div>
        
        <!-- Pagination Controls -->
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="text-sm text-gray-600">
                <span id="pagination-info">Showing 0 - 0 of 0 users</span>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    id="prev-page-btn" 
                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <div id="page-numbers" class="flex items-center gap-1">
                    <!-- Page numbers will be inserted here -->
                </div>
                <button 
                    id="next-page-btn" 
                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
    {{-- END ALL USERS TAB --}}
    
    @elseif(request('tab') == 'password-requests')
    {{-- PASSWORD REQUESTS TAB --}}
    <div class="flex-1 overflow-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if(isset($pendingRequests) && $pendingRequests->count() > 0)
            
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#C41E3A] text-white">
                        <tr>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">User</th>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Username</th>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Requested</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Status</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User" class="w-10 h-10 rounded-full object-cover bg-gray-100 flex-shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate">{{ $request->user->first_name ?? 'N/A' }} {{ $request->user->last_name ?? '' }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $request->user->username ?? $request->username ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-gray-700">{{ $request->username }}</td>
                            <td class="px-4 lg:px-6 py-4 text-gray-600 text-sm whitespace-nowrap">{{ $request->requested_at->format('M d, Y h:i A') }}</td>
                            <td class="px-4 lg:px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-center table-action-cell">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <button 
                                        type="button"
                                        data-request-id="{{ $request->id }}"
                                        onclick="openPasswordResetModal({{ $request->id }}, '{{ $request->username }}', {{ $request->user_id }})"
                                        class="password-request-approve-btn inline-flex items-center justify-center w-9 h-9 p-0 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                        title="Approve"
                                        aria-label="Approve"
                                        data-icon-action="labelled"
                                    >
                                        <i class="fas fa-check text-sm m-0"></i>
                                    </button>
                                    <button 
                                        type="button"
                                        data-request-id="{{ $request->id }}"
                                        onclick="openRejectModal({{ $request->id }}, '{{ $request->username }}')"
                                        class="password-request-reject-btn inline-flex items-center justify-center w-9 h-9 p-0 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                                        title="Reject"
                                        aria-label="Reject"
                                        data-icon-action="labelled"
                                    >
                                        <i class="fas fa-times text-sm m-0"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($pendingRequests as $request)
                <div class="p-4">
                    <div class="flex items-start gap-3 mb-3">
                        <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User" class="w-10 h-10 rounded-full object-cover bg-gray-100 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-800">{{ $request->user->first_name ?? 'N/A' }} {{ $request->user->last_name ?? '' }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $request->user->username ?? $request->username ?? 'N/A' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                            Pending
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        <i class="far fa-clock mr-1"></i> Requested {{ $request->requested_at->format('M d, Y h:i A') }}
                    </p>
                    <div class="flex gap-2">
                        <button 
                            type="button"
                            data-request-id="{{ $request->id }}"
                            onclick="openPasswordResetModal({{ $request->id }}, '{{ $request->username }}', {{ $request->user_id }})"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors"
                        >
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button 
                            type="button"
                            data-request-id="{{ $request->id }}"
                            onclick="openRejectModal({{ $request->id }}, '{{ $request->username }}')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition-colors"
                        >
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            @else
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-key text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-1">No Pending Requests</h3>
                <p class="text-gray-500 text-sm text-center">There are no password reset requests at this time.</p>
            </div>
            @endif
        </div>
    </div>
    {{-- END PASSWORD REQUESTS TAB --}}
    
    @elseif(request('tab') == 'account-settings')
    {{-- ACCOUNT SETTINGS TAB --}}
    <div class="flex-1 overflow-auto flex justify-center">
        <div class="max-w-4xl w-full">
            @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center gap-2 text-green-700">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif
            
            @if(session('status'))
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-center gap-2 text-blue-700">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
            @endif
            
            @if($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center gap-2 text-red-700 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-medium">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-red-600 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- My Account Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-purple-700 flex items-center gap-2 mb-4">
                    <i class="fas fa-user-circle"></i>
                    My Account
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Name Card -->
                    <div class="bg-[#c35817] border-2 border-[#c35817] rounded-xl p-4 text-white">
                        <p class="text-sm text-white mb-1">Name</p>
                        <p class="font-semibold text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    </div>
                    <!-- Email Card -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-4">
                        <p class="text-sm text-green-600 mb-1">Email</p>
                        <p class="font-semibold text-gray-800">{{ Auth::user()->email }}</p>
                    </div>
                    <!-- Position Card -->
                    <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 border-2 border-cyan-200 rounded-xl p-4">
                        <p class="text-sm text-cyan-600 mb-1">Position</p>
                        <p class="font-semibold text-gray-800">{{ Auth::user()->position }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Security Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Security</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Change Email Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <form action="{{ route('admin.account.change-email') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">New Email</label>
                                    <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-sm">
                                </div>
                                
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Current Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-sm pr-10">
                                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="password-toggle-icon"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg hover:from-cyan-600 hover:to-cyan-700 transition-all text-sm font-medium">
                                    <i class="fas fa-envelope"></i>
                                    Update Email
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Password Reset Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-base font-semibold text-gray-800 mb-2">Password Reset</h4>
                        <p class="text-sm text-gray-500 mb-4">Send a reset <span class="text-purple-600">link</span> to your email to change your password.</p>
                        
                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <input type="hidden" name="login" value="{{ Auth::user()->email }}">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all text-sm font-medium">
                                <i class="fas fa-lock"></i>
                                Password Reset Link
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END ACCOUNT SETTINGS TAB --}}
    @endif
</main>

<!-- User Details Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeUserModal()"></div>
    <div class="admin-user-modal-card absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xs sm:max-w-md md:max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-4 py-3 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">User Details</h3>
            <button onclick="closeUserModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="userModalContent" class="p-4">
            <div class="flex justify-center py-8">
                <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="admin-user-modal-card absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xs sm:max-w-md md:max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-3 flex items-center justify-between sticky top-0">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit User
            </h3>
            <button onclick="closeEditModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="editModalContent" class="p-4">
            <div class="flex justify-center py-8">
                <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const adminCaseDepartments = @json($adminCaseDepartments);

document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('users-table-body');
    const mobileView = document.getElementById('users-mobile-view');
    
    // Only run users table code if we're on the All Users tab (tableBody exists)
    if (!tableBody) {
        return; // Exit early if not on All Users tab
    }
    
    const prevPageBtn = document.getElementById('prev-page-btn');
    const nextPageBtn = document.getElementById('next-page-btn');
    const pageNumbersContainer = document.getElementById('page-numbers');
    const paginationInfo = document.getElementById('pagination-info');
    const searchInput = document.getElementById('user-search');
    const statusFilter = document.getElementById('status-filter');
    const clearUserFiltersBtn = document.getElementById('clear-user-filters');
    
    // Pagination and filter variables
    const itemsPerPage = {{ auth()->user()->isDepartmentAdmin() ? 5 : 10 }};
    let currentPage = 1;
    let allUsers = [];
    let filteredUsers = [];
    
    // Pagination handlers
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderUsers();
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderUsers();
            }
        });
    }
    
    // Search functionality
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                applyFilters();
                renderUsers();
            }, 300);
        });
    }
    
    // Status filter
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
            renderUsers();
        });
    }

    clearUserFiltersBtn?.addEventListener('click', function () {
        searchInput.value = '';
        statusFilter.value = '';
        currentPage = 1;
        applyFilters();
        renderUsers();
    });
    
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value;
        
        filteredUsers = allUsers.filter(user => {
            // Search filter
            const matchesSearch = !searchTerm || 
                user.full_name.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm) ||
                (user.username && user.username.toLowerCase().includes(searchTerm)) ||
                user.barangay_name.toLowerCase().includes(searchTerm);
            
            // Status filter
            const effectiveActive = @json(auth()->user()->isBcpcAdmin()) && /bcpc/i.test(user.department || '')
                ? user.bcpc_is_active !== false
                : user.is_active;
            const matchesStatus = statusValue === '' || 
                (statusValue === '1' && effectiveActive) ||
                (statusValue === '0' && !effectiveActive);
            
            return matchesSearch && matchesStatus;
        });
    }
    
    function renderUsers() {
        const totalItems = filteredUsers.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
        const paginatedItems = filteredUsers.slice(startIndex, endIndex);
        
        // Update pagination info
        if (totalItems > 0) {
            paginationInfo.textContent = `Showing ${startIndex + 1} - ${endIndex} of ${totalItems} users`;
        } else {
            paginationInfo.textContent = 'No users found';
        }
        
        // Update pagination buttons
        prevPageBtn.disabled = currentPage <= 1;
        nextPageBtn.disabled = currentPage >= totalPages;
        
        // Render page numbers
        renderPageNumbers(totalPages);
        
        // Render desktop table
        tableBody.innerHTML = '';
        
        if (paginatedItems.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                            <p class="text-sm">No users found</p>
                        </div>
                    </td>
                </tr>
            `;
            mobileView.innerHTML = `
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                    <p class="text-sm">No users found</p>
                </div>
            `;
            return;
        }
        
        // Desktop table rows
        paginatedItems.forEach(user => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors';
            
            const effectiveActive = @json(auth()->user()->isBcpcAdmin()) && /bcpc/i.test(user.department || '')
                ? user.bcpc_is_active !== false
                : user.is_active;
            const statusBadge = effectiveActive
                ? `<span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Active
                   </span>`
                : `<span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Inactive
                   </span>`;
            
            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="/images/mswdo_logo.jpg" alt="User" class="w-10 h-10 rounded-full object-cover bg-gray-100 flex-shrink-0">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-800 truncate">${user.full_name}</p>
                            <p class="text-xs text-gray-500 truncate">${user.username || ''}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600 text-sm truncate">${user.email || 'N/A'}</td>
                <td class="px-6 py-4 text-gray-600 text-sm truncate">${user.barangay_name}</td>
                <td class="px-6 py-4 text-center">${statusBadge}</td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" onclick="viewUserDetails(${user.id})" class="inline-flex items-center justify-center px-3 py-1.5 bg-[#C41E3A] text-white border border-[#C41E3A] hover:bg-[#A01830] rounded-lg text-xs font-medium transition-colors" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" onclick="openEditModal(${user.id})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit User">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${adminCaseDepartments.includes('BCPC') && /bcpc/i.test(user.department || '') ? `<button type="button" onclick="confirmDepartmentStatus(${user.id}, '${user.full_name}', 'BCPC', ${user.bcpc_is_active !== false})" class="p-2 text-white hover:opacity-90 rounded-lg transition-colors ${user.bcpc_is_active !== false ? 'bcpc-disable-control' : 'bcpc-enable-control'}" style="background-color: ${user.bcpc_is_active !== false ? '#08306b' : '#c35817'}" title="${user.bcpc_is_active !== false ? 'Disable BCPC access' : 'Enable BCPC access'}"><i class="fas fa-child"></i></button>` : ''}
                        ${adminCaseDepartments.includes('VAWC') && /vawc/i.test(user.department || '') ? `<button type="button" onclick="confirmDepartmentStatus(${user.id}, '${user.full_name}', 'VAWC', ${user.vawc_is_active !== false})" class="p-2 text-white rounded-lg transition-colors ${user.vawc_is_active !== false ? 'swd-disable-control' : 'swd-enable-control'}" title="${user.vawc_is_active !== false ? 'Disable SWD access' : 'Enable SWD access'}"><i class="fas fa-shield-alt"></i></button>` : ''}
                        <button type="button" onclick="confirmArchive(${user.id}, '${user.full_name}')" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Archive User">
                            <i class="fas fa-archive"></i>
                        </button>
                    </div>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Mobile card view
        mobileView.innerHTML = '';
        paginatedItems.forEach(user => {
            const card = document.createElement('div');
            card.className = 'user-profile-card';
            
            const effectiveActive = @json(auth()->user()->isBcpcAdmin()) && /bcpc/i.test(user.department || '')
                ? user.bcpc_is_active !== false
                : user.is_active;
            const statusBadgeMobile = effectiveActive
                ? `<span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>`
                : `<span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Inactive</span>`;
            
            card.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/mswdo_logo.jpg" alt="User" class="w-12 h-12 rounded-full object-cover bg-gray-100 flex-shrink-0">
                        <div>
                            <p class="font-semibold text-gray-800">${user.full_name}</p>
                            <p class="text-xs text-gray-500">${user.username || 'N/A'}</p>
                        </div>
                    </div>
                    ${statusBadgeMobile}
                </div>
                <div class="text-sm text-gray-600 mb-3">
                    <p><span class="text-gray-400">Barangay:</span> ${user.barangay_name}</p>
                </div>
                <div class="user-profile-card-actions flex flex-wrap gap-2">
                    <button type="button" onclick="viewUserDetails(${user.id})" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-[#C41E3A] text-white border border-[#C41E3A] rounded-lg text-sm font-medium hover:bg-[#A01830] transition-colors">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button type="button" onclick="openEditModal(${user.id})" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    ${adminCaseDepartments.includes('BCPC') && /bcpc/i.test(user.department || '') ? `<button type="button" onclick="confirmDepartmentStatus(${user.id}, '${user.full_name}', 'BCPC', ${user.bcpc_is_active !== false})" class="inline-flex items-center justify-center px-3 py-2 text-white border rounded-lg text-sm font-medium hover:opacity-90 transition-colors ${user.bcpc_is_active !== false ? 'bcpc-disable-control' : 'bcpc-enable-control'}" style="background-color: ${user.bcpc_is_active !== false ? '#08306b' : '#c35817'}; border-color: ${user.bcpc_is_active !== false ? '#08306b' : '#c35817'}" title="${user.bcpc_is_active !== false ? 'Disable BCPC access' : 'Enable BCPC access'}"><i class="fas fa-child"></i></button>` : ''}
                    ${adminCaseDepartments.includes('VAWC') && /vawc/i.test(user.department || '') ? `<button type="button" onclick="confirmDepartmentStatus(${user.id}, '${user.full_name}', 'VAWC', ${user.vawc_is_active !== false})" class="inline-flex items-center justify-center px-3 py-2 text-white border rounded-lg text-sm font-medium transition-colors ${user.vawc_is_active !== false ? 'swd-disable-control' : 'swd-enable-control'}" title="${user.vawc_is_active !== false ? 'Disable SWD access' : 'Enable SWD access'}"><i class="fas fa-shield-alt"></i></button>` : ''}
                    <button type="button" onclick="confirmArchive(${user.id}, '${user.full_name}')" class="archive-user-action px-3 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                        <i class="fas fa-archive"></i>
                    </button>
                </div>
            `;
            
            mobileView.appendChild(card);
        });
    }
    
    function renderPageNumbers(totalPages) {
        pageNumbersContainer.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        if (currentPage <= 3) {
            endPage = Math.min(5, totalPages);
        }
        if (currentPage >= totalPages - 2) {
            startPage = Math.max(1, totalPages - 4);
        }
        
        if (startPage > 1) {
            pageNumbersContainer.appendChild(createPageButton(1));
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-gray-400';
                ellipsis.textContent = '...';
                pageNumbersContainer.appendChild(ellipsis);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            pageNumbersContainer.appendChild(createPageButton(i));
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-gray-400';
                ellipsis.textContent = '...';
                pageNumbersContainer.appendChild(ellipsis);
            }
            pageNumbersContainer.appendChild(createPageButton(totalPages));
        }
    }
    
    function createPageButton(pageNum) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = pageNum === currentPage 
            ? 'inline-flex items-center justify-center w-9 h-9 bg-[#C41E3A] text-white border border-[#C41E3A] rounded-lg text-sm font-medium'
            : 'inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm text-gray-700';
        btn.textContent = pageNum;
        btn.addEventListener('click', function() {
            currentPage = pageNum;
            renderUsers();
        });
        return btn;
    }
    
    // Load initial data
    function loadUsers() {
        fetch('/api/users-list')
            .then(response => response.json())
            .then(data => {
                allUsers = data;
                filteredUsers = [...allUsers];
                applyFilters();
                renderUsers();
            })
            .catch(error => {
                console.error('Error loading users:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-red-500">
                                <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                                <p class="text-sm mb-3">Failed to load users</p>
                                <button onclick="location.reload()" class="px-4 py-2 bg-purple-700 text-white rounded-lg text-sm hover:bg-purple-800 transition-colors">
                                    <i class="fas fa-sync-alt mr-2"></i>Retry
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }
    
    // Initialize
    loadUsers();
});

// Global functions for modals
function viewUserDetails(userId) {
    const modal = document.getElementById('userModal');
    const content = document.getElementById('userModalContent');
    
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex justify-center py-8">
            <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
        </div>
    `;
    
    fetch(`/admin/users/api/${userId}/details`)
        .then(res => res.json())
        .then(response => {
            if (!response.success) throw new Error(response.message);
            const data = response.user;
            const hasBcpcDepartment = /bcpc/i.test(data.department || '');
            const hasVawcDepartment = /vawc/i.test(data.department || '');
            content.innerHTML = `
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <img src="/images/mswdo_logo.jpg" alt="User" class="w-16 h-16 rounded-full object-cover bg-gray-100 flex-shrink-0">
                    <div>
                        <h4 class="text-xl font-bold text-gray-800">${data.first_name} ${data.last_name}</h4>
                        <p class="text-sm text-gray-500">${data.username || 'N/A'}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-gray-800">${data.email || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Position</span>
                        <span class="font-medium text-gray-800">${data.position || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Department</span>
                        <span class="font-medium text-gray-800">${@json(Auth::user()->isVawcAdmin()) ? (data.department === 'VAWC' ? 'SWD' : (data.department === 'BCPC/VAWC' ? 'SWD/BCPC' : (data.department || 'N/A'))) : (data.department === 'BCPC/VAWC' ? 'BCPC/SWD' : (data.department || 'N/A'))}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Barangay</span>
                        <span class="font-medium text-gray-800">${data.barangay || 'Not assigned'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2 py-1 ${data.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} rounded-full text-xs font-semibold">
                            ${data.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last Login</span>
                        <span class="font-medium text-gray-800">${data.last_login_at ? new Date(data.last_login_at).toLocaleDateString() : 'Never'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="font-medium text-gray-800">${data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-100 flex gap-3">
                    <button type="button" onclick="closeUserModal(); openEditModal(${data.id});" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#C41E3A] text-white rounded-lg hover:bg-[#A01830] transition-colors text-sm font-medium">
                        <i class="fas fa-edit"></i> Edit User
                    </button>
                    <button type="button" onclick="closeUserModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                        Close
                    </button>
                </div>
            `;
        })
        .catch(err => {
            content.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                    <p>Failed to load user details</p>
                </div>
            `;
        });
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function confirmAccountStatus(userId, userName, isActive) {
    const action = isActive ? 'Disable' : 'Enable';

    Swal.fire({
        title: `${action} User`,
        text: `${action} ${userName}'s account?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: action,
        confirmButtonColor: department === 'VAWC' ? (isActive ? '#b22222' : '#c35817') : (isActive ? '#d97706' : '#dc2626')
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`/admin/users/${userId}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: !isActive })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);
            Swal.fire({ icon: 'success', title: data.is_active ? 'Account Enabled' : 'Account Inactive', text: data.is_active ? 'The account is now active.' : 'The account is now inactive.', confirmButtonText: 'OK' })
                .then(() => location.reload());
        })
        .catch(error => Swal.fire('Error', error.message || 'Failed to update user status', 'error'));
    });
}

function confirmDepartmentStatus(userId, userName, department, isActive) {
    const action = isActive ? 'Disable' : 'Enable';

    Swal.fire({
        title: `${action} ${department} Access`,
        text: `${action} ${department} access for ${userName}? Other department access will remain unchanged.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: action,
        confirmButtonColor: isActive ? '#d97706' : '#16a34a'
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`/admin/users/${userId}/department-access`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ department, is_active: !isActive })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);
            Swal.fire({ icon: 'success', title: data.department_is_active ? `${department} Access Enabled` : `${department} Access Disabled`, text: data.department_is_active ? 'Account status is Active.' : 'Account status is Inactive.', confirmButtonText: 'OK' })
                .then(() => location.reload());
        })
        .catch(error => Swal.fire('Error', error.message || 'Failed to update department access', 'error'));
    });
}

function confirmArchive(userId, userName) {
    Swal.fire({
        title: 'Archive User',
        html: `
            <div class="text-center py-4">
                <i class="fas fa-archive text-5xl text-amber-500 mb-4"></i>
                <p class="text-gray-600">Are you sure you want to archive <strong>${userName}</strong>?</p>
                <p class="text-gray-400 text-sm mt-2">This user will be moved to archived users.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Archive',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Archived!',
                        text: 'User has been archived successfully.',
                        confirmButtonText: 'OK'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to archive user', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Failed to archive user', 'error'));
        }
    });
}

// Password reset modal functions (if needed)
function openPasswordResetModal(requestId, username, userId) {
    Swal.fire({
        title: 'Reset Password',
        html: `
            <div class="text-left">
                <p class="mb-4">Set a new password for <strong>${username}</strong>:</p>
                <div class="mb-3">
                    <div class="relative">
                        <input type="password" id="swal-new-password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg" placeholder="New Password">
                        <button type="button" id="swal-toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="swal-password-requirements" class="mt-2 p-2 bg-gray-50 rounded text-xs space-y-1 hidden">
                        <p class="font-medium text-gray-600 mb-1">Password must contain:</p>
                        <div id="swal-req-length" class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-circle text-[8px]"></i>
                            <span>At least 8 characters</span>
                        </div>
                        <div id="swal-req-upper" class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-circle text-[8px]"></i>
                            <span>One uppercase letter</span>
                        </div>
                        <div id="swal-req-lower" class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-circle text-[8px]"></i>
                            <span>One lowercase letter</span>
                        </div>
                        <div id="swal-req-number" class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-circle text-[8px]"></i>
                            <span>One number</span>
                        </div>
                        <div id="swal-req-special" class="flex items-center gap-2 text-gray-500">
                            <i class="fas fa-circle text-[8px]"></i>
                            <span>One special character (!@#$%^&*)</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <input type="password" id="swal-confirm-password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg" placeholder="Confirm Password">
                        <button type="button" id="swal-toggle-confirm-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="swal-password-match" class="mt-2 p-2 rounded text-xs hidden">
                        <span id="swal-match-text" class="flex items-center gap-2"></span>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Reset Password',
        confirmButtonColor: '#10b981',
        didOpen: () => {
            const passwordInput = document.getElementById('swal-new-password');
            const confirmPasswordInput = document.getElementById('swal-confirm-password');
            const passwordRequirements = document.getElementById('swal-password-requirements');
            const passwordMatch = document.getElementById('swal-password-match');
            const matchText = document.getElementById('swal-match-text');
            const togglePassword = document.getElementById('swal-toggle-password');
            const toggleConfirmPassword = document.getElementById('swal-toggle-confirm-password');

            const requirements = {
                length: { el: document.getElementById('swal-req-length'), regex: /.{8,}/ },
                upper: { el: document.getElementById('swal-req-upper'), regex: /[A-Z]/ },
                lower: { el: document.getElementById('swal-req-lower'), regex: /[a-z]/ },
                number: { el: document.getElementById('swal-req-number'), regex: /[0-9]/ },
                special: { el: document.getElementById('swal-req-special'), regex: /[!@#$%^&*]/ }
            };

            function checkPasswordRequirements() {
                const password = passwordInput.value;
                let allValid = true;

                if (password) {
                    passwordRequirements.classList.remove('hidden');
                }

                for (const [key, req] of Object.entries(requirements)) {
                    const isValid = req.regex.test(password);
                    const icon = req.el.querySelector('i');

                    if (isValid) {
                        req.el.classList.remove('text-gray-500');
                        req.el.classList.add('text-green-600');
                        icon.classList.remove('fa-circle');
                        icon.classList.add('fa-check-circle');
                    } else {
                        req.el.classList.add('text-gray-500');
                        req.el.classList.remove('text-green-600');
                        icon.classList.add('fa-circle');
                        icon.classList.remove('fa-check-circle');
                        allValid = false;
                    }
                }

                return allValid;
            }

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (!confirmPassword) {
                    passwordMatch.classList.add('hidden');
                    return false;
                }

                passwordMatch.classList.remove('hidden');

                if (password === confirmPassword && password) {
                    passwordMatch.classList.remove('bg-red-50');
                    passwordMatch.classList.add('bg-green-50');
                    matchText.innerHTML = '<i class="fas fa-check-circle text-green-600"></i><span class="text-green-600">Passwords match</span>';
                    return true;
                } else {
                    passwordMatch.classList.add('bg-red-50');
                    passwordMatch.classList.remove('bg-green-50');
                    matchText.innerHTML = '<i class="fas fa-times-circle text-red-600"></i><span class="text-red-600">Passwords do not match</span>';
                    return false;
                }
            }

            [togglePassword, toggleConfirmPassword].forEach((btn, index) => {
                btn.addEventListener('click', function() {
                    const input = index === 0 ? passwordInput : confirmPasswordInput;
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            });

            passwordInput.addEventListener('input', () => {
                checkPasswordRequirements();
                checkPasswordMatch();
            });
            passwordInput.addEventListener('focus', () => passwordRequirements.classList.remove('hidden'));
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        },
        preConfirm: () => {
            const password = document.getElementById('swal-new-password').value;
            const confirm = document.getElementById('swal-confirm-password').value;
            if (!password || password.length < 8) {
                Swal.showValidationMessage('Password must be at least 8 characters');
                return false;
            }
            const requirements = {
                length: /.{8,}/,
                upper: /[A-Z]/,
                lower: /[a-z]/,
                number: /[0-9]/,
                special: /[!@#$%^&*]/
            };
            for (const [key, regex] of Object.entries(requirements)) {
                if (!regex.test(password)) {
                    Swal.showValidationMessage('Password does not meet all requirements');
                    return false;
                }
            }
            if (password !== confirm) {
                Swal.showValidationMessage('Passwords do not match');
                return false;
            }
            return { password, requestId, userId };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Resetting Password...',
                text: 'Please wait while we reset the password',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Handle password reset API call
            fetch(`/admin/password-reset-requests/${result.value.requestId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    new_password: result.value.password,
                    new_password_confirmation: result.value.password,
                    user_id: result.value.userId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', 'Password has been reset', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to reset password', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Failed to reset password', 'error');
            });
        }
    });
}

function openRejectModal(requestId, username) {
    Swal.fire({
        title: 'Reject Password Request',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-700 mb-3">Please state the reason for rejecting the password reset request for <strong>${username}</strong>:</p>
                <textarea id="swal-reject-reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#C41E3A] focus:border-[#C41E3A]" rows="4" placeholder="Enter reason for rejection..."></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Submit & Reject',
        confirmButtonColor: '#dc2626',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#6b7280',
        preConfirm: () => {
            const reason = document.getElementById('swal-reject-reason')?.value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please provide a reason for rejecting this request.');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Rejecting Request...',
                text: 'Sending notification email to requester...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/password-reset-requests/${requestId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: result.value, admin_notes: result.value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Request Rejected',
                        text: 'The password reset request has been rejected and an email has been sent to the requester.',
                        confirmButtonColor: '#C41E3A'
                    }).then(() => {
                        const alert = document.getElementById('password-reset-alert');
                        if (alert) alert.remove();
                        document.querySelectorAll(`[data-request-id="${requestId}"]`).forEach((button) => {
                            const row = button.closest('tr');
                            if (row) row.remove();
                        });
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to reject request', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Failed to reject request', 'error');
            });
        }
    });
}

// Close modal on ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeUserModal();
        closeEditModal();
    }
});

// Edit Modal Functions
let editingUserId = null;

function openEditModal(userId) {
    editingUserId = userId;
    const modal = document.getElementById('editModal');
    const content = document.getElementById('editModalContent');
    
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex justify-center py-8">
            <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
        </div>
    `;
    
    fetch(`/admin/users/api/${userId}/details`)
        .then(res => res.json())
        .then(response => {
            if (!response.success) throw new Error(response.message);
            const data = response.user;
            content.innerHTML = `
                <form id="editUserForm" class="space-y-4">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" value="${data.first_name || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" value="${data.last_name || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="${data.username || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" autocomplete="username">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="${data.email || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm" autocomplete="email">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="text" name="position" value="${data.position || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                @if(Auth::user()->isVawcAdmin())
                                ${data.department && !['VAWC', 'SWD', 'SWD/VAWC', 'BCPC/VAWC', 'VAWC/BCPC'].includes(data.department) ? '<option value="' + data.department + '" selected>' + data.department + '</option>' : ''}
                                <option value="VAWC" ${['VAWC', 'SWD', 'SWD/VAWC'].includes(data.department) ? 'selected' : ''}>SWD</option>
                                <option value="BCPC/VAWC" ${['BCPC/VAWC', 'VAWC/BCPC'].includes(data.department) ? 'selected' : ''}>SWD/BCPC</option>
                                @else
                                ${data.department && !['BCPC', 'BCPC/VAWC'].includes(data.department) ? '<option value="' + data.department + '" selected>' + data.department + '</option>' : ''}
                                <option value="BCPC" ${data.department === 'BCPC' ? 'selected' : ''}>BCPC</option>
                                <option value="BCPC/VAWC" ${data.department === 'BCPC/VAWC' ? 'selected' : ''}>BCPC/SWD</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Barangay Assignment</label>
                        <select id="edit_barangay" name="barangay" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" id="edit_is_active" ${data.is_active ? 'checked' : ''} class="w-4 h-4 border-gray-300 rounded cursor-pointer" style="accent-color: #2563eb;">
                            Active
                        </label>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-500">
                            <input type="checkbox" id="edit_is_inactive" ${data.is_active ? '' : 'checked'} disabled class="w-4 h-4 border-gray-300 rounded cursor-not-allowed opacity-100" style="accent-color: #2563eb;">
                            Inactive
                        </label>
                    </div>
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500 mb-2">Change Password (leave blank to keep current)</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <div class="relative">
                                    <input type="password" id="edit_new_password" name="password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                    <button type="button" id="toggle_edit_new_password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600" aria-label="Show new password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="edit-password-requirements" class="mt-2 text-xs space-y-1">
                                    <p class="font-medium text-gray-600">Password must contain:</p>
                                    <div id="edit-req-length" class="flex items-center gap-2 text-gray-500"><i class="fas fa-circle text-[8px]"></i><span>At least 8 characters</span></div>
                                    <div id="edit-req-upper" class="flex items-center gap-2 text-gray-500"><i class="fas fa-circle text-[8px]"></i><span>One uppercase letter</span></div>
                                    <div id="edit-req-lower" class="flex items-center gap-2 text-gray-500"><i class="fas fa-circle text-[8px]"></i><span>One lowercase letter</span></div>
                                    <div id="edit-req-number" class="flex items-center gap-2 text-gray-500"><i class="fas fa-circle text-[8px]"></i><span>One number</span></div>
                                    <div id="edit-req-special" class="flex items-center gap-2 text-gray-500"><i class="fas fa-circle text-[8px]"></i><span>One special character (!@#$%^&amp;*)</span></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" id="edit_confirm_password" name="password_confirmation" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                    <button type="button" id="toggle_edit_confirm_password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600" aria-label="Show confirm password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="edit-password-match" class="mt-2 text-xs hidden"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            `;

            // Populate barangay dropdown
            fetch('/api/barangays')
                .then(res => res.json())
                .then(barangays => {
                    const dropdown = document.getElementById('edit_barangay');
                    dropdown.innerHTML = '<option value="">Select Barangay</option>';
                    barangays.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.id;
                        option.textContent = `${barangay.name} [${barangay.code}]`;
                        // Match by name (user.barangay is name, barangay.name is name)
                        if (data.barangay && barangay.name === data.barangay) {
                            option.selected = true;
                        }
                        dropdown.appendChild(option);
                    });
                });
            
            // Attach form submit handler
            document.getElementById('editUserForm').addEventListener('submit', function(e) {
                e.preventDefault();
                if (passwordInput.value && (!validateEditPassword() || !validateEditPasswordMatch())) {
                    Swal.fire('Password requirements not met', 'Use a password that meets every listed requirement and matches the confirmation.', 'error');
                    return;
                }
                saveUserChanges();
            });

            const activeCheckbox = document.getElementById('edit_is_active');
            const inactiveCheckbox = document.getElementById('edit_is_inactive');
            activeCheckbox?.addEventListener('change', () => {
                if (inactiveCheckbox) inactiveCheckbox.checked = !activeCheckbox.checked;
            });

            const passwordInput = document.getElementById('edit_new_password');
            const confirmPasswordInput = document.getElementById('edit_confirm_password');
            const passwordMatch = document.getElementById('edit-password-match');
            const passwordRequirements = {
                length: { el: document.getElementById('edit-req-length'), regex: /.{8,}/ },
                upper: { el: document.getElementById('edit-req-upper'), regex: /[A-Z]/ },
                lower: { el: document.getElementById('edit-req-lower'), regex: /[a-z]/ },
                number: { el: document.getElementById('edit-req-number'), regex: /[0-9]/ },
                special: { el: document.getElementById('edit-req-special'), regex: /[!@#$%^&*]/ }
            };

            function validateEditPassword() {
                let valid = true;
                Object.values(passwordRequirements).forEach(requirement => {
                    const meetsRequirement = requirement.regex.test(passwordInput.value);
                    const icon = requirement.el.querySelector('i');
                    requirement.el.classList.toggle('text-green-600', meetsRequirement);
                    requirement.el.classList.toggle('text-gray-500', !meetsRequirement);
                    icon.classList.toggle('fa-check-circle', meetsRequirement);
                    icon.classList.toggle('fa-circle', !meetsRequirement);
                    valid = valid && meetsRequirement;
                });
                return valid;
            }

            function validateEditPasswordMatch() {
                const confirmation = confirmPasswordInput.value;
                passwordMatch.classList.toggle('hidden', !confirmation);
                if (!confirmation) return true;

                const matches = passwordInput.value === confirmation;
                passwordMatch.classList.toggle('text-green-600', matches);
                passwordMatch.classList.toggle('text-red-600', !matches);
                passwordMatch.innerHTML = matches
                    ? '<i class="fas fa-check-circle mr-1"></i>Passwords match'
                    : '<i class="fas fa-times-circle mr-1"></i>Passwords do not match';
                return matches;
            }

            function toggleEditPassword(input, button) {
                const showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                button.querySelector('i').classList.toggle('fa-eye', showing);
                button.querySelector('i').classList.toggle('fa-eye-slash', !showing);
                button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            }

            passwordInput.addEventListener('input', () => {
                validateEditPassword();
                validateEditPasswordMatch();
            });
            confirmPasswordInput.addEventListener('input', validateEditPasswordMatch);
            document.getElementById('toggle_edit_new_password').addEventListener('click', () => toggleEditPassword(passwordInput, document.getElementById('toggle_edit_new_password')));
            document.getElementById('toggle_edit_confirm_password').addEventListener('click', () => toggleEditPassword(confirmPasswordInput, document.getElementById('toggle_edit_confirm_password')));


        })
        .catch(err => {
            content.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                    <p>Failed to load user details</p>
                </div>
            `;
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    editingUserId = null;
}

function saveUserChanges() {
    if (!editingUserId) return;
    
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    
    // Convert FormData to JSON, handling checkbox
    const data = {};
    formData.forEach((value, key) => {
        if (key !== '_token' && key !== '_method') {
            data[key] = value;
        }
    });
    data.is_active = form.querySelector('[name="is_active"]').checked;
    ['bcpc_is_active', 'vawc_is_active'].forEach((name) => {
        const checkbox = form.querySelector(`[name="${name}"]`);
        if (checkbox) data[name] = checkbox.checked;
    });
    // Add barangay assignment
    data.barangay = form.querySelector('[name="barangay"]').value;
    
    fetch(`/admin/users/api/${editingUserId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'User has been updated successfully.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', response.message || 'Failed to update user', 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Failed to update user', 'error');
    });
}

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-toggle-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush

@endsection
