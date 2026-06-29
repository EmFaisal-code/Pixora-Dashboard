<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting
        $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Log suspicious activity
            \Log::warning('Too many login attempts for email: ' . $request->email . ' from IP: ' . $request->ip());
            
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Clear rate limiter on successful login
            RateLimiter::clear($key);
            
            // Log successful login
            \Log::info('Successful login for user: ' . $request->email . ' from IP: ' . $request->ip());
            
            return redirect()->intended(route('admin.dashboard'));
        }

        // Increment rate limiter on failed login
        RateLimiter::hit($key, 300); // 5 minutes lockout
        
        // Log failed login attempt
        \Log::warning('Failed login attempt for email: ' . $request->email . ' from IP: ' . $request->ip());

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout
        if ($user) {
            \Log::info('User logged out: ' . $user->email . ' from IP: ' . $request->ip());
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('status', 'You have been logged out successfully.');
    }
}
