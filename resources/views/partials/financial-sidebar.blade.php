<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Financial Assistance</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="/admin/financial/dashboard" class="{{ request()->is('admin/financial/dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/admin/financial/financialstep1" class="{{ request()->is('admin/financial/financialstep1') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> Intake &amp; Assessment
            </a>
        </li>
        <li>
            <a href="/admin/beneficiary-intake" class="{{ request()->is('admin/beneficiary-intake*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Masterlist
            </a>
        </li>
        <li>
            <a href="{{ route('admin.financial.financialstep1statistics') }}" class="{{ request()->is('admin/financial/financialstep1statistics*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Statistics &amp; Analytics
            </a>
        </li>
        <li>
            <a href="#" onclick="confirmLogout(event)">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
