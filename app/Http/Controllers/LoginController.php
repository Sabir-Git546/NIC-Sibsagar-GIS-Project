<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Models\UserModel;

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

        return view('login');
    }


    // =========================
    // LOGIN PROCESS
    // =========================
    public function login(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'userid'  => 'required|string|max:50',

            'password' => 'required|string|min:6',

            'g-recaptcha-response' => 'required'

        ]);


        /*
        |--------------------------------------------------------------------------
        | RATE LIMIT KEY
        |--------------------------------------------------------------------------
        */

        $throttleKey =
            Str::lower($request->userid)
            . '|' .
            $request->ip();


        /*
        |--------------------------------------------------------------------------
        | TOO MANY ATTEMPTS
        |--------------------------------------------------------------------------
        */

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {

            $seconds =
                RateLimiter::availableIn($throttleKey);

            return back()->with(

                'error',

                "Too many login attempts. Try again in {$seconds} seconds."

            );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY GOOGLE CAPTCHA
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | CAPTCHA FAILED
        |--------------------------------------------------------------------------
        */

        if (!$result['success']) {

            return back()

                ->withErrors([

                    'g-recaptcha-response' =>

                    'CAPTCHA verification failed.'

                ])

                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $user = UserModel::where(

            'userid',

            $request->userid

        )->first();


        /*
        |--------------------------------------------------------------------------
        | CHECK USER + PASSWORD
        |--------------------------------------------------------------------------
        */

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

            return back()->with(

                'error',

                'Invalid credentials'

            );
        }


        /*
        |--------------------------------------------------------------------------
        | CLEAR RATE LIMIT
        |--------------------------------------------------------------------------
        */

        RateLimiter::clear($throttleKey);


        /*
        |--------------------------------------------------------------------------
        | LOGIN USER
        |--------------------------------------------------------------------------
        */

        Auth::login($user);


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | REDIRECT TO MAIN DASHBOARD
        |--------------------------------------------------------------------------
        */

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