@extends('layouts.admin')

@section('title', 'Beneficiary Intake List')

@section('navbar-title', 'Beneficiary Intake List')

@section('content')
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0">All Beneficiary Intakes</h6>
                        <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>New Intake
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Control Number</th>
                                    <th>Client Name</th>
                                    <th>Date Processed</th>
                                    <th>Service</th>
                                    <th>Purpose</th>
                                    <th>Submitted To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($intakes as $intake)
                                <tr>
                                    <td>{{ $intake->control_number }}</td>
                                    <td>{{ $intake->client_full_name }}</td>
                                    <td>{{ $intake->date_processed->format('M d, Y') }}</td>
                                    <td>{{ $intake->service_provided }}</td>
                                    <td>{{ $intake->purpose }}</td>
                                    <td>{{ $intake->submitted_to }}</td>
                                    <td>
                                        <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No beneficiary intakes found.</p>
                                        <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-primary btn-sm mt-2">
                                            Create New Intake
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($intakes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $intakes->links() }}
                    </div>
                    @endif
                </div>
            </div>
@endsection

