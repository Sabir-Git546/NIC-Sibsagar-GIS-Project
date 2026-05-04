<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('audit_logs')
            ->orderBy('created_at', 'desc');

        // optional filters
        if ($request->user) {
            $query->where('userid', $request->user);
        }

        if ($request->module) {
            $query->where('module', $request->module);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $logs = $query->limit(100)->get();

        return view('admin.audit_logs', compact('logs'));
    }
}