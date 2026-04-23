@extends('admin-panel.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

    <!-- Pixora Stats -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $pixoraStats['total'] }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('admin.pixora-users') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $pixoraStats['active'] }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
                <a href="{{ route('admin.pixora-users') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $pixoraStats['banned'] }}</h3>
                    <p>Banned Users</p>
                </div>
                <div class="icon"><i class="fas fa-ban"></i></div>
                <a href="{{ route('admin.pixora-users') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Registrasi Terbaru</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pixora-users') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-users mr-1"></i> Semua User
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($recentUsers) > 0)
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Username TikTok</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td><strong>{{ $user['username'] }}</strong></td>
                                <td>
                                    @if($user['is_banned'] ?? false)
                                        <span class="badge badge-danger">Banned</span>
                                    @else
                                        <span class="badge badge-success">Aktif</span>
                                    @endif
                                </td>
                                <td><small>{{ \Carbon\Carbon::parse($user['created_at'])->diffForHumans() }}</small></td>
                                <td>
                                    <a href="{{ route('admin.pixora-users') }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2"></i><br>
                        Belum ada user terdaftar.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
