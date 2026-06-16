<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use App\Models\UserModel;
use App\Models\OtpVerification;

use App\Services\OtpService;
use App\Services\AuditService;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }


   public function cancelLogin()
    {
        session()->forget([
            'login_step',
            'login_userid',
            'debug_otp'
        ]);

        return redirect()->route('login');
    }


    /* ======================================
        LOGIN STEP 1: CHECK CREDENTIALS
    ====================================== */
    public function login(Request $request)
    {
        $request->validate([
            'userid' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'g-recaptcha-response' => 'required'
        ]);

        $key = Str::lower($request->userid) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', 'Too many attempts.');
        }

        $user = UserModel::where('userid', $request->userid)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            return back()->with('error', 'Invalid credentials.');
        }

        RateLimiter::clear($key);

        // GENERATE OTP + CAPTURE IT
        $otp = app(OtpService::class)->generate(
            $user->userid,
            OtpService::LOGIN
        );

        // STORE SESSION FOR UI CONTROL
        session([
            'login_userid' => $user->userid,
            'login_step'   => 'otp',
            'debug_otp'    => $otp   // DEV ONLY
        ]);

        return back()->with(
            'success',
            "Generated OTP: {$otp}"
        );
    }


    /* ======================================
        LOGIN STEP 2: VERIFY OTP
    ====================================== */
    public function loginVerifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $userid = session('login_userid');

        // Verify login session
        if (
            !$userid ||
            session('login_step') !== 'otp'
        ) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Session expired. Please login again.'
                );
        }

        // Get valid OTP
        $otpRecord = OtpVerification::where(
                'userid',
                $userid
            )
            ->where(
                'purpose',
                OtpService::LOGIN
            )
            ->where(
                'is_verified',
                false
            )
            ->where(
                'expires_at',
                '>=',
                now()
            )
            ->latest()
            ->first();


        // Validate OTP
        if (
            !$otpRecord ||
            !Hash::check(
                $request->otp,
                $otpRecord->otp_hash
            )
        ) {
            return back()->with(
                'error',
                'Invalid or expired OTP.'
            );
        }

        // Mark OTP as used
        $otpRecord->update([
            'is_verified' => true
        ]);


        // Get user
        $user = UserModel::where(
            'userid',
            $userid
        )->first();

    
        if (!$user) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'User account not found.'
                );
        }

        // Login user
        Auth::login($user);

    
        // Regenerate session
        $request->session()->regenerate();

        // Cleanup login session
        session()->forget([
            'login_userid',
            'login_step',
            'debug_otp'
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Login successful.'
            );
    }


    //-----------------------------------
    // FORGET PASSWORD FORM
    //-----------------------------------
    public function showForgotPasswordForm()
    {
        /*session()->forget([
            'auth_step',
            'auth_user'
        ]);*/

        return view('forgot-password');
    }


    //
    public function cancelForgotPassword()
    {
        session()->forget([
            'auth_step',
            'auth_user'
        ]);

        return redirect()->route('password.forgot');
    }


    /* ======================================
        PASSWORD RESET STEP 1: SEND OTP
    ====================================== */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'userid' => 'required|string|max:50'
        ]);

        $user = UserModel::where(
            'userid',
            $request->userid
        )->first();

        if (!$user) {

            return back()->with(
                'error',
                'User not found.'
            );
        }

        $key = 'reset-otp|' . Str::lower(
            $request->userid
        );

        if (RateLimiter::tooManyAttempts($key, 3)) {

            return back()->with(
                'error',
                'Too many OTP requests. Please try again after 5 minutes.'
            );
        }

        // Generate OTP
        $otp = app(OtpService::class)->generate(
            $user->userid,
            OtpService::PASSWORD_RESET
        );

        // Apply rate limiting
        RateLimiter::hit(
            $key,
            300
        );

        // Store reset session state
        session([
            'auth_step' => 'reset_otp',
            'auth_user' => $user->userid
        ]);

        //test
        //dd(session()->all());

        // Development mode
        return back()->with(
            'success',
            "OTP generated successfully. OTP: {$otp}"
        );

        /*
        // Production mode
        return back()->with(
            'success',
            'OTP sent successfully.'
        );
        */
    }

    /* ======================================
        PASSWORD RESET STEP 2: VERIFY OTP
    ====================================== */
    public function verifyOtp(Request $request)
    {

        try {

            $request->validate([
                'otp' => 'required|digits:6'
            ]);

            $userid = session('auth_user');

            // Verify reset session exists
            if (
                !$userid ||
                session('auth_step') !== 'reset_otp'
            ) {

                return redirect()
                    ->route('password.forgot')
                    ->with(
                        'error',
                        'Session expired. Please generate OTP again.'
                    );
            }

            // Get latest valid OTP
            $otpRecord = OtpVerification::where(
                    'userid',
                    $userid
                )
                ->where(
                    'purpose',
                    OtpService::PASSWORD_RESET
                )
                ->where(
                    'is_verified',
                    false
                )
                ->where(
                    'expires_at',
                    '>=',
                    now()
                )
                ->latest()
                ->first();

            // Verify OTP
            if (
                !$otpRecord ||
                !Hash::check(
                    $request->otp,
                    $otpRecord->otp_hash
                )
            ) {

                return back()->with(
                    'error',
                    'Invalid or expired OTP.'
                );
            }

            // Mark OTP as verified
            $otpRecord->update([
                'is_verified' => true,
            ]);

            // Move to password reset step
            session([
                'auth_step' => 'reset_password'
            ]);

            return back()->with(
                'success',
                'OTP verified successfully. You may now reset your password.'
            );

        } catch (\Exception $e) {

            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }

        
    }

    /* ======================================
        PASSWORD RESET STEP 3
    ====================================== */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $userid = session('auth_user');

        if (!$userid || session('auth_step') !== 'reset_password') {
            return redirect()
                ->route('password.forgot')
                ->with('error', 'OTP verification required.');
        }

        $user = UserModel::where('userid', $userid)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // cleanup
        OtpVerification::where('userid', $userid)->delete();

        session()->forget(['auth_step', 'auth_user']);

        return redirect()->route('login')
            ->with('success', 'Password reset successful.');
    }

}