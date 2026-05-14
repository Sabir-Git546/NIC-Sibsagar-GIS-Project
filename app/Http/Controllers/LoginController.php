<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\UserModel;
use App\Models\Role;

class LoginController extends Controller
{
    // =========================
    // SHOW LOGIN FORM
    // =========================
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $roles = Role::all();

        return view('login', compact('roles'));
    }

    // =========================
    // LOGIN PROCESS
    // =========================
    public function login(Request $request)
    {
        // VALIDATION
        $request->validate([
            'userid'  => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'roleid'  => 'required|integer'
        ]);

        // RATE LIMIT KEY
        $throttleKey = Str::lower($request->userid) . '|' . $request->ip();

        // CHECK TOO MANY ATTEMPTS
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {

            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->with(
                'error',
                "Too many login attempts. Try again in {$seconds} seconds."
            );
        }

        // FIND USER
        $user = UserModel::where('userid', $request->userid)->first();

        // CHECK USER + ROLE + PASSWORD
        if (
            !$user ||
            $user->roleid != $request->roleid ||
            !Hash::check($request->password, $user->password)
        ) {

            RateLimiter::hit($throttleKey, 60);

            return back()->with('error', 'Invalid credentials');
        }

        // CLEAR RATE LIMIT
        RateLimiter::clear($throttleKey);

        // LOGIN USER
        Auth::login($user);

        // REGENERATE SESSION
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}