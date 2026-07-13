<div class="sidebar" id="sidebar">
    <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard" class="{{ request()->is('admin/social-case/dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
        
        <li class="sidebar-menu-header">Cases</li>
        <li><a href="{{ route('admin.social-case-eligibility.index') }}" class="submenu {{ request()->routeIs('admin.social-case-eligibility.*') ? 'active' : '' }}"><i class="fas fa-user-plus"></i> New Social Case</a></li>
        <li><a href="/admin/social-case-studies" class="submenu {{ request()->is('admin/social-case-studies/*') ? 'active' : '' }}"><i class="fas fa-folder-open"></i> Active Cases</a></li>
        <li><a href="{{ route('admin.social-case.released') }}" class="submenu {{ request()->routeIs('admin.social-case.released') ? 'active' : '' }}"><i class="fas fa-archive"></i> Released</a></li>
        
        <li class="sidebar-menu-header">Clients</li>
        <li><a href="/admin/beneficiary-intake" class="submenu {{ request()->is('admin/beneficiary-intake/*') ? 'active' : '' }}"><i class="fas fa-users"></i> Beneficiary Intake</a></li>
        
        <li class="sidebar-menu-header">Reports</li>
        <li><a href="{{ route('admin.social-case.reports') }}" class="submenu {{ request()->routeIs('admin.social-case.reports*') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Generate Reports</a></li>
        
        <li class="sidebar-menu-header">System</li>
        <li><a href="#" class="submenu"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
