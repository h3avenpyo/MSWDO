<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Financial Assistance</span>
    </div>
    <ul class="sidebar-menu">
        @if(request()->is('admin/financial/financialstep2*') || session('admin_user_role') === 'financialstep2')
        <li>
            <a href="{{ route('admin.financial.financialstep2') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd"></i> Step 2 Masterlist
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.payroll') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Payroll Generation
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.payroll-records*') ? 'active' : '' }}">
                <i class="fas fa-archive"></i> Payroll Records
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep2.all-intakes') }}"
                class="{{ request()->routeIs('admin.financial.financialstep2.all-intakes') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> All Intakes
            </a>
        </li>
        <li>
            <a href="#" onclick="confirmLogout(event)">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
        @else
        <li>
            <a href="/admin/financial/dashboard"
                class="{{ request()->is('admin/financial/dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Financial Dashboard
            </a>
        </li>
        <li>
            <a href="/admin/financial/financialstep1"
                class="{{ request()->is('admin/financial/financialstep1') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> Step 1: Intake
            </a>
        </li>
        <li>
            <a href="/admin/beneficiary-intake"
                class="{{ request()->is('admin/beneficiary-intake*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> All Intakes
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep1statistics') }}"
                class="{{ request()->is('admin/financial/financialstep1statistics*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Statistics &amp; Analytics
            </a>
        </li>
        <li>
            <a href="#" onclick="confirmLogout(event)">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
        @endif
    </ul>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>