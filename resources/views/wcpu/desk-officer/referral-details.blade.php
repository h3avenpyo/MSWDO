@extends('layouts.desk-officer')

@section('title', 'Referral Details')

@section('content')
<header id="desk-officer-dashboard-header" class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="flex items-center gap-3 min-w-0">
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Referral Details</h2>
                <p class="text-sm text-gray-500">View case referral information</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('desk-officer.components.notifications')
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
<div class="mx-auto w-full max-w-3xl p-4 sm:p-6 lg:p-8">
    <div class="mb-4">
        <a href="{{ route('desk-officer.referrals.archived') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left"></i> Back to Archived Referrals
        </a>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @include('desk-officer.partials.referral-details')
    </div>
</div>
@endsection
