@extends('layouts.admin')

@section('title', 'Edit Social Case Study')
@section('navbar-title', 'Edit Social Case Study')

@section('page-styles')
<style>
    .table-responsive { border-radius: .75rem; }
</style>
@endsection

@section('content')
<div class="card">
                <div class="card-body">
                <form method="POST" action="{{ route('admin.social-case-studies.update', $socialCaseStudy) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Case Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open" {{ $socialCaseStudy->status === 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ $socialCaseStudy->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Closed" {{ $socialCaseStudy->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interview Date</label>
                    <input type="date" name="interview_date" value="{{ $socialCaseStudy->interview_date?->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <textarea name="summary" rows="6" class="form-control">{{ $socialCaseStudy->summary }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Case Study</button>
            </form>
                </div>
</div>
@endsection
