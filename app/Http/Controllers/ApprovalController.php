<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class ApprovalController extends Controller
{
    // =========================
    // LIST ALL REQUESTS
    // =========================
    public function index()
    {
        $requests = DB::table('approval_requests')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.permission_approvals', compact('requests'));
    }

    // =========================
    // APPROVE REQUEST
    // =========================
    public function approve($requestid)
    {
        $req = DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->first();

        if (!$req) {
            return back()->with('error', 'Request not found');
        }

        // =========================
        // PROJECT MODULE
        // =========================
        if ($req->module === 'project') {

            // UPDATE APPROVAL
            if ($req->action === 'update_request') {

                $newData = json_decode($req->new_data, true);
                $oldData = json_decode($req->old_data, true);

                Project::where('projectid', $req->recordid)
                    ->update($newData);

                audit_log(
                    'update_approved',
                    'project',
                    $req->recordid,
                    $oldData,
                    $newData
                );
            }

            // DELETE APPROVAL
            if ($req->action === 'delete_request') {

                $oldData = json_decode($req->old_data, true);

                Project::where('projectid', $req->recordid)->delete();

                audit_log(
                    'delete_approved',
                    'project',
                    $req->recordid,
                    $oldData,
                    null
                );
            }
        }

        // =========================
        // GIS MODULE
        // =========================
        if ($req->module === 'gis') {

            if ($req->action === 'delete_request') {

                DB::table('project_gis_data')
                    ->where('projectid', $req->recordid)
                    ->delete();

                audit_log(
                    'delete_approved',
                    'gis',
                    $req->recordid,
                    null,
                    null
                );
            }
        }

        // =========================
        // UPDATE STATUS
        // =========================
        DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->update([
                'status' => 'approved',
                'approved_by' => session('userid'),
                'approved_at' => now()
            ]);

        return back()->with('success', 'Request approved successfully');
    }

    // =========================
    // REJECT REQUEST
    // =========================
    public function reject($requestid)
    {
        $req = DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->first();

        if (!$req) {
            return back()->with('error', 'Request not found');
        }

        DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->update([
                'status' => 'rejected',
                'approved_by' => session('userid'),
                'approved_at' => now()
            ]);

        audit_log(
            'rejected',
            $req->module,
            $req->recordid,
            json_decode($req->old_data, true),
            json_decode($req->new_data, true)
        );

        return back()->with('success', 'Request rejected');
    }
}