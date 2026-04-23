<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <i class="fas fa-mobile-alt brand-image img-circle elevation-3" style="opacity: .8; margin-left: 10px; margin-right: 10px;"></i>
        <span class="brand-text font-weight-light">Pixora Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name ?? 'Admin User' }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Pixora Users -->
                <li class="nav-item">
                    <a href="{{ route('admin.pixora-users') }}" class="nav-link {{ request()->routeIs('admin.pixora-users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-mobile-alt"></i>
                        <p>Pixora Users</p>
                    </a>
                </li>

                <!-- Pixora Versions -->
                <li class="nav-item">
                    <a href="{{ route('admin.pixora-versions') }}" class="nav-link {{ request()->routeIs('admin.pixora-versions*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-code-branch"></i>
                        <p>Version Manager</p>
                    </a>
                </li>

                <!-- Pixora Config -->
                <li class="nav-item">
                    <a href="{{ route('admin.pixora-config') }}" class="nav-link {{ request()->routeIs('admin.pixora-config*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sliders-h"></i>
                        <p>Feature Config</p>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item {{ request()->routeIs('admin.settings*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.password') }}" class="nav-link {{ request()->routeIs('admin.settings.password') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>