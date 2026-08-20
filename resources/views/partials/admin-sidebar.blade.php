<div class="sidebar" id="sidebar">
    <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard" class="{{ request()->is('admin/social-case/dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
        
        <li class="sidebar-menu-header">Cases</li>
        <li><a href="/admin/social-case/new" class="submenu {{ request()->is('admin/social-case/new') ? 'active' : '' }}"><i class="fas fa-user-plus"></i> New Social Case</a></li>
        <li><a href="/admin/social-case/cases" class="submenu {{ request()->is('admin/social-case/cases') ? 'active' : '' }}"><i class="fas fa-folder-open"></i> Active Cases</a></li>
        <li><a href="/admin/social-case/archive" class="submenu {{ request()->is('admin/social-case/archive') ? 'active' : '' }}"><i class="fas fa-archive"></i> Archive</a></li>
        
        <li class="sidebar-menu-header">Clients</li>
        <li><a href="/admin/beneficiary-intake" class="submenu {{ request()->is('admin/beneficiary-intake/*') ? 'active' : '' }}"><i class="fas fa-users"></i> Beneficiary Intake</a></li>
        
        <li class="sidebar-menu-header">System</li>
        <li><a href="#" class="submenu"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
