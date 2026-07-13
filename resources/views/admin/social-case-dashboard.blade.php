@extends('layouts.admin')

@section('title', 'Social Case Study Dashboard')

@section('navbar-title', 'Social Case Study Dashboard')

@section('content')
            <!-- Search and Quick Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div class="search-container flex-grow-1">
                                    <i class="fas fa-search text-muted"></i>
                                    <input type="text" placeholder="Search by name, control no., barangay, phone number..." id="globalSearch">
                                </div>
                                <div class="search-filter-dropdown">
                                    <button class="search-filter-btn" onclick="toggleSearchFilter()">
                                        <i class="fas fa-filter me-1"></i>Filter
                                    </button>
                                    <div class="search-filter-menu" id="searchFilterMenu">
                                        <div class="search-filter-item active" onclick="selectFilter(this, 'all')">
                                            <i class="fas fa-list"></i> All
                                        </div>
                                        <div class="search-filter-item" onclick="selectFilter(this, 'name')">
                                            <i class="fas fa-user"></i> Name
                                        </div>
                                        <div class="search-filter-item" onclick="selectFilter(this, 'control_no')">
                                            <i class="fas fa-hashtag"></i> Control No.
                                        </div>
                                        <div class="search-filter-item" onclick="selectFilter(this, 'barangay')">
                                            <i class="fas fa-map-marker-alt"></i> Barangay
                                        </div>
                                        <div class="search-filter-item" onclick="selectFilter(this, 'phone')">
                                            <i class="fas fa-phone"></i> Phone Number
                                        </div>
                                        <div class="search-filter-item" onclick="selectFilter(this, 'case_no')">
                                            <i class="fas fa-folder"></i> Case Number
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.social-case-eligibility.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Start New Case
                                </a>
                                <a href="/admin/social-case-studies" class="btn btn-outline-secondary">
                                    <i class="fas fa-clock me-2"></i>Pending
                                </a>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-chart-bar me-2"></i>Reports
                                </a>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-archive me-2"></i>Released
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Progress Card -->
            @if(isset($currentCase))
            <div class="case-progress-card">
                <div class="case-progress-header">
                    <div>
                        <div class="case-progress-title">Current Case</div>
                        <div class="case-progress-client">{{ $currentCase->client->full_name ?? 'Juan Dela Cruz' }}</div>
                    </div>
                    <a href="{{ route('admin.social-case-studies.show', $currentCase->id) }}" class="btn btn-light btn-sm">
                        View Details <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="case-progress-steps">
                    <div class="case-progress-step completed">
                        <div class="case-progress-step-icon"><i class="fas fa-check"></i></div>
                        <div class="case-progress-step-label">Requirements</div>
                    </div>
                    <div class="case-progress-step completed">
                        <div class="case-progress-step-icon"><i class="fas fa-check"></i></div>
                        <div class="case-progress-step-label">Interview</div>
                    </div>
                    <div class="case-progress-step completed">
                        <div class="case-progress-step-icon"><i class="fas fa-check"></i></div>
                        <div class="case-progress-step-label">Assessment</div>
                    </div>
                    <div class="case-progress-step current">
                        <div class="case-progress-step-icon"><i class="fas fa-clock"></i></div>
                        <div class="case-progress-step-label">Approval</div>
                    </div>
                    <div class="case-progress-step">
                        <div class="case-progress-step-icon"><i class="fas fa-paper-plane"></i></div>
                        <div class="case-progress-step-label">Release</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Today's Work Metrics -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-label">New Intakes Today</div>
                            <div class="stat-value">{{ $newIntakesToday ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-label">Pending Interviews</div>
                            <div class="stat-value">{{ $pendingInterviews ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-label">Pending Assessments</div>
                            <div class="stat-value">{{ $pendingAssessments ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-label">Ready for Approval</div>
                            <div class="stat-value">{{ $readyForApproval ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Case Workflow Pipeline -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-project-diagram me-2"></i>Case Workflow
                        </div>
                        <div class="card-body">
                            <div class="workflow-step">
                                <div class="workflow-step-icon" style="background-color: #DBEAFE; color: #1E40AF;">1</div>
                                <div class="workflow-step-content">
                                    <div class="workflow-step-title">New Intake</div>
                                    <div class="workflow-step-count">{{ $newIntakeCount ?? 12 }} cases</div>
                                </div>
                            </div>
                            <div class="workflow-step">
                                <div class="workflow-step-icon" style="background-color: #FEF3C7; color: #92400E;">2</div>
                                <div class="workflow-step-content">
                                    <div class="workflow-step-title">Requirements Review</div>
                                    <div class="workflow-step-count">{{ $requirementsReviewCount ?? 5 }} cases</div>
                                </div>
                            </div>
                            <div class="workflow-step">
                                <div class="workflow-step-icon" style="background-color: #EDE9FE; color: #6D28D9;">3</div>
                                <div class="workflow-step-content">
                                    <div class="workflow-step-title">Interview</div>
                                    <div class="workflow-step-count">{{ $interviewCount ?? 3 }} cases</div>
                                </div>
                            </div>
                            <div class="workflow-step">
                                <div class="workflow-step-icon" style="background-color: #D1FAE5; color: #065F46;">4</div>
                                <div class="workflow-step-content">
                                    <div class="workflow-step-title">Assessment</div>
                                    <div class="workflow-step-count">{{ $assessmentCount ?? 6 }} cases</div>
                                </div>
                            </div>
                            <div class="workflow-step">
                                <div class="workflow-step-icon" style="background-color: #FEE2E2; color: #991B1B;">5</div>
                                <div class="workflow-step-content">
                                    <div class="workflow-step-title">Approval</div>
                                    <div class="workflow-step-count">{{ $approvalCount ?? 2 }} cases</div>
                                </div>
                            </div>
                            <div class="workflow-step">
                                <div class="workflow-step-icon" style="background-color: #E5E7EB; color: #4B5563;">6</div>
                                <div class="workflow-step-content">
                                    <div class="workflow-step-title">Released</div>
                                    <div class="workflow-step-count">{{ $releasedCount ?? 14 }} cases</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-history me-2"></i>Recent Activity
                        </div>
                        <div class="card-body">
                            @forelse($recentActivity ?? [] as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon" style="background-color: {{ $activity['color'] ?? '#E5E7EB' }}; color: {{ $activity['text_color'] ?? '#4B5563' }};">
                                        <i class="fas fa-{{ $activity['icon'] ?? 'circle' }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">{{ $activity['text'] ?? 'Activity' }}</div>
                                        <div class="activity-time">{{ $activity['time'] ?? 'Just now' }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-history"></i></div>
                                    <div>No recent activity</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-calendar-alt me-2"></i>Today's Schedule
                        </div>
                        <div class="card-body">
                            @forelse($todaySchedule ?? [] as $schedule)
                                <div class="case-row">
                                    <div class="case-client-name">{{ $schedule['time'] ?? '9:00 AM' }}</div>
                                    <div class="case-details">{{ $schedule['title'] ?? 'Interview' }}</div>
                                    <div class="case-meta">
                                        <span class="badge badge-interview">{{ $schedule['type'] ?? 'Interview' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-calendar-alt"></i></div>
                                    <div>No scheduled activities today</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Cases with More Details -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-folder-open me-2"></i>Latest Cases
                </div>
                <div class="card-body">
                    @forelse($latestEncodedCases as $case)
                        <div class="case-row">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="case-client-name">{{ $case->client->full_name }}</div>
                                    <div class="case-details">{{ $case->assistance_type ?? 'Medical Assistance' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="case-details">Barangay: {{ $case->client->barangay ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="case-details">Assigned: {{ $case->assigned_to ?? 'Maria Santos' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="case-meta">
                                        <span class="badge {{ $case->priority == 'Urgent' ? 'badge-urgent' : ($case->status == 'Interview' ? 'badge-interview' : 'badge-pending-docs') }}">
                                            {{ $case->priority ?? 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="case-details">{{ $case->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                            <div>No cases found</div>
                            <small>Cases will appear here after intake</small>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row g-4 mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-2"></i>Cases per Month
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                @foreach($monthlyCases ?? [
                                    ['month' => 'Jan', 'count' => 45],
                                    ['month' => 'Feb', 'count' => 52],
                                    ['month' => 'Mar', 'count' => 38],
                                    ['month' => 'Apr', 'count' => 65],
                                    ['month' => 'May', 'count' => 48],
                                    ['month' => 'Jun', 'count' => 72]
                                ] as $month)
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; font-weight: 600; color: var(--text-muted);">{{ $month['month'] }}</div>
                                        <div class="progress-bar-custom flex-grow-1">
                                            <div class="progress-bar-fill" style="width: {{ ($month['count'] / 80) * 100 }}%; background-color: var(--primary);"></div>
                                        </div>
                                        <div style="width: 40px; font-weight: 600; color: var(--text);">{{ $month['count'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i>Assistance Types
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                @foreach($assistanceTypes ?? [
                                    ['type' => 'Medical', 'count' => 125, 'color' => '#3B82F6'],
                                    ['type' => 'Burial', 'count' => 45, 'color' => '#6B7280'],
                                    ['type' => 'Educational', 'count' => 78, 'color' => '#10B981'],
                                    ['type' => 'Financial', 'count' => 92, 'color' => '#F59E0B']
                                ] as $assistance)
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 120px; font-weight: 600; color: var(--text);">{{ $assistance['type'] }}</div>
                                        <div class="progress-bar-custom flex-grow-1">
                                            <div class="progress-bar-fill" style="width: {{ ($assistance['count'] / 150) * 100 }}%; background-color: {{ $assistance['color'] }};"></div>
                                        </div>
                                        <div style="width: 40px; font-weight: 600; color: var(--text);">{{ $assistance['count'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('page-scripts')
    <script>
        function toggleSearchFilter() {
            document.getElementById('searchFilterMenu').classList.toggle('show');
        }

        function selectFilter(element, filter) {
            // Remove active class from all items
            document.querySelectorAll('.search-filter-item').forEach(item => {
                item.classList.remove('active');
            });
            // Add active class to clicked item
            element.classList.add('active');
            // Close menu
            document.getElementById('searchFilterMenu').classList.remove('show');
            // Update search placeholder based on filter
            const searchInput = document.getElementById('globalSearch');
            const placeholders = {
                'all': 'Search by name, control no., barangay, phone number...',
                'name': 'Search by client name...',
                'control_no': 'Search by control number...',
                'barangay': 'Search by barangay...',
                'phone': 'Search by phone number...',
                'case_no': 'Search by case number...'
            };
            searchInput.placeholder = placeholders[filter];
        }

        // Close search filter menu when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.search-filter-dropdown');
            const menu = document.getElementById('searchFilterMenu');
            if (!dropdown.contains(event.target)) {
                menu.classList.remove('show');
            }
        });
    </script>
@endsection
