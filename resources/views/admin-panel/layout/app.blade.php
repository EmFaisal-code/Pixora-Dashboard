<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - Pixora</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/EM_INDONESIA.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite (Tailwind + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-x-hidden" x-data="{ sidebarOpen: false }">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        @include('admin-panel.layout.sidebar')

        <!-- Main Content -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <!-- Header -->
            @include('admin-panel.layout.header')

            <!-- Main Page Content -->
            <main class="w-full px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
                
                <!-- Page Title & Breadcrumbs -->
                <div class="sm:flex sm:justify-between sm:items-center mb-8">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl md:text-3xl font-display font-bold text-slate-900 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                        <nav class="mt-1" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Home</a>
                                </li>
                                @hasSection('breadcrumb')
                                    <li>
                                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                                    </li>
                                    @yield('breadcrumb')
                                @endif
                            </ol>
                        </nav>
                    </div>
                    
                    <!-- Right side actions -->
                    <div class="flex gap-2">
                        @yield('header-actions')
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 bg-accent-50 border border-accent-200 text-accent-700 px-4 py-4 rounded-2xl relative flex items-start shadow-sm" role="alert">
                        <i data-lucide="check-circle-2" class="w-5 h-5 mr-3 mt-0.5 text-accent-500"></i>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                        <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-4 hover:opacity-75 transition-opacity">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-2xl relative flex items-start shadow-sm" role="alert">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-3 mt-0.5 text-red-500"></i>
                        <span class="block sm:inline font-medium">{{ session('error') }}</span>
                        <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-4 hover:opacity-75 transition-opacity">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif

                <!-- Content -->
                @yield('content')

            </main>
            
            <!-- Footer -->
            <footer class="mt-auto p-6 text-center text-sm text-slate-500 border-t border-slate-200 bg-white/40">
                &copy; {{ date('Y') }} <span class="font-display font-semibold text-slate-700">Pixora</span>. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
