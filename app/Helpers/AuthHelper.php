<?php

use App\Services\AuditService;

if (!function_exists('audit_log')) {

    function audit_log($action, $module, $recordid = null, $oldData = null, $newData = null)
    {
        try {

            AuditService::log(
                $action,
                $module,
                null,        // description not used anymore
                $oldData,
                $newData,
                $recordid
            );

        } catch (\Exception $e) {
            \Log::error("Audit Helper Failed: " . $e->getMessage());
        }
    }
}