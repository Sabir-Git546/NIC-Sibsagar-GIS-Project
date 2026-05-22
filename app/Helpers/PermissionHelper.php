<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('hasPermission')) {

    function hasPermission($permission)
    {
        $roleid = session('roleid');

        if (!$roleid) return false;

        return DB::table('role_permissions')
            ->join('permissions', 'permissions.permissionid', '=', 'role_permissions.permissionid')
            ->where('role_permissions.roleid', $roleid)
            ->whereRaw('LOWER(TRIM(permissions.permissionname)) = ?', [
                strtolower(trim($permission))
            ])
            ->exists();
    }
}