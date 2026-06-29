@extends('admin-panel.layout.app')

@section('title', 'Change Password')
@section('page-title', 'Security Settings')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
    <li><i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i></li>
    <li class="text-slate-900 font-medium">Change Password</li>
@endsection

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    
    {{-- ── Left: Password Form ── --}}
    <div class="xl:col-span-7">
        <div class="bg-white border border-slate-200/60 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <i data-lucide="key-round" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-lg font-display font-semibold text-slate-900">Change Password</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui kata sandi Anda untuk menjaga keamanan akun</p>
                </div>
            </div>
            
            <form action="{{ route('admin.settings.password.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Current Password --}}
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-4 w-4 text-slate-400"></i>
                        </div>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border {{ $errors->has('current_password') ? 'border-red-300 ring-red-500/20' : 'border-slate-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 focus:bg-white transition-colors"
                               placeholder="Masukkan password saat ini"
                               required>
                    </div>
                    @error('current_password')
                        <p class="mt-1.5 text-sm text-red-500 flex items-start"><i data-lucide="alert-circle" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-slate-100 pt-6"></div>

                {{-- New Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="h-4 w-4 text-slate-400"></i>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border {{ $errors->has('password') ? 'border-red-300 ring-red-500/20' : 'border-slate-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 focus:bg-white transition-colors"
                                   placeholder="Masukkan password baru"
                                   required>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500 flex items-start"><i data-lucide="alert-circle" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="h-4 w-4 text-slate-400"></i>
                            </div>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 focus:bg-white transition-colors"
                                   placeholder="Ulangi password baru"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Password
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Right: Security Info ── --}}
    <div class="xl:col-span-5 space-y-6">
        
        {{-- Requirements --}}
        <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-lg border border-slate-800 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl -mt-10 -mr-10"></div>
            
            <div class="flex items-center gap-3 mb-5 relative z-10">
                <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-400">
                    <i data-lucide="shield" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-display font-semibold tracking-wide text-white">Password Requirements</h3>
            </div>
            
            <ul class="space-y-3 relative z-10">
                <li class="flex items-center text-sm text-slate-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0"></i>
                    <span>At least <strong>8 characters</strong> long</span>
                </li>
                <li class="flex items-center text-sm text-slate-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0"></i>
                    <span>Contains uppercase letter <strong>(A-Z)</strong></span>
                </li>
                <li class="flex items-center text-sm text-slate-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0"></i>
                    <span>Contains lowercase letter <strong>(a-z)</strong></span>
                </li>
                <li class="flex items-center text-sm text-slate-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0"></i>
                    <span>Contains number <strong>(0-9)</strong></span>
                </li>
                <li class="flex items-center text-sm text-slate-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0"></i>
                    <span>Contains special character <strong>(@$!%*?&)</strong></span>
                </li>
            </ul>
        </div>

        {{-- Notes --}}
        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 shadow-sm">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="text-sm font-semibold text-amber-800 mb-1">Security Note</h4>
                    <p class="text-sm text-amber-700/80 leading-relaxed">After changing your password, you will remain logged in on this device, but you'll need to use the new password for future logins on all devices.</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 shadow-sm">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Security Features</h4>
                    <ul class="space-y-1.5">
                        <li class="flex items-start text-sm text-blue-700/80">
                            <span class="mr-2">•</span> <span>Login attempts are rate-limited (5 attempts per 5 minutes)</span>
                        </li>
                        <li class="flex items-start text-sm text-blue-700/80">
                            <span class="mr-2">•</span> <span>All login activities are logged for security audit</span>
                        </li>
                        <li class="flex items-start text-sm text-blue-700/80">
                            <span class="mr-2">•</span> <span>Passwords are encrypted using bcrypt hashing</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection