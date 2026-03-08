<?php

namespace App\Http\Controllers;

use App\Models\Role;  
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $roles = Role::all();   //fetch roles
        //dd($roles->first());

        return view('login', compact('roles'));  
    }

    //function for login validation and redirect to dashboard
    public function login(Request $request)
    {
        $request->validate([
            'userid' => 'required',
            'userpass' => 'required',
            'roleid' => 'required'
        ]);

        $user = UserModel::where('userid', $request->userid)
                    ->where('roleid', $request->roleid)
                    ->first();

        if (!$user) {
            return back()->with('error', 'Invalid User ID or Role');
        }

        // Check password
        //if (!Hash::check($request->userpass, $user->userpass)) {
        if ($request->userpass != $user->userpass){
            return back()->with('error', 'Invalid Password');
        }

        // Store session
        session([
            'userid' => $user->userid,
            'roleid' => $user->roleid,
            'username'=> $user->username
        ]);

        // Redirect to single dashboard
        return redirect()->route('dashboard');
    }
}