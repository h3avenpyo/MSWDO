@extends('layouts.desk-officer')

@section('title', 'Referral Details')

@section('content')
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
