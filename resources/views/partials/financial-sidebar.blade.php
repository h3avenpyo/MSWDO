<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-wrapper" onclick="handleBrandClick()" title="Financial Assistance">
            <i class="fas fa-hand-holding-usd sidebar-brand-icon"></i>
            <span class="sidebar-brand-text">Financial Assistance</span>
        </div>
        <button type="button" class="sidebar-toggle-btn" id="sidebarCollapseBtn" onclick="toggleSidebarCollapse()" aria-label="Collapse sidebar" title="Collapse sidebar">
            <i class="fas fa-chevron-left toggle-icon"></i>
        </button>
    </div>
    <ul class="sidebar-menu">
        @if(request()->is('admin/financial/financialstep2*') || session('admin_user_role') === 'financialstep2')
        <li>
            <a href="{{ route('admin.financial.financialstep2') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2') ? 'active' : '' }}"
                data-tooltip="Step 2 Dashboard"
                title="Step 2 Dashboard">
                <i class="fas fa-hand-holding-usd"></i>
                <span class="menu-label">Step 2 Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.payroll') ? 'active' : '' }}"
                data-tooltip="Payroll Generation"
                title="Payroll Generation">
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="menu-label">Payroll Generation</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.payroll-records*') ? 'active' : '' }}"
                data-tooltip="Payroll Records"
                title="Payroll Records">
                <i class="fas fa-archive"></i>
                <span class="menu-label">Payroll Records</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.liquidation') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.liquidation*') ? 'active' : '' }}"
                data-tooltip="Liquidation"
                title="Liquidation">
                <i class="fas fa-receipt"></i>
                <span class="menu-label">Liquidation</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.all-intakes') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.all-intakes') ? 'active' : '' }}"
                data-tooltip="All Masterlist"
                title="All Masterlist">
                <i class="fas fa-layer-group"></i>
                <span class="menu-label">All Masterlist</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.statistics') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.statistics*') ? 'active' : '' }}"
                data-tooltip="Statistics"
                title="Statistics">
                <i class="fas fa-chart-pie"></i>
                <span class="menu-label">Statistics</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="confirmLogout(event)"
                data-tooltip="Logout"
                title="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-label">Logout</span>
            </a>
        </li>
        @else
        <li>
            <a href="/admin/financial/dashboard"
                class="{{ request()->is('admin/financial/dashboard') ? 'active' : '' }}"
                data-tooltip="Financial Dashboard"
                title="Financial Dashboard">
                <i class="fas fa-th-large"></i>
                <span class="menu-label">Financial Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/admin/financial/financialstep1"
                class="{{ request()->is('admin/financial/financialstep1') ? 'active' : '' }}"
                data-tooltip="Step 1: Intake"
                title="Step 1: Intake">
                <i class="fas fa-clipboard-list"></i>
                <span class="menu-label">Step 1: Intake</span>
            </a>
        </li>
        <li>
            <a href="/admin/beneficiary-intake"
                class="{{ request()->is('admin/beneficiary-intake*') ? 'active' : '' }}"
                data-tooltip="All Intakes"
                title="All Intakes">
                <i class="fas fa-list"></i>
                <span class="menu-label">All Intakes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep1statistics') }}"
                class="{{ request()->is('admin/financial/financialstep1statistics*') ? 'active' : '' }}"
                data-tooltip="Statistics & Analytics"
                title="Statistics &amp; Analytics">
                <i class="fas fa-chart-pie"></i>
                <span class="menu-label">Statistics &amp; Analytics</span>
            </a>
        </li>
        <li>
            <a href="#" onclick="confirmLogout(event)"
                data-tooltip="Logout"
                title="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-label">Logout</span>
            </a>
        </li>
        @endif
    </ul>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>