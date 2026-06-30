@extends('admin-panel.layout.app')

@section('title', 'Pixora Users')
@section('page-title', 'Pixora Users')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
    <li><i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i></li>
    <li class="text-slate-900 font-medium">Users</li>
@endsection

@section('header-actions')
    <div x-data="{ isRefreshing: false }" @refresh-start.window="isRefreshing = true" @refresh-end.window="isRefreshing = false" class="flex items-center gap-3">
        <button @click="$dispatch('trigger-refresh')" :disabled="isRefreshing" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            <i data-lucide="refresh-cw" :class="isRefreshing ? 'animate-spin text-primary-600' : ''" class="w-4 h-4 mr-2"></i>
            <span x-text="isRefreshing ? 'Memuat...' : 'Refresh Data'"></span>
        </button>

        <button class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm shadow-primary-500/30 transition-all">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Add User
        </button>
    </div>
@endsection

@section('content')

<div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden" 
     x-data="{ 
        search: '', 
        users: [],
        refreshError: null,
        selected: [],
        showBanModal: false,
        showDeleteModal: false,
        banReason: '',
        isProcessingBulk: false,
        showUserPanel: false,
        selectedUser: null,
        isLoadingTikTok: false,
        tiktokData: null,
        get allUsernames() {
            return Array.from(document.querySelectorAll('.row-checkbox')).map(cb => cb.value);
        },
        toggleAll(checked) {
            this.selected = checked ? this.allUsernames : [];
        },
        async openUserPanel(user) {
            this.selectedUser = user;
            this.showUserPanel = true;
            this.tiktokData = null;
            this.isLoadingTikTok = true;
            
            try {
                const response = await fetch(`/admin/api/tiktok-profile/${user.username}`);
                if (!response.ok) {
                    this.tiktokData = { error: 'Profil diprivat atau tidak ditemukan' };
                } else {
                    this.tiktokData = await response.json();
                }
            } catch(e) {
                this.tiktokData = { error: 'Terjadi kesalahan jaringan' };
            } finally {
                this.isLoadingTikTok = false;
            }
        },
        async bulkDelete() {
            if (this.selected.length === 0) return;
            this.isProcessingBulk = true;
            try {
                const response = await fetch('{{ route('admin.pixora-users.bulk-delete') }}', {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        usernames: this.selected
                    })
                });
                if (!response.ok) {
                    let errorMsg = 'Gagal menghapus pengguna';
                    try {
                        const errText = await response.text();
                        try {
                            const errData = JSON.parse(errText);
                            errorMsg = errData.error || errData.message || errorMsg;
                        } catch(e) {
                            errorMsg = 'Kesalahan Server (Status ' + response.status + ').';
                        }
                    } catch (e) {}
                    throw new Error(errorMsg);
                }
                this.selected = [];
                this.showDeleteModal = false;
                await this.refreshTable();
            } catch (err) {
                alert(err.message);
            } finally {
                this.isProcessingBulk = false;
            }
        },
        async bulkBan() {
            if (this.selected.length === 0) return;
            this.isProcessingBulk = true;
            try {
                const response = await fetch('{{ route('admin.pixora-users.bulk-ban') }}', {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        usernames: this.selected,
                        reason: this.banReason
                    })
                });
                if (!response.ok) {
                    let errorMsg = 'Gagal memproses Ban';
                    try {
                        const errText = await response.text();
                        try {
                            const errData = JSON.parse(errText);
                            errorMsg = errData.error || errData.message || errorMsg;
                        } catch(e) {
                            errorMsg = 'Kesalahan Server (Status ' + response.status + ').';
                        }
                    } catch (e) {}
                    throw new Error(errorMsg);
                }
                this.selected = [];
                this.banReason = '';
                this.showBanModal = false;
                await this.refreshTable();
            } catch (err) {
                alert(err.message);
            } finally {
                this.isProcessingBulk = false;
            }
        },
        async refreshTable() {
            this.$dispatch('refresh-start');
            this.refreshError = null;
            try {
                const response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store'
                });
                if (!response.ok) throw new Error('Gagal mengambil data dari server');
                
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newTbody = doc.getElementById('users-tbody');
                if (newTbody) document.getElementById('users-tbody').innerHTML = newTbody.innerHTML;
                
                const newCount = doc.getElementById('users-total-count');
                if (newCount) document.getElementById('users-total-count').innerHTML = newCount.innerHTML;
                
                if (typeof lucide !== 'undefined') {
                    setTimeout(() => lucide.createIcons(), 50);
                }
            } catch (err) {
                this.refreshError = err.message || 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                setTimeout(() => this.refreshError = null, 5000);
            } finally {
                this.$dispatch('refresh-end');
            }
        }
     }"
     @trigger-refresh.window="refreshTable()">
    
    <!-- Error Alert -->
    <div x-show="refreshError" x-transition style="display: none;" class="px-6 py-3 bg-red-50/80 border-b border-red-100 flex items-center gap-3">
        <div class="p-1.5 bg-red-100 rounded-lg">
            <i data-lucide="wifi-off" class="w-4 h-4 text-red-600"></i>
        </div>
        <p class="text-sm text-red-700 font-medium" x-text="refreshError"></p>
    </div>

    <!-- Floating Bulk Action Bar -->
    <div x-show="selected.length > 0" x-transition style="display: none;" class="bg-indigo-50 border-b border-indigo-100 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2 text-indigo-700 font-medium">
            <span class="flex items-center justify-center px-2 py-0.5 bg-indigo-200 text-indigo-800 rounded-md text-xs" x-text="selected.length"></span>
            <span class="text-sm">pengguna terpilih</span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showBanModal = true" class="px-3 py-1.5 text-sm font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors flex items-center shadow-sm">
                <i data-lucide="ban" class="w-4 h-4 mr-1.5"></i> Ban Terpilih
            </button>
            <button @click="showDeleteModal = true" class="px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-lg transition-colors flex items-center shadow-sm">
                <i data-lucide="trash-2" class="w-4 h-4 mr-1.5"></i> Hapus Terpilih
            </button>
        </div>
    </div>

    <!-- Table Header & Controls -->
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-50 rounded-lg text-primary-600 hidden sm:block">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-lg font-display font-semibold text-slate-900">Daftar User Pixora</h3>
                <p id="users-total-count" class="text-sm text-slate-500 mt-0.5">Total {{ count($users) }} users terdaftar. (Min. Version: v{{ $minVersion }})</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Search Input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
                </div>
                <input x-model="search" type="text" placeholder="Cari username..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 sm:text-sm transition-colors">
            </div>
            <button class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors">
                <i data-lucide="filter" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        @if(count($users) > 0)
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 bg-slate-50/50 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4 w-12 text-center">
                        <input type="checkbox" @change="toggleAll($event.target.checked)" :checked="selected.length > 0 && selected.length === allUsernames.length" class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500 cursor-pointer">
                    </th>
                    <th scope="col" class="px-6 py-4 font-medium">
                        <a href="{{ route('admin.pixora-users', ['sort' => 'username', 'dir' => ($sort == 'username' && $dir == 'asc' ? 'desc' : 'asc')]) }}" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                            Pengguna
                            @if($sort == 'username')
                                <i data-lucide="{{ $dir == 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-4 h-4 text-slate-300"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-4 font-medium">
                        <a href="{{ route('admin.pixora-users', ['sort' => 'status', 'dir' => ($sort == 'status' && $dir == 'asc' ? 'desc' : 'asc')]) }}" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                            Status
                            @if($sort == 'status')
                                <i data-lucide="{{ $dir == 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-4 h-4 text-slate-300"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-4 font-medium">
                        <a href="{{ route('admin.pixora-users', ['sort' => 'version', 'dir' => ($sort == 'version' && $dir == 'asc' ? 'desc' : 'asc')]) }}" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                            Versi Aplikasi
                            @if($sort == 'version')
                                <i data-lucide="{{ $dir == 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-4 h-4 text-slate-300"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-4 font-medium">
                        <a href="{{ route('admin.pixora-users', ['sort' => 'activity', 'dir' => ($sort == 'activity' && $dir == 'asc' ? 'desc' : 'asc')]) }}" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                            Aktivitas
                            @if($sort == 'activity')
                                <i data-lucide="{{ $dir == 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-4 h-4 text-slate-300"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-4 font-medium">
                        <a href="{{ route('admin.pixora-users', ['sort' => 'date', 'dir' => ($sort == 'date' && $dir == 'asc' ? 'desc' : 'asc')]) }}" class="flex items-center gap-1 hover:text-primary-600 transition-colors">
                            Tanggal Gabung
                            @if($sort == 'date')
                                <i data-lucide="{{ $dir == 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-4 h-4 text-slate-300"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" class="px-6 py-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="users-tbody" class="divide-y divide-slate-100">
                @foreach($users as $i => $user)
                @php
                    $versionUsed = $user['version_used'] ?? null;
                    $isOutdated = $versionUsed && version_compare($versionUsed, $minVersion, '<');
                    $isBanned = $user['is_banned'] ?? false;
                    $isOnline = isset($user['last_seen']) && (strtotime($user['last_seen']) > (time() - 600));
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors {{ $isBanned ? 'bg-red-50/30' : ($isOutdated ? 'bg-amber-50/30' : '') }}" 
                    x-show="search === '' || '{{ strtolower($user['username']) }}'.includes(search.toLowerCase())">
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" value="{{ $user['username'] }}" x-model="selected" class="row-checkbox w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500 cursor-pointer">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-medium">
                                    {{ substr(str_replace('@', '', $user['username']), 0, 1) }}
                                </div>
                                @if($isOnline && !$isBanned)
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-slate-900 block">{{ $user['username'] }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($isBanned)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                <i data-lucide="ban" class="w-3 h-3 mr-1.5"></i> Banned
                            </span>
                        @else
                            @if($isOnline)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    Offline
                                </span>
                            @endif
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($versionUsed)
                            @if($isOutdated)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200" title="Perlu update ke v{{ $minVersion }}">
                                    <i data-lucide="alert-triangle" class="w-3 h-3 mr-1.5 text-amber-500"></i> v{{ $versionUsed }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">
                                    v{{ $versionUsed }}
                                </span>
                            @endif
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($user['last_seen'] ?? false)
                            <span class="text-slate-600 block">{{ \Carbon\Carbon::parse($user['last_seen'])->diffForHumans() }}</span>
                            <span class="text-xs text-slate-400">{{ date('H:i', strtotime($user['last_seen'])) }}</span>
                        @else
                            <span class="text-slate-400">Belum pernah</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium text-slate-700 block">{{ date('d M Y', strtotime($user['created_at'])) }}</span>
                        <span class="text-xs text-slate-400">{{ date('H:i', strtotime($user['created_at'])) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button @click="openUserPanel({{ json_encode($user) }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.pixora-users.toggle-ban', urlencode($user['username'])) }}" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="p-2 rounded-lg transition-colors {{ $isBanned ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:text-amber-600 hover:bg-amber-50' }}"
                                        title="{{ $isBanned ? 'Unban' : 'Ban' }}"
                                        onclick="return confirm('{{ $isBanned ? 'Unban' : 'Ban' }} user {{ $user['username'] }}?')">
                                    <i data-lucide="{{ $isBanned ? 'unlock' : 'ban' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.pixora-users.delete', urlencode($user['username'])) }}" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" 
                                        title="Hapus"
                                        onclick="return confirm('Hapus user {{ $user['username'] }} secara permanen?')">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-16 px-4">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4 border border-slate-100">
                <i data-lucide="users" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-medium text-slate-900">Belum ada user</h3>
            <p class="mt-1 text-sm text-slate-500">Saat ini belum ada user yang terdaftar dalam sistem.</p>
        </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" aria-hidden="true" @click="if(!isProcessingBulk) showDeleteModal = false"></div>
            
            <div x-show="showDeleteModal" x-transition.scale class="relative z-10 inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl sm:my-8">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-medium leading-6 text-slate-900" id="modal-title">Hapus <span x-text="selected.length"></span> Pengguna</h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500">Tindakan ini akan menghapus pengguna terpilih secara permanen dan tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="button" @click="bulkDelete()" :disabled="isProcessingBulk" class="inline-flex justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-xl shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 sm:w-auto items-center">
                        <i data-lucide="loader-2" x-show="isProcessingBulk" class="w-4 h-4 mr-2 animate-spin" style="display:none"></i>
                        Konfirmasi Hapus
                    </button>
                    <button type="button" @click="showDeleteModal = false" :disabled="isProcessingBulk" class="inline-flex justify-center w-full px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 disabled:opacity-50 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ban Modal -->
    <div x-show="showBanModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="showBanModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" aria-hidden="true" @click="if(!isProcessingBulk) showBanModal = false"></div>
            
            <div x-show="showBanModal" x-transition.scale class="relative z-10 inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl sm:my-8">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-100 rounded-full">
                        <i data-lucide="ban" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <div class="mt-4 w-full text-center">
                        <h3 class="text-lg font-medium leading-6 text-slate-900" id="modal-title">Ban <span x-text="selected.length"></span> Pengguna</h3>
                        <div class="mt-3 text-left">
                            <label for="ban-reason" class="block text-sm font-medium text-slate-700 mb-2">Alasan Banned (Opsional)</label>
                            <textarea id="ban-reason" x-model="banReason" rows="3" class="block w-full border border-slate-300 rounded-xl shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm p-3 placeholder-slate-400 bg-slate-50 focus:bg-white transition-colors" placeholder="Masukkan alasan kenapa pengguna ini dibanned..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="button" @click="bulkBan()" :disabled="isProcessingBulk" class="inline-flex justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-amber-600 border border-transparent rounded-xl shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50 sm:w-auto items-center">
                        <i data-lucide="loader-2" x-show="isProcessingBulk" class="w-4 h-4 mr-2 animate-spin" style="display:none"></i>
                        Setujui Ban
                    </button>
                    <button type="button" @click="showBanModal = false" :disabled="isProcessingBulk" class="inline-flex justify-center w-full px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 disabled:opacity-50 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SLIDE OVER PANEL DETAIL PENGGUNA -->
    <div x-show="showUserPanel" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <!-- Background Overlay -->
        <div x-show="showUserPanel" 
             x-transition:enter="ease-in-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in-out duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="showUserPanel = false"></div>

        <!-- Panel Container: fixed, full-screen, no overflow-hidden ancestors -->
        <div class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md pointer-events-none sm:pl-10">
            <!-- Panel -->
            <div x-show="showUserPanel" 
                 @click.outside="showUserPanel = false"
                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full" 
                 class="pointer-events-auto w-full h-full">
                <div class="grid h-full bg-white shadow-2xl border-l border-slate-200" style="grid-template-rows: auto minmax(0, 1fr) auto;">
                    
                    <!-- Header -->
                    <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-lg border border-primary-200 shadow-sm">
                                    <span x-text="selectedUser ? selectedUser.username.replace('@', '').substring(0,1).toUpperCase() : ''"></span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900" id="slide-over-title" x-text="selectedUser ? selectedUser.username : ''"></h2>
                                    <div class="mt-0.5 flex items-center">
                                        <template x-if="selectedUser?.is_banned">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800">
                                                <i data-lucide="shield-alert" class="w-3 h-3 mr-1"></i> Banned
                                            </span>
                                        </template>
                                        <template x-if="!selectedUser?.is_banned">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800">
                                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Active
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-3 flex h-7 items-center">
                                <button type="button" @click="showUserPanel = false" class="rounded-lg bg-white text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 p-1 border border-transparent hover:border-slate-200 transition-colors">
                                    <span class="sr-only">Close panel</span>
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Content: the ONLY scrollable element -->
                    <div class="overflow-y-auto overscroll-contain px-4 py-4 sm:px-6 sm:py-5 bg-white min-h-0" style="-webkit-overflow-scrolling: touch;">
                        
                        <!-- Internal System Info -->
                        <div class="mb-4">
                            <h3 class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2 border-b border-slate-100 pb-1">Informasi Sistem (Internal)</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center p-2 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex items-center gap-2 text-xs text-slate-600 whitespace-nowrap">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 shrink-0"></i> Mendaftar
                                    </div>
                                    <div class="text-xs font-medium text-slate-900 text-right ml-4 break-words" x-text="selectedUser?.created_at ? new Date(selectedUser.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '-'"></div>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex items-center gap-2 text-xs text-slate-600 whitespace-nowrap">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i> Terakhir Aktif
                                    </div>
                                    <div class="text-xs font-medium text-slate-900 text-right ml-4 break-words" x-text="selectedUser?.last_seen ? new Date(selectedUser.last_seen).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'Belum pernah'"></div>
                                </div>
                                <div class="flex justify-between items-center p-2 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex items-center gap-2 text-xs text-slate-600 whitespace-nowrap">
                                        <i data-lucide="smartphone" class="w-3.5 h-3.5 shrink-0"></i> Versi Aplikasi
                                    </div>
                                    <div class="text-xs font-medium text-slate-900 bg-white px-2 py-0.5 rounded shadow-sm border border-slate-200 shrink-0" x-text="selectedUser?.version_used || 'Unknown'"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- TikTok Public Info -->
                        <div>
                            <h3 class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2 border-b border-slate-100 pb-1 flex items-center gap-2">
                                Informasi Publik TikTok
                            </h3>
                            
                            <!-- Skeleton Loading -->
                            <div x-show="isLoadingTikTok" class="animate-pulse space-y-2">
                                <div class="h-16 bg-slate-100 rounded-2xl border border-slate-200"></div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="h-12 bg-slate-100 rounded-xl border border-slate-200"></div>
                                    <div class="h-12 bg-slate-100 rounded-xl border border-slate-200"></div>
                                    <div class="h-12 bg-slate-100 rounded-xl border border-slate-200"></div>
                                </div>
                            </div>
                            
                            <!-- Error / Private -->
                            <div x-show="!isLoadingTikTok && tiktokData?.error" class="bg-slate-50 border border-slate-200 border-dashed rounded-xl p-4 text-center">
                                <div class="mx-auto flex items-center justify-center h-10 w-10 rounded-full bg-slate-100 mb-2 text-slate-400">
                                    <i data-lucide="lock" class="h-5 w-5"></i>
                                </div>
                                <h3 class="text-xs font-medium text-slate-900" x-text="tiktokData?.error"></h3>
                                <p class="mt-1 text-[10px] text-slate-500">TikTok membatasi akses pada profil publik ini.</p>
                            </div>
                            
                            <!-- Data Ready -->
                            <div x-show="!isLoadingTikTok && tiktokData && !tiktokData.error">
                                <!-- Profile Card -->
                                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-3 text-white shadow-lg mb-2 flex items-center gap-3 relative overflow-hidden">
                                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center border border-white/30 backdrop-blur-sm z-10 shrink-0">
                                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                    </div>
                                    <div class="z-10 truncate">
                                        <p class="text-[10px] text-slate-300 font-medium mb-0.5">Display Name</p>
                                        <p class="text-base font-bold truncate" x-text="tiktokData?.display_name"></p>
                                    </div>
                                </div>
                                
                                <!-- Stats Grid -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-2 text-center transition-transform hover:-translate-y-1 hover:shadow-md">
                                        <p class="text-[10px] font-medium text-slate-500 mb-0.5">Followers</p>
                                        <p class="text-sm font-bold text-slate-900" x-text="tiktokData?.followers"></p>
                                    </div>
                                    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-2 text-center transition-transform hover:-translate-y-1 hover:shadow-md">
                                        <p class="text-[10px] font-medium text-slate-500 mb-0.5">Following</p>
                                        <p class="text-sm font-bold text-slate-900" x-text="tiktokData?.following"></p>
                                    </div>
                                    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-2 text-center transition-transform hover:-translate-y-1 hover:shadow-md">
                                        <p class="text-[10px] font-medium text-slate-500 mb-0.5">Likes</p>
                                        <p class="text-sm font-bold text-slate-900" x-text="tiktokData?.likes"></p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="border-t border-slate-100 p-4 bg-slate-50">
                        <button type="button" @click="showUserPanel = false" class="w-full justify-center inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm border border-slate-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
                            Tutup Panel
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
