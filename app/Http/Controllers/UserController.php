<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //show user table view
    public function index()
    {
        // eager load department and role to reduce queries
        $users = UserModel::with(['department', 'role'])->get();

        return view('users.viewUser', compact('users'));
    }

    //show addUser form
    public function create()
    {
        $depts = Department::all();
        $roles = Role::all();

        return view('users.addUser', compact('depts', 'roles'));
    }

    // store departments
    public function store(Request $request)
    {
        $request->validate([
            'userid'      => 'required|unique:users,userid',
            'username'    => 'required|max:100',
            'userpass'    => 'required|min:6',
            'useremail'   => 'required|email|unique:users,useremail',
            'useraddress' => 'required',
            'userphno'    => 'required',
            'deptid'      => 'required|exists:departments,deptid',
            'roleid'      => 'required|exists:roles,roleid',
        ]);

        UserModel::create([
            'userid'      => $request->userid,
            'username'    => $request->username,
            'userpass'    => $request->userpass,
            'useremail'   => $request->useremail,
            'useraddress' => $request->useraddress,
            'userphno'    => $request->userphno,
            'deptid'      => $request->deptid,
            'roleid'      => $request->roleid,
        ]);

        return redirect()->back()->with('success', 'User Added Successfully!');
    }

    public function edit($userid)
    {
        $user = UserModel::findOrFail($userid);
        $depts = Department::all();
        $roles = Role::all();

        return view('users.editUser', compact('user', 'depts', 'roles'));
    }

    public function update(Request $request, $userid)
    {
        $request->validate([
            'username' => 'required|max:100',
            'useremail' => "required|email|unique:users,useremail,{$userid},userid",
            'useraddress' => 'required',
            'userphno' => 'required',
            'deptid' => 'required|exists:departments,deptid',
            'roleid' => 'required|exists:roles,roleid',
        ]);

        $user = UserModel::findOrFail($userid);

        $user->update([
            'username' => $request->username,
            'useremail' => $request->useremail,
            'useraddress' => $request->useraddress,
            'userphno' => $request->userphno,
            'deptid' => $request->deptid,
            'roleid' => $request->roleid,
        ]);

        return redirect()->route('user.index')->with('success', 'User Updated Successfully!');
    }

    public function destroy($userid)
    {
        // Find user by primary key or fail
        $user = UserModel::findOrFail($userid);

        // Delete user
        $user->delete();

        // Redirect back to user list with success message
        return redirect()->route('user.index')->with('success', 'User deleted successfully!');
    }
}