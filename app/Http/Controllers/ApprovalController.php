<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Services\AuditService;

class ApprovalController extends Controller
{
    private function authorizeAdmin()
    {
        if (!auth()->check() || auth()->user()->roleid != 1) {
            abort(403, 'Unauthorized');
        }
    }

    public function index()
    {
        $this->authorizeAdmin();

        $requests = DB::table('approval_requests')
            ->leftJoin('users', 'users.userid', '=', 'approval_requests.userid')
            ->select('approval_requests.*', 'users.userid as username')
            ->orderByDesc('approval_requests.created_at')
            ->paginate(10)

            ->withQueryString();

        return view('admin.permission_approvals', compact('requests'));
    }

    public function approve($requestid)
    {
        $this->authorizeAdmin();

        $req = DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->first();

        if (!$req) {
            return back()->with('error', 'Request not found');
        }

        if ($req->status !== 'pending') {
            return back()->with('error', 'Already processed');
        }

        $newData = json_decode($req->new_data ?? '{}', true);
        $oldData = json_decode($req->old_data ?? '{}', true);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------
            | PROJECT MODULE
            |--------------------------------------
            */
            if ($req->module === 'PROJECT') {

                // UPDATE
                if ($req->action === 'update_request') {

                    Project::where('projectid', $req->recordid)
                        ->update($newData);

                    AuditService::log(
                        'UPDATE_APPROVED',
                        'PROJECT',
                        null,
                        $oldData,
                        $newData,
                        $req->recordid
                    );
                }

                // DELETE
                if ($req->action === 'delete_request') {

                    Project::where('projectid', $req->recordid)->delete();

                    AuditService::log(
                        'DELETE_APPROVED',
                        'PROJECT',
                        null,
                        $oldData,
                        null,
                        $req->recordid
                    );
                }
            }

            /*
            |--------------------------------------
            | GIS MODULE
            |--------------------------------------
            */
            if ($req->module === 'GIS') {

                if ($req->action === 'upload_request') {

                    $projectid = $newData['projectid'] ?? null;
                    $layername  = $newData['layername'] ?? null;
                    $geojson    = $newData['geojson'] ?? null;

                    if (!isset($geojson['features'])) {
                        throw new \Exception("Invalid GeoJSON structure");
                    }

                    foreach ($geojson['features'] as $feature) {

                        if (!isset($feature['geometry'])) {
                            continue;
                        }

                        DB::table('project_gis_data')->insert([
                            'projectid' => $projectid,
                            'layername' => $layername,
                            'geometry' => DB::raw(
                                "ST_SetSRID(ST_GeomFromGeoJSON('" .
                                json_encode($feature['geometry']) .
                                "'), 4326)"
                            ),
                            'attributes' => json_encode($feature['properties'] ?? [])
                        ]);
                    }

                    AuditService::log(
                        'CREATE_APPROVED',
                        'GIS',
                        null,
                        null,
                        $newData,
                        $req->recordid
                    );
                }

                if ($req->action === 'delete_request') {

                    DB::table('project_gis_data')
                        ->where('projectid', $req->recordid)
                        ->where('layername', $req->layername)
                        ->delete();

                    AuditService::log(
                        'DELETE_APPROVED',
                        'GIS',
                        null,
                        $oldData,
                        null,
                        $req->recordid
                    );
                }
            }

            /*
            |--------------------------------------
            | FINAL STATUS UPDATE
            |--------------------------------------
            */
            DB::table('approval_requests')
                ->where('requestid', $requestid)
                ->update([
                    'status' => 'approved',
                    'approved_by' => auth()->user()->userid,
                    'approved_at' => now()
                ]);

            DB::commit();

            return back()->with('success', 'Request approved successfully');

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('Approval failed', [
                'error' => $e->getMessage(),
                'request_id' => $requestid
            ]);

            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function reject($requestid)
    {
        $this->authorizeAdmin();

        $req = DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->first();

        if (!$req) {
            return back()->with('error', 'Request not found');
        }

        if ($req->status !== 'pending') {
            return back()->with('error', 'Already processed');
        }

        DB::table('approval_requests')
            ->where('requestid', $requestid)
            ->update([
                'status' => 'rejected',
                'approved_by' => auth()->user()->userid,
                'approved_at' => now()
            ]);

        AuditService::log(
            'REJECTED',
            $req->module,
            null,
            json_decode($req->old_data ?? '[]', true),
            json_decode($req->new_data ?? '[]', true),
            $req->recordid
        );

        return back()->with('success', 'Request rejected successfully');
    }
}