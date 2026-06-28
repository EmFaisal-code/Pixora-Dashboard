@extends('admin-panel.layout')
@section('title', 'Feature Config')
@section('page-title', 'Feature Config')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Feature Config</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h mr-2"></i>Remote Feature Toggle & Config</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>
                @endif

                @foreach($configs as $config)
                
                @if(in_array($config['key'], ['min_version', 'latest_version', 'update_message', 'announcement', 'download_url']))
                <div class="py-3 border-bottom">
                    <div class="font-weight-bold mb-2">
                        @if($config['key'] === 'min_version') 🛑 Force Update — Minimum Version
                        @elseif($config['key'] === 'latest_version') 🆕 Latest Version (Optional Update)
                        @elseif($config['key'] === 'update_message') 📝 Update Message (What's New)
                        @elseif($config['key'] === 'announcement') 📢 Global Announcement Banner
                        @elseif($config['key'] === 'download_url') 🔗 Download URL
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.pixora-config.set-value', $config['key']) }}" class="d-flex align-items-center" style="gap:8px;">
                        @csrf
                        <div class="input-group" style="width:100%;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ $config['key'] }}</span>
                            </div>
                            @if(in_array($config['key'], ['update_message', 'announcement']))
                                <textarea name="value" class="form-control" rows="2">{{ $config['value'] ?? '' }}</textarea>
                            @else
                                <input type="text" name="value" value="{{ $config['value'] ?? '' }}" class="form-control">
                            @endif
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                @else
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div>
                        <div class="font-weight-bold">
                            @if($config['key'] === 'feature_boost') 🚀 Upload Boost
                            @elseif($config['key'] === 'feature_prepare') 🎬 Prepare Video
                            @elseif($config['key'] === 'feature_download') ⬇️ Download TikTok
                            @else {{ $config['key'] }}
                            @endif
                        </div>
                        <small class="text-muted">{{ $config['key'] }}</small>
                    </div>
                    <form method="POST" action="{{ route('admin.pixora-config.toggle', $config['key']) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $config['enabled'] ? 'btn-success' : 'btn-secondary' }}"
                            onclick="return confirm('{{ $config['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }} fitur ini?')">
                            {{ $config['enabled'] ? '✅ Aktif' : '❌ Nonaktif' }}
                        </button>
                    </form>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection