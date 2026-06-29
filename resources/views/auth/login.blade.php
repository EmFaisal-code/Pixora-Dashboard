<!DOCTYPE html>
<html lang="en" class="antialiased h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Pixora Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center bg-slate-900 font-sans selection:bg-primary-500 selection:text-white relative overflow-hidden">

    <!-- Decorative blobs -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none flex items-center justify-center">
        <div class="absolute top-[-20%] left-[-10%] w-[70vw] h-[70vw] rounded-full bg-primary-600/20 blur-[120px] mix-blend-screen"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-violet-600/20 blur-[120px] mix-blend-screen"></div>
    </div>

    <div class="w-full max-w-md px-6 z-10 relative">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-primary-500 to-violet-500 shadow-xl shadow-primary-500/30 mb-5 border border-white/10">
                <i data-lucide="smartphone" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-display font-bold text-white tracking-tight mb-2">Pixora Admin</h1>
            <p class="text-slate-400">Silakan login untuk mengelola sistem</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 p-8 rounded-3xl shadow-2xl">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-200 text-sm">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-red-400 mt-0.5"></i>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="post" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                
                <div class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="block w-full pl-11 pr-4 py-3.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500/50 transition-all shadow-inner"
                                   placeholder="admin@pixora.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="w-5 h-5 text-slate-500"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                   class="block w-full pl-11 pr-4 py-3.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500/50 transition-all shadow-inner"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center pt-1">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-900/50 text-primary-500 focus:ring-primary-500/50 focus:ring-offset-slate-800 transition-colors">
                        <label for="remember" class="ml-2.5 block text-sm text-slate-300 cursor-pointer select-none">
                            Ingat sesi saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="loading"
                            class="w-full flex items-center justify-center py-3.5 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-violet-600 hover:from-primary-500 hover:to-violet-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 shadow-lg shadow-primary-500/25 transition-all disabled:opacity-70 disabled:cursor-not-allowed group mt-2">
                        <span x-show="!loading">Masuk ke Dashboard</span>
                        <div x-show="loading" style="display: none;" class="flex items-center justify-center gap-2">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                            <span>Mengautentikasi...</span>
                        </div>
                        <i data-lucide="arrow-right" x-show="!loading" class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Badge -->
        <div class="mt-8 flex items-center justify-center gap-2.5 text-sm text-slate-500">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Secure & Encrypted Connection</span>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>