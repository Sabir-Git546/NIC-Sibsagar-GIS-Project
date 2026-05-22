<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuditService
{
    public static function log($action, $module, $description = null, $oldData = null, $newData = null, $recordId = null)
    {
        try {

            DB::table('audit_logs')->insert([
                'userid' => session('userid') ?? auth()->user()->userid ?? null,
                'module' => $module,
                'action' => $action,
                'record_id' => $recordId,

                'old_data' => $oldData ? json_encode($oldData) : null,
                'new_data' => $newData ? json_encode($newData) : null,

                'ip_address' => request()->ip(),
                'created_at' => now()
            ]);

        } catch (\Exception $e) {
            \Log::error("Audit Log Failed: " . $e->getMessage());
        }
    }
}