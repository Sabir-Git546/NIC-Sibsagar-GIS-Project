<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\Department;
use App\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

use App\Services\AuditService;

class UserController extends Controller
{
    // user table in viewusers 
    public function index()
    {
        try {

            $users = UserModel::with([
                'department',
                'role'
            ])
                ->paginate(10)
                ->withQueryString();

            return view(
                'users.viewUser',
                compact('users')
            );

        } catch (\Exception $e) {

            Log::error('User Index Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load users.'
            );
        }
    }


    // add user form
    public function create()
    {
        try {

            $depts = Department::all();
            $roles = Role::all();

            return view(
                'users.addUser',
                compact('depts', 'roles')
            );

        } catch (\Exception $e) {

            Log::error('User Create Form Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load create user page.'
            );
        }
    }


    // store user in db
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'userid' => 'required|string|max:50|unique:users,userid',
                'username' => 'required|string|max:100',
                'userpass' => 'required|string|min:6',
                're_password' => 'required|same:userpass',
                'email' => 'required|email|unique:users,email',
                'deptid' => 'required|exists:departments,deptid',
                'roleid' => 'required|exists:roles,roleid',
            ]);

            DB::beginTransaction();

            // Duplicate userid prevention
            $useridExists = UserModel::where(
                'userid',
                trim($data['userid'])
            )->exists();

            if ($useridExists) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'User ID already exists.'
                );
            }

            // Duplicate email prevention
            $emailExists = UserModel::where(
                'email',
                trim($data['email'])
            )->exists();

            if ($emailExists) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Email already exists.'
                );
            }

            $user = UserModel::create([
                'userid' => trim($request->userid),
                'username' => trim($request->username),
                'password' => Hash::make($request->userpass),
                'email' => trim($request->email),
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

            DB::commit();

            return redirect()
                ->route('user.index')
                ->with(
                    'success',
                    'User added successfully!'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('User Store Database Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while creating user.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('User Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to create user.'
            );
        }
    }


    // edit user form
    public function edit($id)
    {
        try {

            $user = UserModel::findOrFail($id);

            $depts = Department::all();
            $roles = Role::all();

            return view(
                'users.editUser',
                compact('user', 'depts', 'roles')
            );

        } catch (\Exception $e) {

            Log::error('User Edit Error', [
                'message' => $e->getMessage(),
                'userid' => $id,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load edit user page.'
            );
        }
    }


    // update user details in db
    public function update(Request $request, $id)
    {
        try {

            DB::beginTransaction();

            $user = UserModel::findOrFail($id);

            $data = $request->validate([
                'username' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'deptid' => 'required|exists:departments,deptid',
                'roleid' => 'required|exists:roles,roleid',
            ]);

            // Prevent duplicate email
            $emailExists = UserModel::where(
                'email',
                trim($data['email'])
            )
                ->where('id', '!=', $id)
                ->exists();

            if ($emailExists) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Email already exists.'
                );
            }

            $old = $user->toArray();

            $user->update([
                'username' => trim($request->username),
                'email' => trim($request->email),
                'deptid' => $request->deptid,
                'roleid' => $request->roleid,
            ]);

            AuditService::log(
                'UPDATE',
                'USER',
                'User updated: ' . $user->userid,
                $old,
                $user->fresh()->toArray()
            );

            DB::commit();

            return redirect()
                ->route('user.index')
                ->with(
                    'success',
                    'User updated successfully!'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('User Update Database Error', [
                'message' => $e->getMessage(),
                'userid' => $id,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while updating user.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('User Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'userid' => $id,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to update user.'
            );
        }
    }


    // delete user record from db
    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $user = UserModel::findOrFail($id);

            // Prevent self delete
            if (auth()->id() == $user->id) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'You cannot delete your own account.'
                );
            }

            $oldData = $user->toArray();

            AuditService::log(
                'DELETE',
                'USER',
                'User deleted: ' . $user->userid,
                $oldData,
                null
            );

            $user->delete();

            DB::commit();

            return redirect()
                ->route('user.index')
                ->with(
                    'success',
                    'User deleted successfully!'
                );

        } catch (QueryException $e) {

            DB::rollBack();

            Log::error('User Delete Database Error', [
                'message' => $e->getMessage(),
                'userid' => $id,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Database error occurred while deleting user.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('User Delete Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'userid' => $id,
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to delete user.'
            );
        }
    }
}