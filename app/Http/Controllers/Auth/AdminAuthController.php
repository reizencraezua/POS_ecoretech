<?php

// app/Http/Controllers/Auth/AdminAuthController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        // If user is already authenticated, redirect to appropriate dashboard
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($user->isCashier()) {
                return redirect()->route('cashier.dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists and is admin or cashier role
        $user = \App\Models\User::where('email', $request->email)
            ->where('is_active', true)
            ->whereIn('role', ['admin', 'super_admin', 'cashier'])
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            // Use the web guard for all authentication
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Redirect based on user role
            if ($user->isCashier()) {
                return redirect()->intended(route('cashier.dashboard'));
            } else {
                return redirect()->intended(route('admin.dashboard'));
            }
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Use the web guard for logout
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page
        return redirect()->route('login');
    }
}
