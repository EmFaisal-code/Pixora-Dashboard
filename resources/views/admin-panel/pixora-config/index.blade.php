@extends('admin-panel.layout.app')

@section('title', 'Feature Config')
@section('page-title', 'Feature & Remote Config')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
    <li><i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i></li>
    <li class="text-slate-900 font-medium">Feature Config</li>
@endsection

@section('content')

@php
    // Grouping the configs for better UI
    $textConfigs = collect($configs)->filter(function($c) {
        return in_array($c['key'], ['min_version', 'latest_version', 'update_message', 'announcement', 'download_url']);
    });
    $toggleConfigs = collect($configs)->filter(function($c) {
        return !in_array($c['key'], ['min_version', 'latest_version', 'update_message', 'announcement', 'download_url']);
    });
@endphp

<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    
    {{-- ── Left: Text Configurations ── --}}
    <div class="xl:col-span-8 space-y-6">
        <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-lg font-display font-semibold text-slate-900">System Parameters</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Atur parameter dan konfigurasi utama ekstensi</p>
                </div>
            </div>
            
            <div class="divide-y divide-slate-100">
                @foreach($textConfigs as $config)
                <div class="p-6 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center gap-2 mb-3">
                        @if($config['key'] === 'min_version')
                            <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500"></i>
                            <span class="font-medium text-slate-900 text-sm">Force Update — Minimum Version</span>
                        @elseif($config['key'] === 'latest_version')
                            <i data-lucide="arrow-up-circle" class="w-4 h-4 text-emerald-500"></i>
                            <span class="font-medium text-slate-900 text-sm">Latest Version (Optional Update)</span>
                        @elseif($config['key'] === 'update_message')
                            <i data-lucide="message-square" class="w-4 h-4 text-blue-500"></i>
                            <span class="font-medium text-slate-900 text-sm">Update Message (What's New)</span>
                        @elseif($config['key'] === 'announcement')
                            <i data-lucide="megaphone" class="w-4 h-4 text-purple-500"></i>
                            <span class="font-medium text-slate-900 text-sm">Global Announcement Banner</span>
                        @elseif($config['key'] === 'download_url')
                            <i data-lucide="link" class="w-4 h-4 text-primary-500"></i>
                            <span class="font-medium text-slate-900 text-sm">Download URL</span>
                        @endif
                        <span class="ml-auto text-xs font-mono text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $config['key'] }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.pixora-config.set-value', $config['key']) }}">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3 items-start">
                            <div class="flex-grow w-full">
                                @if(in_array($config['key'], ['update_message', 'announcement']))
                                    <textarea name="value" rows="4" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors shadow-sm">{{ $config['value'] ?? '' }}</textarea>
                                @else
                                    <input type="text" name="value" value="{{ $config['value'] ?? '' }}" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors shadow-sm">
                                @endif
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors sm:w-auto w-full">
                                <i data-lucide="save" class="w-4 h-4 sm:mr-2"></i> <span class="sm:inline hidden">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Right: Feature Toggles ── --}}
    <div class="xl:col-span-4 space-y-6">
        <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden sticky top-6">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <i data-lucide="toggle-right" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-lg font-display font-semibold text-slate-900">Feature Toggles</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Aktifkan/nonaktifkan fitur secara remote</p>
                </div>
            </div>
            
            <div class="divide-y divide-slate-100">
                @foreach($toggleConfigs as $config)
                <div class="p-5 flex items-center justify-between hover:bg-slate-50/50 transition-colors group">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if($config['key'] === 'feature_boost')
                                <i data-lucide="rocket" class="w-4 h-4 text-orange-500"></i>
                                <span class="font-medium text-slate-900 text-sm">Upload Boost</span>
                            @elseif($config['key'] === 'feature_prepare')
                                <i data-lucide="film" class="w-4 h-4 text-blue-500"></i>
                                <span class="font-medium text-slate-900 text-sm">Prepare Video</span>
                            @elseif($config['key'] === 'feature_download')
                                <i data-lucide="download" class="w-4 h-4 text-emerald-500"></i>
                                <span class="font-medium text-slate-900 text-sm">Download TikTok</span>
                            @else
                                <i data-lucide="settings-2" class="w-4 h-4 text-slate-500"></i>
                                <span class="font-medium text-slate-900 text-sm">{{ Str::title(str_replace('_', ' ', $config['key'])) }}</span>
                            @endif
                        </div>
                        <span class="text-xs font-mono text-slate-400">{{ $config['key'] }}</span>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.pixora-config.toggle', $config['key']) }}">
                        @csrf
                        <button type="submit" 
                                class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $config['enabled'] ? 'bg-emerald-500' : 'bg-slate-200 hover:bg-slate-300' }}"
                                onclick="return confirm('{{ $config['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }} fitur ini?')">
                            <span class="sr-only">Toggle feature</span>
                            <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $config['enabled'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection