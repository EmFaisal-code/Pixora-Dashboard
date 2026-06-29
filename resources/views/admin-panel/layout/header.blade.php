<header class="sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 bg-white/80 backdrop-blur-md border-b border-slate-200">
    <div class="flex items-center">
        <!-- Hamburger Menu Button -->
        <button class="text-slate-500 hover:text-slate-700 focus:outline-none lg:hidden mr-4 p-2 -ml-2 rounded-lg hover:bg-slate-100 transition-colors" 
                @click.stop="sidebarOpen = !sidebarOpen" 
                aria-controls="sidebar" 
                :aria-expanded="sidebarOpen">
            <span class="sr-only">Open sidebar</span>
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <!-- Search (Optional, visually there for modern feel) -->
        <div class="hidden sm:flex items-center bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-400 transition-all">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 mr-2"></i>
            <input type="text" placeholder="Search everywhere..." class="bg-transparent border-none focus:outline-none text-sm text-slate-700 w-48 lg:w-64 placeholder-slate-400">
            <div class="hidden lg:flex items-center justify-center bg-white border border-slate-200 rounded px-1.5 py-0.5 ml-2">
                <span class="text-[10px] font-medium text-slate-400">⌘K</span>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Notifications -->
        <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100 transition-colors focus:outline-none">
            <span class="sr-only">View notifications</span>
            <i data-lucide="bell" class="w-5 h-5"></i>
            <!-- Indicator -->
            <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>

        <!-- Profile dropdown -->
        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen" 
                    @click.outside="profileOpen = false"
                    class="flex items-center gap-2 focus:outline-none rounded-full p-1 pr-2 hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center text-primary-700 font-display font-semibold border border-primary-200">
                    {{ substr(Auth::user()->username ?? 'A', 0, 1) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-slate-700">{{ Auth::user()->username ?? 'Admin' }}</span>
                <i data-lucide="chevron-down" class="hidden md:block w-4 h-4 text-slate-400"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div x-show="profileOpen" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg shadow-slate-200/50 py-1 border border-slate-200 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                 x-cloak>
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->username ?? 'Administrator' }}</p>
                    <p class="text-xs text-slate-500 truncate mt-0.5">Admin Account</p>
                </div>
                <a href="{{ route('admin.settings.password') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary-600 transition-colors">
                    <i data-lucide="key" class="w-4 h-4 mr-2 text-slate-400"></i> Change Password
                </a>
                <div class="border-t border-slate-100 mt-1 pt-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4 mr-2 text-red-500"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
