@extends('admin-panel.layout')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item active">Change Password</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <form action="{{ route('admin.settings.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        @error('current_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <small class="form-text text-muted">
                            <strong>Password Requirements:</strong><br>
                            • At least 8 characters long<br>
                            • Must contain uppercase letter (A-Z)<br>
                            • Must contain lowercase letter (a-z)<br>
                            • Must contain number (0-9)<br>
                            • Must contain special character (@$!%*?&)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Password Requirements</h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> At least 8 characters long</li>
                    <li><i class="fas fa-check text-success"></i> Contains uppercase letter (A-Z)</li>
                    <li><i class="fas fa-check text-success"></i> Contains lowercase letter (a-z)</li>
                    <li><i class="fas fa-check text-success"></i> Contains number (0-9)</li>
                    <li><i class="fas fa-check text-success"></i> Contains special character (@$!%*?&)</li>
                    <li><i class="fas fa-check text-success"></i> Must be different from current password</li>
                    <li><i class="fas fa-check text-success"></i> Confirmation must match new password</li>
                </ul>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Security Note:</strong> After changing your password, you will remain logged in on this device, but you'll need to use the new password for future logins.
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-shield-alt"></i>
                    <strong>Security Features:</strong><br>
                    • Login attempts are rate-limited (5 attempts per 5 minutes)<br>
                    • All login activities are logged for security audit<br>
                    • Passwords are encrypted using bcrypt hashing
                </div>
            </div>
        </div>
    </div>
</div>
@endsection