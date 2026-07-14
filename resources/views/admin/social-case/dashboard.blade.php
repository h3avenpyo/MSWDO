@extends('admin.social-case.layout')
@section('title', 'Dashboard - Social Case Study')

@section('content')
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="page-head">
        <div><h1>Dashboard</h1><p>Overview of all social case study requests.</p></div>
        <button class="btn primary" onclick="window.location.href='/admin/social-case/new'"><i data-lucide="plus" style="width:16px;height:16px"></i> New case</button>
    </div>
    
    <!-- Workflow Progress Cards -->
    <div class="workflow-cards">
        <div class="workflow-card draft">
            <div class="num" id="draftCases">0</div>
            <div class="label">New Cases</div>
            <div class="trend neutral" id="draftTrend">— this week</div>
            <div class="arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </div>
        <div class="workflow-card review">
            <div class="num" id="reviewCases">0</div>
            <div class="label">For Interview</div>
            <div class="trend neutral" id="reviewTrend">— this week</div>
            <div class="arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </div>
        <div class="workflow-card approved">
            <div class="num" id="approvedCases">0</div>
            <div class="label">Approved</div>
            <div class="trend up" id="approvedTrend">↑ 0% this month</div>
            <div class="arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </div>
        <div class="workflow-card printed">
            <div class="num" id="printedCases">0</div>
            <div class="label">For Printing</div>
            <div class="trend neutral" id="printedTrend">— this week</div>
            <div class="arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </div>
        <div class="workflow-card released">
            <div class="num" id="releasedCases">0</div>
            <div class="label">Released</div>
            <div class="trend up" id="releasedTrend">↑ 0% this month</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="/admin/social-case/new" class="quick-action">
            <i data-lucide="user-plus" style="width:24px;height:24px"></i>
            <span>New Case</span>
        </a>
        <a href="/admin/social-case/cases" class="quick-action">
            <i data-lucide="search" style="width:24px;height:24px"></i>
            <span>Search Client</span>
        </a>
        <a href="/admin/social-case/cases" class="quick-action">
            <i data-lucide="file-text" style="width:24px;height:24px"></i>
            <span>Generate Report</span>
        </a>
        <a href="/admin/social-case/cases" class="quick-action">
            <i data-lucide="printer" style="width:24px;height:24px"></i>
            <span>Print Cases</span>
        </a>
        <a href="/admin/social-case/cases" class="quick-action">
            <i data-lucide="check-circle" style="width:24px;height:24px"></i>
            <span>Released Cases</span>
        </a>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Left Column -->
        <div>
            <!-- Recent Cases Table -->
            <div class="panel">
                <h3>Recent Cases</h3>
                <table>
                    <tr><th>Control No</th><th>Client</th><th>Assistance Type</th><th>Status</th><th>Date</th></tr>
                    <tbody id="recentCasesTable"></tbody>
                </table>
            </div>

            <!-- Monthly Statistics Chart -->
            <div class="panel">
                <h3>Cases Processed per Month</h3>
                <div class="chart-container large">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Assistance Type Analytics -->
            <div class="panel">
                <h3>Most Requested Assistance</h3>
                <div class="chart-container">
                    <canvas id="assistanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Today's Activities -->
            <div class="panel">
                <h3>Today's Activities</h3>
                <div id="todayActivities"></div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="panel">
                <h3>Recent Activity</h3>
                <div id="activityFeed"></div>
            </div>

            <!-- Upcoming Follow-ups -->
            <div class="panel" id="followUpPanel" style="display:none;">
                <h3>Upcoming Follow-ups</h3>
                <div id="followUpList"></div>
            </div>

            <!-- Barangay Distribution -->
            <div class="panel">
                <h3>Top Barangays</h3>
                <div class="chart-container">
                    <canvas id="barangayChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Nearing Eligibility Panel -->
    <div class="panel" id="nearingEligiblePanel" style="display:none;">
        <h3>Nearing re-eligibility (within 30 days)</h3>
        <table>
            <tr><th>Client</th><th>Released</th><th>Eligible again</th><th>Days left</th><th></th></tr>
            <tbody id="nearingEligibleTable"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadDashboard();
    });
</script>
@endpush
