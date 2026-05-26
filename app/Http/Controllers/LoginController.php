<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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