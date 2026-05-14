<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'users.userid', '=', 'audit_logs.userid')
            ->select('audit_logs.*', 'users.username')
            ->orderByDesc('audit_logs.created_at');

        if ($request->filled('user')) {
            $query->where('audit_logs.userid', trim($request->user));
        }

        if ($request->filled('module')) {
            $query->where('audit_logs.module', trim($request->module));
        }

        if ($request->filled('action')) {
            $query->where('audit_logs.action', trim($request->action));
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('audit_logs.created_at', [
                $request->from,
                $request->to
            ]);
        }

        return view('admin.audit_logs', [
            'logs' => $query->paginate(50)
        ]);
    }
}