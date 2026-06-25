@extends('admin-panel.layout')
@section('title', 'Pixora Users')
@section('page-title', 'Pixora Users')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pixora Users</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Daftar User Pixora</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ count($users) }} Users</span>
                    <span class="badge badge-info ml-1">Min. Version: v{{ $minVersion }}</span>
                </div>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                @if(count($users) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="usersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username TikTok</th>
                                <th>Status</th>
                                <th>Versi</th>
                                <th>Terakhir Aktif</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $i => $user)
                            @php
                                $versionUsed = $user['version_used'] ?? null;
                                $isOutdated = $versionUsed && version_compare($versionUsed, $minVersion, '<');
                            @endphp
                            <tr class="{{ ($user['is_banned'] ?? false) ? 'table-danger' : ($isOutdated ? 'table-warning' : '') }}">
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $user['username'] }}</strong>
                                    @if($user['reset_requested'] ?? false)
                                        <span class="badge badge-warning ml-1" title="User meminta reset password">
                                            <i class="fas fa-key"></i> Reset Request
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user['is_banned'] ?? false)
                                        <span class="badge badge-danger"><i class="fas fa-ban mr-1"></i>Banned</span>
                                    @else
                                        @if(isset($user['last_seen']) && \Carbon\Carbon::parse($user['last_seen'])->diffInMinutes() < 10)
                                            <span class="badge badge-success"><i class="fas fa-circle mr-1" style="font-size: 8px; vertical-align: middle;"></i>Online</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-circle mr-1" style="font-size: 8px; vertical-align: middle;"></i>Offline</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($versionUsed)
                                        @if($isOutdated)
                                            <span class="badge badge-warning" title="Perlu update ke v{{ $minVersion }}">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>v{{ $versionUsed }}
                                            </span>
                                        @else
                                            <span class="badge badge-success">v{{ $versionUsed }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user['last_seen'] ?? false)
                                        <small title="{{ \Carbon\Carbon::parse($user['last_seen'])->format('d M Y, H:i:s') }}">
                                            {{ \Carbon\Carbon::parse($user['last_seen'])->diffForHumans() }}
                                        </small>
                                    @else
                                        <small class="text-muted">Belum pernah</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($user['created_at'])->format('d M Y, H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-warning"
                                            onclick="openResetModal('{{ $user['username'] }}')" title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <form method="POST"
                                            action="{{ route('admin.pixora-users.toggle-ban', urlencode($user['username'])) }}"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ ($user['is_banned'] ?? false) ? 'btn-success' : 'btn-secondary' }}"
                                                title="{{ ($user['is_banned'] ?? false) ? 'Unban' : 'Ban' }}"
                                                onclick="return confirm('{{ ($user['is_banned'] ?? false) ? 'Unban' : 'Ban' }} user {{ $user['username'] }}?')">
                                                <i class="fas {{ ($user['is_banned'] ?? false) ? 'fa-unlock' : 'fa-ban' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.pixora-users.delete', urlencode($user['username'])) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus"
                                                onclick="return confirm('Hapus user {{ $user['username'] }}?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum ada user terdaftar</h4>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-key mr-2"></i>Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="resetForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">User: <strong id="resetUsername"></strong></p>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="text" name="password" class="form-control" placeholder="Min. 4 karakter" required minlength="4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openResetModal(username) {
    document.getElementById('resetUsername').innerText = username;
    document.getElementById('resetForm').action = '/admin/pixora-users/' + encodeURIComponent(username) + '/reset-password';
    $('#resetModal').modal('show');
}
$(document).ready(function() {
    $('#usersTable').DataTable({
        responsive: true,
        order: [[4, 'desc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ user",
            paginate: { previous: "Sebelumnya", next: "Berikutnya" }
        }
    });
});
</script>
@endpush
