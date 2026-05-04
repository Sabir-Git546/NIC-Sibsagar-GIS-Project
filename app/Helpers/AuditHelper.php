<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

if (!function_exists('audit_log')) {

    function audit_log($action, $module, $recordid, $oldData = null, $newData = null)
    {
        \DB::table('audit_logs')->insert([
            'userid' => session('userid'),
            'action' => $action,
            'module' => $module,
            'recordid' => $recordid,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now()
        ]);
    }
}