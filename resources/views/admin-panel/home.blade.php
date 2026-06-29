@extends('admin-panel.layout.app')

@section('title', 'Dashboard Overview')
@section('page-title', 'Overview')

@section('header-actions')
    <button class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm transition-all">
        <i data-lucide="download" class="w-4 h-4 mr-2 text-slate-500"></i>
        Export Report
    </button>
@endsection

@section('content')

    <!-- Pixora Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Users</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900">{{ $pixoraStats['total'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center text-primary-600">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <a href="{{ route('admin.pixora-users') }}" class="text-primary-600 hover:text-primary-700 font-medium inline-flex items-center group-hover:underline">
                    Manage users <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-accent-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Active Users</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900">{{ $pixoraStats['active'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-accent-50 flex items-center justify-center text-accent-600">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <a href="{{ route('admin.pixora-users') }}" class="text-accent-600 hover:text-accent-700 font-medium inline-flex items-center group-hover:underline">
                    View active <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Banned Users -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Banned Users</p>
                    <h3 class="text-3xl font-display font-bold text-slate-900">{{ $pixoraStats['banned'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500">
                    <i data-lucide="user-x" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <a href="{{ route('admin.pixora-users') }}" class="text-red-600 hover:text-red-700 font-medium inline-flex items-center group-hover:underline">
                    Review bans <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div x-data="dashboardAnalytics()" x-init="initData()" class="mb-8">
        <!-- Header / Filters -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-slate-900">Analytics</h2>
            <div class="flex items-center gap-2">
                <select x-model="days" @change="fetchData()" class="text-sm border-slate-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 py-2 pl-3 pr-8">
                    <option value="7">7 Hari</option>
                    <option value="14">14 Hari</option>
                    <option value="30">30 Hari</option>
                </select>
                <button @click="fetchData(true)" title="Refresh Data" class="p-2 text-slate-500 hover:text-primary-600 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4" :class="isLoading ? 'animate-spin text-primary-500' : ''"></i>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="isLoading && !stats" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 h-80 animate-pulse border border-slate-200 shadow-sm flex items-center justify-center">
                <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
            </div>
            <div class="bg-white rounded-2xl p-6 h-80 animate-pulse border border-slate-200 shadow-sm flex items-center justify-center">
                <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
            </div>
            <div class="lg:col-span-3 bg-white rounded-2xl p-6 h-80 animate-pulse border border-slate-200 shadow-sm flex items-center justify-center">
                <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
            </div>
        </div>

        <!-- Charts Content -->
        <div x-show="stats" style="display: none;">
            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- User Growth -->
                <div class="lg:col-span-2 bg-white border border-slate-200/60 shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Pertumbuhan Pengguna Baru</h3>
                    <div class="relative h-64">
                        <canvas x-ref="canvasGrowth"></canvas>
                    </div>
                </div>

                <!-- Version Distribution -->
                <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Distribusi Versi</h3>
                    <div class="relative h-64">
                        <canvas x-ref="canvasVersion"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 gap-6 mb-6">
                <!-- DAU -->
                <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800">Daily Active Users (DAU)</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Total DAU minggu ini: <span class="font-bold text-slate-700" x-text="stats?.dau?.total_current_week"></span>
                                <span class="ml-2 text-xs px-2 py-0.5 rounded-full inline-flex items-center" 
                                    :class="stats?.dau?.trend_percentage >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                    x-show="stats?.dau?.total_current_week > 0 || stats?.dau?.trend_percentage !== 0">
                                    <i :data-lucide="stats?.dau?.trend_percentage >= 0 ? 'trending-up' : 'trending-down'" class="w-3 h-3 mr-1"></i>
                                    <span x-text="stats?.dau?.trend_percentage >= 0 ? '+' : ''"></span><span x-text="stats?.dau?.trend_percentage"></span>%
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="relative h-64">
                        <canvas x-ref="canvasDau"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <h3 class="text-lg font-display font-semibold text-slate-900">Registrasi Terbaru</h3>
            </div>
            <a href="{{ route('admin.pixora-users') }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition-colors">
                Semua User <i data-lucide="arrow-right" class="w-4 h-4 ml-1.5"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            @if(count($recentUsers) > 0)
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 bg-slate-50/50 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium">User Profile</th>
                        <th scope="col" class="px-6 py-4 font-medium">Status</th>
                        <th scope="col" class="px-6 py-4 font-medium">Terdaftar</th>
                        <th scope="col" class="px-6 py-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentUsers as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-medium">
                                    {{ substr(str_replace('@', '', $user['username']), 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-medium text-slate-900">{{ $user['username'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user['is_banned'] ?? false)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Banned
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ \Carbon\Carbon::parse($user['created_at'])->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.pixora-users') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-12 px-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="text-sm font-medium text-slate-900">Belum ada user</h3>
                <p class="mt-1 text-sm text-slate-500">Belum ada user yang mendaftar ke sistem.</p>
            </div>
            @endif
        </div>
    </div>

@endsection
