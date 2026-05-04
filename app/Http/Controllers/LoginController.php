<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\UserModel;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        $roles = Role::all();
        return view('login', compact('roles'));
    }

    // Login validation
    public function login(Request $request)
    {
        $request->validate([
            'userid'   => 'required',
            'userpass' => 'required',
            'roleid'   => 'required'
        ]);

        // STEP 1: find user by userid only
        $user = UserModel::where('userid', $request->userid)->first();

        if (!$user) {
            return back()->with('error', 'Invalid User ID');
        }

        // STEP 2: check role match
        if ($user->roleid != $request->roleid) {
            return back()->with('error', 'Invalid Role Selected');
        }

        // STEP 3: check password
        if ($request->userpass != $user->userpass) {
            return back()->with('error', 'Invalid Password');
        }

        // STEP 4: store session (IMPORTANT for RBAC)
        session([
            'userid'   => $user->userid,
            'roleid'   => $user->roleid,
            'username' => $user->username
        ]);

        // OPTIONAL DEBUG (remove later)
        // dd(session()->all());

        return redirect()->route('dashboard');
    }
}