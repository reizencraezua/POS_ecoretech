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
        return view('admin.Auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists and is admin/cashier role
        $user = \App\Models\User::where('email', $request->email)
            ->where('is_active', true)
            ->whereIn('role', ['admin', 'super_admin', 'cashier'])
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            Auth::guard('admin')->login($user, $request->boolean('remember'));
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
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
