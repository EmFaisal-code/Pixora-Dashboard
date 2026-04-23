<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PixoraUserController;
use App\Http\Controllers\Admin\PixoraConfigController;
use App\Http\Controllers\Admin\PixoraVersionController;

// Root → redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes (protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Settings
    Route::get('/settings/password', [SettingsController::class, 'showPasswordForm'])->name('settings.password');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    // Pixora Users
    Route::get('/pixora-users', [PixoraUserController::class, 'index'])->name('pixora-users');
    Route::post('/pixora-users/{username}/reset-password', [PixoraUserController::class, 'resetPassword'])->name('pixora-users.reset-password');
    Route::post('/pixora-users/{username}/toggle-ban', [PixoraUserController::class, 'toggleBan'])->name('pixora-users.toggle-ban');
    Route::delete('/pixora-users/{username}', [PixoraUserController::class, 'destroy'])->name('pixora-users.delete');

    // Pixora Versions
    Route::get('/pixora-versions', [PixoraVersionController::class, 'index'])->name('pixora-versions');
    Route::post('/pixora-versions', [PixoraVersionController::class, 'store'])->name('pixora-versions.store');
    Route::post('/pixora-versions/{version}/toggle', [PixoraVersionController::class, 'toggle'])->name('pixora-versions.toggle');
    Route::delete('/pixora-versions/{version}', [PixoraVersionController::class, 'destroy'])->name('pixora-versions.destroy');

    // Pixora Config
    Route::get('/pixora-config', [PixoraConfigController::class, 'index'])->name('pixora-config');
    Route::post('/pixora-config/{key}/toggle', [PixoraConfigController::class, 'toggle'])->name('pixora-config.toggle');
    Route::post('/pixora-config/set-version', [PixoraConfigController::class, 'setVersion'])->name('pixora-config.set-version');
});
