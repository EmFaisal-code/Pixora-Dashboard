<!-- Sidebar -->
<div class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden transition-opacity" 
     x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     aria-hidden="true" x-cloak></div>

<div id="sidebar" class="flex flex-col absolute z-50 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 bg-slate-900 shadow-2xl transition-all duration-300 ease-in-out border-r border-slate-800"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'"
     @click.outside="sidebarOpen = false"
     @keydown.escape.window="sidebarOpen = false">

    <!-- Sidebar Header -->
    <div class="flex justify-between items-center px-6 py-6 border-b border-slate-800/60">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white shadow-lg shadow-primary-500/20">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <span class="text-xl font-display font-bold tracking-tight text-white">Pixora</span>
        </div>
        <!-- Close button for mobile -->
        <button class="lg:hidden text-slate-400 hover:text-white" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
            <span class="sr-only">Close sidebar</span>
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="px-6 py-5 border-b border-slate-800/60">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 font-display font-semibold border border-slate-700">
                {{ substr(Auth::user()->username ?? 'Admin', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->username ?? 'Admin' }}</p>
                <p class="text-xs text-slate-400 truncate">Administrator</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Main Menu</p>
        
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 ease-in-out {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white shadow-md shadow-primary-900/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
            <i data-lucide="layout-dashboard" class="mr-3 w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white transition-colors' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.pixora-users') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 ease-in-out {{ request()->routeIs('admin.pixora-users') ? 'bg-primary-600 text-white shadow-md shadow-primary-900/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
            <i data-lucide="users" class="mr-3 w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pixora-users') ? 'text-white' : 'text-slate-400 group-hover:text-white transition-colors' }}"></i>
            Pixora Users
        </a>
        
        <a href="{{ route('admin.pixora-versions') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 ease-in-out {{ request()->routeIs('admin.pixora-versions') ? 'bg-primary-600 text-white shadow-md shadow-primary-900/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
            <i data-lucide="git-branch" class="mr-3 w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pixora-versions') ? 'text-white' : 'text-slate-400 group-hover:text-white transition-colors' }}"></i>
            Version Manager
        </a>
        
        <a href="{{ route('admin.pixora-config') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 ease-in-out {{ request()->routeIs('admin.pixora-config') ? 'bg-primary-600 text-white shadow-md shadow-primary-900/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
            <i data-lucide="toggle-left" class="mr-3 w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pixora-config') ? 'text-white' : 'text-slate-400 group-hover:text-white transition-colors' }}"></i>
            Feature Config
        </a>

        <div x-data="{ expanded: {{ request()->is('admin/settings*') ? 'true' : 'false' }} }" class="mt-4">
            <button @click="expanded = !expanded" class="w-full group flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-xl text-slate-300 hover:bg-slate-800/60 hover:text-white transition-all duration-150 ease-in-out">
                <div class="flex items-center">
                    <i data-lucide="settings" class="mr-3 w-5 h-5 flex-shrink-0 text-slate-400 group-hover:text-white transition-colors"></i>
                    Settings
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': expanded}"></i>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-3 py-1 space-y-1">
                <a href="{{ route('admin.settings.password') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-150 ease-in-out {{ request()->routeIs('admin.settings.password') ? 'text-primary-400 bg-slate-800/50' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                    Change Password
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Logout -->
    <div class="p-4 border-t border-slate-800/60">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="group flex w-full items-center justify-center px-4 py-2.5 text-sm font-medium text-slate-300 bg-slate-800/50 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all duration-200 border border-slate-700 hover:border-red-500/30">
                <i data-lucide="log-out" class="mr-2 w-4 h-4"></i>
                Sign Out
            </button>
        </form>
    </div>
</div>
