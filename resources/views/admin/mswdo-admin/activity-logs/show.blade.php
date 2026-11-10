@extends('admin.mswdo-admin.layout')
@section('title', 'Activity Details - MSWDO')
@section('page_title', 'Activity Details')
@section('content')
@include('admin.mswdo-admin.activity-logs.styles')
<div class="activity-header"><h1>Activity #{{ $activityLog->id }}</h1><p>{{ $activityLog->description }}</p></div>
<div class="activity-card">
    @include('admin.mswdo-admin.activity-logs.details')
    <div class="activity-actions"><a class="activity-button" href="{{ route('admin.activity-logs.index') }}">Back to user logs</a></div>
</div>
@endsection
