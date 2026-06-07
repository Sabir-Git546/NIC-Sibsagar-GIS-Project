<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PasswordReset;

use Illuminate\Support\Str;

use App\Models\UserModel;
use App\Services\AuditService;

class LoginController extends Controller
{
    // show login form
    public function showLoginForm()
    {
        try {

            if (Auth::check()) {

                return redirect()->route('dashboard');
            }

            return view('login');

        } catch (\Exception $e) {

            Log::error('Login form load failed', [

                'message' => $e->getMessage(),

                'ip' => request()->ip()

            ]);

            return back()->with(
                'error',
                'Unable to load login page.'
            );
        }
    }


    // login processing func
    public function login(Request $request)
    {
        try {

            // validate login details
            $request->validate([

                'userid' => 'required|string|max:50',

                'password' => 'required|string|min:6',

                'g-recaptcha-response' => 'required'

            ]);


            // limit login attempt to 5 times 
            $throttleKey =

                Str::lower($request->userid)

                . '|'

                . $request->ip();


            // 
            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {

                $seconds =
                    RateLimiter::availableIn($throttleKey);

                Log::warning('Too many login attempts', [

                    'userid' => $request->userid,

                    'ip' => $request->ip()

                ]);

                return back()->with(

                    'error',

                    "Too many login attempts. Try again in {$seconds} seconds."

                );
            }


            // verify google captcha
            $response = Http::asForm()->post(

                'https://www.google.com/recaptcha/api/siteverify',

                [

                    'secret' =>
                        env('RECAPTCHA_SECRET_KEY'),

                    'response' =>
                        $request->input('g-recaptcha-response'),

                    'remoteip' =>
                        $request->ip(),

                ]
            );

            $result = $response->json();


            // captcha fail
            if (!isset($result['success']) || !$result['success']) {

                Log::warning('CAPTCHA verification failed', [

                    'userid' => $request->userid,

                    'ip' => $request->ip()

                ]);

                return back()

                    ->withErrors([

                        'g-recaptcha-response' =>

                        'CAPTCHA verification failed.'

                    ])

                    ->withInput();
            }


            // find user in db
            $user = UserModel::where(

                'userid',

                $request->userid

            )->first();


            // check user id password
            if (

                !$user ||

                !Hash::check(

                    $request->password,

                    $user->password

                )

            ) {

                RateLimiter::hit(

                    $throttleKey,

                    60

                );

                // security log
                Log::warning('Failed login attempt', [

                    'userid' => $request->userid,

                    'ip' => $request->ip()

                ]);


                // audit log
                AuditService::log(

                    'LOGIN_FAILED',

                    'AUTH',

                    'Failed login attempt',

                    null,

                    [

                        'userid' => $request->userid,

                        'ip' => $request->ip()

                    ]

                );

                return back()->with(

                    'error',

                    'Invalid credentials.'

                );
            }


            // clear rate limit
            RateLimiter::clear($throttleKey);


            // login user
            Auth::login($user);


            //  regenerate session
            $request->session()->regenerate();


            //  security log
            Log::info('User login successful', [

                'userid' => $user->userid,

                'ip' => $request->ip()

            ]);


            //  audit log
            AuditService::log(

                'LOGIN_SUCCESS',

                'AUTH',

                'User logged in successfully',

                null,

                [

                    'userid' => $user->userid,

                    'ip' => $request->ip()

                ]

            );


            //  redirect user
            return redirect()

                ->route('dashboard')

                ->with(

                    'success',

                    'Login successful.'

                );

        } catch (\Exception $e) {

            Log::error('Login error', [

                'message' => $e->getMessage(),

                'trace' => $e->getTraceAsString(),

                'userid' => $request->userid ?? null,

                'ip' => $request->ip()

            ]);

            return back()->with(

                'error',

                'Login service temporarily unavailable.'

            );
        }
    }

    public function showForgotPasswordForm()
    {
        try {

            return view('forgot-password');

        } catch (\Exception $e) {

            Log::error('Forgot password page load failed', [

                'message' => $e->getMessage(),
                'ip' => request()->ip()

            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Unable to load page.');
        }
    }

    public function sendOtp(Request $request)
    {
        try {

            $request->validate([

                'userid' => 'required|string|max:50'

            ]);


            /*
            |--------------------------------------------------------------------------
            | OTP RATE LIMITING
            |--------------------------------------------------------------------------
            */
            $throttleKey =

                'password-reset|'

                . Str::lower($request->userid)

                . '|'

                . $request->ip();


            if (RateLimiter::tooManyAttempts($throttleKey, 3)) {

                $seconds =
                    RateLimiter::availableIn($throttleKey);

                Log::warning('Too many OTP requests', [

                    'userid' => $request->userid,

                    'ip' => $request->ip()

                ]);

                return back()->with(

                    'error',

                    "Too many OTP requests. Try again in {$seconds} seconds."

                );
            }


            /*
            |--------------------------------------------------------------------------
            | VERIFY USER EXISTS
            |--------------------------------------------------------------------------
            */
            $user = UserModel::where(

                'userid',

                $request->userid

            )->first();


            if (!$user) {

                return back()->with(

                    'error',

                    'User ID not found.'

                );
            }


            /*
            |--------------------------------------------------------------------------
            | REMOVE OLD OTP RECORDS
            |--------------------------------------------------------------------------
            */
            PasswordReset::where(

                'userid',

                $request->userid

            )->delete();


            /*
            |--------------------------------------------------------------------------
            | GENERATE OTP
            |--------------------------------------------------------------------------
            */
            $otp = random_int(

                100000,

                999999

            );


            /*
            |--------------------------------------------------------------------------
            | STORE OTP
            |--------------------------------------------------------------------------
            */
            PasswordReset::create([

                'userid' => $request->userid,

                'otp_hash' => Hash::make($otp),

                'expires_at' => now()->addMinutes(5),

                'is_verified' => false

            ]);


            /*
            |--------------------------------------------------------------------------
            | RECORD OTP REQUEST
            |--------------------------------------------------------------------------
            */
            RateLimiter::hit(

                $throttleKey,

                300 // 5 minutes

            );


            /*
            |--------------------------------------------------------------------------
            | SECURITY LOG
            |--------------------------------------------------------------------------
            */
            Log::info('Password reset OTP generated', [

                'userid' => $request->userid,

                'ip' => $request->ip()

            ]);


            /*
            |--------------------------------------------------------------------------
            | AUDIT LOG
            |--------------------------------------------------------------------------
            */
            AuditService::log(

                'PASSWORD_RESET_REQUEST',

                'AUTH',

                'Password reset OTP generated',

                null,

                [

                    'userid' => $request->userid,

                    'ip' => $request->ip()

                ]

            );


            /*
            |--------------------------------------------------------------------------
            | DEVELOPMENT OTP DISPLAY
            |--------------------------------------------------------------------------
            */
            return back()

                ->with(

                    'success',

                    "Development OTP: {$otp}"

                )

                ->with(

                    'userid',

                    $request->userid

                );

        } catch (\Exception $e) {

            Log::error('OTP generation failed', [

                'message' => $e->getMessage(),

                'userid' => $request->userid ?? null,

                'ip' => $request->ip()

            ]);

            return back()->with(

                'error',

                'Unable to generate OTP.'

            );
        }
    }

    public function verifyOtp(Request $request)
    {
        try {

            $request->validate([

                'userid' => 'required|string',

                'otp' => 'required|digits:6'

            ]);

            $reset = PasswordReset::where(

                    'userid',

                    $request->userid

                )

                ->latest('id')

                ->first();


            if (!$reset) {

                return back()->with(

                    'error',

                    'OTP not found.'

                );
            }


            /*
            |--------------------------------------------------------------------------
            | OTP ALREADY USED
            |--------------------------------------------------------------------------
            */
            if ($reset->is_verified) {

                return back()->with(

                    'error',

                    'OTP already verified.'

                );
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK OTP EXPIRY
            |--------------------------------------------------------------------------
            */
            if (now()->gt($reset->expires_at)) {

                AuditService::log(

                    'PASSWORD_RESET_FAILED',

                    'AUTH',

                    'OTP expired',

                    null,

                    [

                        'userid' => $request->userid,

                        'ip' => $request->ip()

                    ]

                );

                return back()->with(

                    'error',

                    'OTP has expired.'

                );
            }


            /*
            |--------------------------------------------------------------------------
            | VERIFY OTP
            |--------------------------------------------------------------------------
            */
            if (!Hash::check(

                $request->otp,

                $reset->otp_hash

            )) {

                AuditService::log(

                    'PASSWORD_RESET_FAILED',

                    'AUTH',

                    'Invalid OTP entered',

                    null,

                    [

                        'userid' => $request->userid,

                        'ip' => $request->ip()

                    ]

                );

                return back()->with(

                    'error',

                    'Invalid OTP.'

                );
            }


            /*
            |--------------------------------------------------------------------------
            | MARK OTP AS VERIFIED
            |--------------------------------------------------------------------------
            */
            $reset->is_verified = true;

            $reset->save();


            /*
            |--------------------------------------------------------------------------
            | SECURITY LOG
            |--------------------------------------------------------------------------
            */
            Log::info('Password reset OTP verified', [

                'userid' => $request->userid,

                'ip' => $request->ip()

            ]);


            /*
            |--------------------------------------------------------------------------
            | AUDIT LOG
            |--------------------------------------------------------------------------
            */
            AuditService::log(

                'PASSWORD_RESET_OTP_VERIFIED',

                'AUTH',

                'OTP verified successfully',

                null,

                [

                    'userid' => $request->userid,

                    'ip' => $request->ip()

                ]

            );


            return back()

                ->with(

                    'otp_verified',

                    true

                )

                ->with(

                    'userid',

                    $request->userid

                )

                ->with(

                    'success',

                    'OTP verified successfully.'

                );

        } catch (\Exception $e) {

            Log::error('OTP verification failed', [

                'message' => $e->getMessage(),

                'userid' => $request->userid ?? null,

                'ip' => $request->ip()

            ]);

            return back()->with(

                'error',

                'OTP verification failed.'

            );
        }
    }

    public function resetPassword(Request $request)
    {
        try {

            $request->validate([

                'userid' => 'required|string',

                'password' => 'required|string|min:6|confirmed'

            ]);

            $reset = PasswordReset::where(
                    'userid',
                    $request->userid
                )
                ->latest('id')
                ->first();

            if (!$reset) {

                return redirect()
                    ->route('password.forgot')
                    ->with(
                        'error',
                        'Password reset session not found.'
                    );
            }

            if (!$reset->is_verified) {

                return redirect()
                    ->route('password.forgot')
                    ->with(
                        'error',
                        'OTP verification required.'
                    );
            }

            $user = UserModel::where(
                'userid',
                $request->userid
            )->first();

            if (!$user) {

                return redirect()
                    ->route('password.forgot')
                    ->with(
                        'error',
                        'User not found.'
                    );
            }

            $user->password =
                Hash::make($request->password);

            $user->save();

            // Remove OTP record
            $reset->delete();

            Log::info('Password reset successful', [

                'userid' => $user->userid,

                'ip' => $request->ip()

            ]);

            AuditService::log(

                'PASSWORD_RESET_SUCCESS',

                'AUTH',

                'Password reset successful',

                null,

                [

                    'userid' => $user->userid,

                    'ip' => $request->ip()

                ]

            );

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Password reset successful. Please login.'
                );

        } catch (\Exception $e) {

            Log::error('Password reset failed', [

                'message' => $e->getMessage(),

                'userid' => $request->userid,

                'ip' => $request->ip()

            ]);

            return redirect()
                ->route('password.forgot')
                ->with(
                    'error',
                    'Password reset failed.'
                );
        }
    }

    // logout user
    public function logout(Request $request)
    {
        try {

            // AUDIT LOG
            AuditService::log(

                'LOGOUT',

                'AUTH',

                'User logged out',

                null,

                [

                    'userid' => auth()->user()?->userid,

                    'ip' => $request->ip()

                ]

            );


            // security log
            Log::info('User logged out', [

                'userid' => auth()->user()?->userid,

                'ip' => $request->ip()

            ]);


            //  logout user
            Auth::logout();


            //  invalid session
            $request->session()->invalidate();


            //  regenerate session
            $request->session()->regenerateToken();


            //  redirect
            return redirect()

                ->route('login')

                ->with(

                    'success',

                    'Logged out successfully.'

                );

        } catch (\Exception $e) {

            Log::error('Logout error', [

                'message' => $e->getMessage(),

                'trace' => $e->getTraceAsString(),

                'ip' => $request->ip()

            ]);

            return redirect()->route('login')->with(

                'error',

                'Logout failed.'

            );
        }
    }
}