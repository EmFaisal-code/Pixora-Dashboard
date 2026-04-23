@extends('admin-panel.layout')
@section('title', 'Feature Config')
@section('page-title', 'Feature Config')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Feature Config</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h mr-2"></i>Remote Feature Toggle</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>
                @endif

                @foreach($configs as $config)
                @if($config['key'] === 'min_version')
                <div class="py-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <div class="font-weight-bold">🔄 Force Update — Minimum Version</div>
                            <small class="text-muted">User dengan versi <strong>lebih rendah</strong> dari ini akan dipaksa update dan tidak bisa menggunakan extension.</small>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.pixora-config.set-version') }}" class="d-flex align-items-center" style="gap:8px;">
                        @csrf
                        <div class="input-group" style="max-width:260px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Min. Version</span>
                            </div>
                            <input type="text" name="version" value="{{ $config['value'] ?? '1.0.0' }}"
                                class="form-control" placeholder="e.g. 1.2.0">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Set minimum version ke {{ $config['value'] ?? '1.0.0' }}? User dengan versi lebih lama tidak bisa menggunakan extension.')">
                                    <i class="fas fa-save mr-1"></i> Set
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Versi saat ini di extension: <strong>v1.1.1</strong></small>
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
