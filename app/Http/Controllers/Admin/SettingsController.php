<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show the password change form.
     */
    public function showPasswordForm()
    {
        return view('admin-panel.settings.password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        // Update password with proper hashing
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Log the password change for security audit
        \Log::info('Password changed for user: ' . $user->email . ' at ' . now());

        return redirect()->route('admin.settings.password')
            ->with('success', 'Password updated successfully! Please use your new password for future logins.');
    }
}