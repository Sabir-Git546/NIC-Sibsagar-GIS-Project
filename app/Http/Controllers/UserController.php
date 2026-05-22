<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditService;

class UserController extends Controller
{
    // =========================
    // LIST USERS
    // =========================
    public function index()
    {
        $users = UserModel::with(['department', 'role'])
            ->paginate(10)

            ->withQueryString();

        return view('users.viewUser', compact('users'));
    }

    // =========================
    // CREATE FORM
    // =========================
    public function create()
    {
        $depts = Department::all();
        $roles = Role::all();

        return view('users.addUser', compact('depts', 'roles'));
    }

    // =========================
    // STORE USER
    // =========================
    public function store(Request $request)
    {

        $request->validate([
            'userid' => 'required|string|max:50|unique:users,userid',
            'username' => 'required|string|max:100',
            'userpass' => 'required|string|min:6',
            're_password' => 'required|same:userpass',
            'email' => 'required|email|unique:users,email',
            'deptid' => 'required|exists:departments,deptid',
            'roleid' => 'required|exists:roles,roleid',
        ]);

        $user = UserModel::create([
            'userid' => $request->userid,
            'username' => $request->username,
            'password' => Hash::make($request->userpass),
            'email' => $request->email,
            'deptid' => $request->deptid,
            'roleid' => $request->roleid,
        ]);

        AuditService::log(
            'CREATE',
            'USER',
            'User created: ' . $request->userid,
            null,
            $user->toArray()
        );

        return redirect()
            ->route('user.index')
            ->with('success', 'User Added Successfully!');
    }

    // =========================
    // EDIT USER
    // =========================
    public function edit($id)
    {
        $user = UserModel::findOrFail($id);

        $depts = Department::all();
        $roles = Role::all();

        return view('users.editUser', compact('user', 'depts', 'roles'));
    }

    // =========================
    // UPDATE USER
    // =========================
    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'deptid' => 'required|exists:departments,deptid',
            'roleid' => 'required|exists:roles,roleid',
        ]);

        $old = $user->toArray();

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'deptid' => $request->deptid,
            'roleid' => $request->roleid,
        ]);

        AuditService::log(
            'UPDATE',
            'USER',
            'User updated: ' . $user->userid,
            $old,
            $user->toArray()
        );

        return redirect()
            ->route('user.index')
            ->with('success', 'User Updated Successfully!');
    }

    // =========================
    // DELETE USER
    // =========================
    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);

        AuditService::log(
            'DELETE',
            'USER',
            'User deleted: ' . $user->userid,
            $user->toArray(),
            null
        );

        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'User deleted successfully!');
    }
}