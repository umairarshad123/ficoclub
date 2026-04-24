<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Single-admin authentication.
 *
 * Credentials live in .env:
 *   ADMIN_EMAIL="karina@example.com"
 *   ADMIN_PASSWORD_HASH='$2y$12$...'   // bcrypt hash, generate once and paste
 *
 * To generate the hash once, run in tinker:
 *   echo Hash::make('your-password');
 */
class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (request()->session()->get('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limit: 5 attempts per minute per IP.
        $key = 'admin-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $expectedEmail    = 'anthony@850ficoclub.com';
        $expectedPassword = 'Anthony@123isd83';
        
        $emailMatches    = strtolower($expectedEmail) === strtolower($validated['email']);
        $passwordMatches = $expectedPassword === $validated['password'];

        if (! $emailMatches || ! $passwordMatches) {
            RateLimiter::hit($key, 60);
            Log::warning('Admin login failed', [
                'ip'    => $request->ip(),
                'email' => $validated['email'],
            ]);
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);
        $request->session()->put('admin_email', $expectedEmail);

        Log::info('Admin logged in', ['ip' => $request->ip(), 'email' => $expectedEmail]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['is_admin', 'admin_email']);
        $request->session()->regenerate();

        return redirect()->route('admin.login.show');
    }
}
