@extends('layouts.admin')

@section('title', 'Social Case Studies')
@section('navbar-title', 'Social Case Studies')

@section('page-styles')
<style>
    .table-responsive { border-radius: .75rem; }
</style>
@endsection

@section('content')
<div class="card">
                <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Case Number</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Workflow Step</th>
                            <th>Interview Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studies as $study)
                            <tr>
                                <td>{{ $study->id }}</td>
                                <td>{{ $study->case_number }}</td>
                                <td>{{ $study->client->full_name }}</td>
                                <td>{{ $study->status }}</td>
                                <td>{{ str_replace('_', ' ', $study->workflow_step) }}</td>
                                <td>{{ optional($study->interview_date)->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $study->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.social-case-studies.step.show', [$study, $study->workflow_step]) }}" class="btn btn-sm btn-outline-primary">Continue Case</a>
                                    <form method="POST" action="{{ route('admin.social-case-studies.destroy', $study) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $studies->links() }}</div>
                </div>
</div>
@endsection
