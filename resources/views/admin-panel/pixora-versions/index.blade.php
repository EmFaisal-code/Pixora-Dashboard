@extends('admin-panel.layout.app')

@section('title', 'Version Manager')
@section('page-title', 'Version Manager')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
    <li><i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i></li>
    <li class="text-slate-900 font-medium">Version Manager</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    {{-- ── Left: Add Version Form ── --}}
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                </div>
                <h3 class="text-lg font-display font-semibold text-slate-900">Tambah Versi</h3>
            </div>
            
            <div class="p-6">
                <form method="POST" action="{{ route('admin.pixora-versions.store') }}" class="space-y-5">
                    @csrf

                    {{-- Version Number --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Versi</label>
                        <input type="text" name="version" class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 focus:bg-white transition-colors" placeholder="e.g. 2.5.0" required>
                        <p class="mt-1.5 text-xs text-slate-500">Format: major.minor.patch</p>
                    </div>

                    {{-- Toggles --}}
                    <div class="space-y-4 pt-2">
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Izinkan versi ini</span>
                            <div class="relative inline-block w-11 h-6 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" name="allowed" value="1" id="allowedCheck" checked class="peer sr-only">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </div>
                        </label>
                        
                        <div class="border-t border-slate-100 pt-4"></div>

                        <label class="flex items-start justify-between cursor-pointer group">
                            <div>
                                <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Jadikan Latest Version</span>
                                <p class="text-xs text-slate-500 mt-0.5">Menampilkan popup update opsional</p>
                            </div>
                            <div class="relative inline-block w-11 h-6 align-middle select-none transition duration-200 ease-in mt-1">
                                <input type="checkbox" name="set_as_latest" value="1" id="latestCheck" checked class="peer sr-only">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
                            </div>
                        </label>
                    </div>

                    {{-- Update Message --}}
                    <div class="pt-2">
                        <label class="flex items-center text-sm font-medium text-slate-700 mb-1.5">
                            <i data-lucide="message-square" class="w-4 h-4 mr-2 text-slate-400"></i>
                            Pesan Update (What's New)
                        </label>
                        <textarea name="update_message" rows="4" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 focus:bg-white transition-colors resize-none" placeholder="Tulis catatan rilis di sini...&#10;Contoh:&#10;- Perbaikan deteksi CAPTCHA&#10;- Peningkatan kecepatan upload">{{ $currentConfig['update_message'] ?? '' }}</textarea>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">Pesan ini akan muncul di popup update opsional pada ekstensi user. Kosongkan jika tidak ada perubahan.</p>
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors mt-6">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Versi
                    </button>
                </form>
            </div>
        </div>

        {{-- Current Config Summary --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            
            <div class="flex items-center gap-2 mb-4 relative z-10">
                <i data-lucide="info" class="w-5 h-5 text-primary-400"></i>
                <h3 class="text-sm font-display font-semibold tracking-wide uppercase text-slate-300">Konfigurasi Aktif</h3>
            </div>
            
            <div class="space-y-4 relative z-10">
                <div>
                    <span class="block text-xs text-slate-400 mb-1">Latest Version</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-primary-500/20 text-primary-300 border border-primary-500/30">
                        v{{ $currentConfig['latest_version'] ?? 'belum diset' }}
                    </span>
                </div>
                <div>
                    <span class="block text-xs text-slate-400 mb-1">Update Message</span>
                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <p class="text-sm text-slate-300 whitespace-pre-line leading-relaxed">{{ !empty($currentConfig['update_message']) ? $currentConfig['update_message'] : 'Tidak ada pesan pembaruan.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right: Version List ── --}}
    <div class="lg:col-span-8">
        <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden h-full">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                        <i data-lucide="git-branch" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-display font-semibold text-slate-900">Daftar Versi</h3>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Allowed</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Blocked</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 bg-slate-50/50 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium">Versi</th>
                            <th scope="col" class="px-6 py-4 font-medium">Status</th>
                            <th scope="col" class="px-6 py-4 font-medium">Ditambahkan</th>
                            <th scope="col" class="px-6 py-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($versions as $v)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-800">
                                    v{{ $v['version'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($v['allowed'])
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <i data-lucide="check" class="w-3 h-3 mr-1.5"></i> Allowed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                        <i data-lucide="ban" class="w-3 h-3 mr-1.5"></i> Blocked
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ \Carbon\Carbon::parse($v['created_at'])->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.pixora-versions.toggle', $v['version']) }}" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $v['allowed'] ? 'text-red-700 bg-red-50 hover:bg-red-100 border border-red-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100' }}"
                                            onclick="return confirm('{{ $v['allowed'] ? 'Block' : 'Allow' }} versi {{ $v['version'] }}?')">
                                            <i data-lucide="{{ $v['allowed'] ? 'ban' : 'check-circle' }}" class="w-3.5 h-3.5 mr-1.5"></i>
                                            {{ $v['allowed'] ? 'Block' : 'Allow' }}
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.pixora-versions.destroy', $v['version']) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            onclick="return confirm('Hapus versi {{ $v['version'] }} secara permanen?')">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-12 px-4">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4 border border-slate-100">
                                        <i data-lucide="git-branch" class="w-8 h-8"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900">Belum ada versi</h3>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan versi aplikasi pertama Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
