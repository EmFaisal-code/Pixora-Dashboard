@extends('admin-panel.layout')
@section('title', 'Version Manager')
@section('page-title', 'Version Manager')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Version Manager</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-plus mr-2"></i>Tambah Versi</h3></div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.pixora-versions.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Nomor Versi</label>
                        <input type="text" name="version" class="form-control" placeholder="e.g. 1.2.0" required>
                        <small class="text-muted">Format: major.minor.patch</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allowedCheck" name="allowed" value="1" checked>
                            <label class="custom-control-label" for="allowedCheck">Izinkan versi ini</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-plus mr-1"></i> Tambah Versi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-code-branch mr-2"></i>Daftar Versi</h3>
                <div class="card-tools">
                    <small class="text-muted">🟢 Allowed = user bisa pakai &nbsp;|&nbsp; 🔴 Blocked = user harus update</small>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Versi</th>
                            <th>Status</th>
                            <th>Ditambahkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($versions as $v)
                        <tr>
                            <td><strong>v{{ $v['version'] }}</strong></td>
                            <td>
                                @if($v['allowed'])
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Allowed</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-ban mr-1"></i>Blocked</span>
                                @endif
                            </td>
                            <td><small>{{ \Carbon\Carbon::parse($v['created_at'])->format('d M Y') }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.pixora-versions.toggle', $v['version']) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $v['allowed'] ? 'btn-danger' : 'btn-success' }}"
                                        onclick="return confirm('{{ $v['allowed'] ? 'Block' : 'Allow' }} versi {{ $v['version'] }}?')">
                                        <i class="fas {{ $v['allowed'] ? 'fa-ban' : 'fa-check' }} mr-1"></i>
                                        {{ $v['allowed'] ? 'Block' : 'Allow' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.pixora-versions.destroy', $v['version']) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Hapus versi {{ $v['version'] }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada versi terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
