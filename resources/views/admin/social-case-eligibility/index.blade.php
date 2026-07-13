@extends('layouts.admin')

@section('title', 'Eligibility Validation')

@section('navbar-title', 'Eligibility Validation')

@section('page-styles')
    <style>
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .metric-card .label {
            font-size: 0.85rem;
            opacity: 0.9;
            margin: 0;
        }
        .metric-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0.5rem 0 0;
        }
        .search-panel {
            border-left: 4px solid var(--primary);
        }
        .search-panel h6 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .result-card {
            border-left: 4px solid var(--success);
        }
        .result-card.not-eligible {
            border-left-color: var(--danger);
        }
    </style>
@endsection

@section('content')
            <div class="row g-4 mb-4">
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Today's Eligibility Checks</p>
                        <p class="value">{{ $metrics['checksToday'] }}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Eligible Clients Today</p>
                        <p class="value">{{ $metrics['eligibleToday'] }}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Not Eligible Today</p>
                        <p class="value">{{ $metrics['notEligibleToday'] }}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Average Search Time</p>
                        <p class="value">{{ $metrics['averageSearchTimeMs'] }} ms</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-5">
                    <div class="card search-panel">
                        <h6>Find Client</h6>
                        <form id="searchForm" class="row g-3" onsubmit="return false;">
                            <div class="col-12">
                                <label class="form-label">Control Number</label>
                                <input type="text" id="controlNumber" class="form-control" placeholder="Enter control number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" id="firstName" class="form-control" placeholder="Juan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="lastName" class="form-control" placeholder="Dela Cruz">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Contact Number</label>
                                <input type="text" id="contactNumber" class="form-control" placeholder="09123456789">
                            </div>
                            <div class="col-12 d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="searchClients()">Search</button>
                            </div>
                            <div class="col-12">
                                <div id="searchResultMessage" class="text-muted"></div>
                            </div>
                        </form>

                        <div id="searchResults" class="mt-4"></div>
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="card results-panel" id="eligibilityPanel">
                        <h6>Eligibility Result</h6>
                        <div id="eligibilityContent" class="mt-3">
                            <p class="text-muted">Search for a client to display eligibility validation results.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card p-4">
                        <h6 class="mb-4">Recent Eligibility Checks</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Officer</th>
                                        <th>Client</th>
                                        <th>Result</th>
                                        <th>Duration</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($metrics['recentChecks'] as $log)
                                    <tr>
                                        <td>{{ $log->officer_name }}</td>
                                        <td>{{ $log->client_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->result === 'eligible' ? 'success' : 'danger' }}">{{ strtoupper(str_replace('_', ' ', $log->result)) }}</span>
                                        </td>
                                        <td>{{ $log->search_duration_ms }} ms</td>
                                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('page-scripts')
    <script>
        async function searchClients() {
            const controlNumber = document.getElementById('controlNumber').value.trim();
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const contactNumber = document.getElementById('contactNumber').value.trim();
            const resultArea = document.getElementById('searchResults');
            const messageArea = document.getElementById('searchResultMessage');

            resultArea.innerHTML = '';
            messageArea.textContent = '';

            const response = await fetch('/admin/social-case-eligibility/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ control_number: controlNumber, first_name: firstName, last_name: lastName, contact_number: contactNumber }),
            });

            const payload = await response.json();
            const clients = payload.clients || [];

            if (!clients.length) {
                messageArea.textContent = 'Client not found. You can register a new client to continue eligibility validation.';
                resultArea.innerHTML = `
                    <div class="alert alert-warning">No matching client found.</div>
                    <a href="/admin/social-case-eligibility/register" class="btn btn-outline-primary">Register New Client</a>
                `;
                return;
            }

            const rows = clients.map(client => {
                return `
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">${client.first_name} ${client.middle_name || ''} ${client.last_name}</div>
                                <div class="text-muted small">ID ${client.id} • ${client.contact_number || 'No contact'}</div>
                            </div>
                            <button onclick="checkEligibility(${client.id})" class="btn btn-sm btn-primary">Check Eligibility</button>
                        </div>
                    </div>
                `;
            }).join('');

            resultArea.innerHTML = rows;
        }

        async function checkEligibility(clientId) {
            const response = await fetch(`/admin/social-case-eligibility/${clientId}/check`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            });

            const data = await response.json();

            if (data.eligible) {
                Swal.fire({
                    title: 'ELIGIBLE',
                    text: 'This client is eligible for assistance.',
                    icon: 'success',
                    confirmButtonColor: '#22C55E',
                    confirmButtonText: 'Proceed',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/admin/social-case-studies/create/${clientId}`;
                    }
                });
            } else {
                Swal.fire({
                    title: 'NOT ELIGIBLE',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>Reason:</strong> ${data.reason}</p>
                            <p><strong>Date of last assistance:</strong> ${data.assistance_date}</p>
                            <p><strong>Type of assistance:</strong> ${data.assistance_type}</p>
                            <p><strong>Next eligible date:</strong> ${data.next_eligible_date}</p>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonColor: '#D32F2F',
                    confirmButtonText: 'Close',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
            }
        }
    </script>
@endsection
