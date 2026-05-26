<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Project;
use App\Services\AuditService;

class ApprovalController extends Controller
{
    // authorize admin
    private function authorizeAdmin()
    {
        if (
            !auth()->check() ||
            auth()->user()->roleid != 1
        ) {

            abort(403, 'Unauthorized');
        }
    }


    // list all approval requests
    public function index()
    {
        try {

            $this->authorizeAdmin();

            $requests = DB::table('approval_requests')
                ->leftJoin(
                    'users',
                    'users.userid',
                    '=',
                    'approval_requests.userid'
                )
                ->select(
                    'approval_requests.*',
                    'users.userid as username'
                )
                ->orderByDesc('approval_requests.created_at')
                ->paginate(10)
                ->withQueryString();

            return view(
                'admin.permission_approvals',
                compact('requests')
            );

        } catch (\Exception $e) {

            Log::error('Approval Index Error', [
                'message' => $e->getMessage(),
                'user' => auth()->id()
            ]);

            return back()->with(
                'error',
                'Unable to load approval requests.'
            );
        }
    }


    // approve request func
    public function approve($requestid)
    {
        try {

            $this->authorizeAdmin();

            DB::beginTransaction();

            $req = DB::table('approval_requests')
                ->where('requestid', $requestid)
                ->lockForUpdate()
                ->first();

            // Request not found
            if (!$req) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Request not found.'
                );
            }

            // Prevent double approval
            if ($req->status !== 'pending') {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Request already processed.'
                );
            }

            // Allowed modules
            $allowedModules = [
                'PROJECT',
                'GIS'
            ];

            if (!in_array($req->module, $allowedModules)) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Invalid module type.'
                );
            }

            // Decode JSON safely
            $newData = json_decode(
                $req->new_data ?? '{}',
                true
            );

            $oldData = json_decode(
                $req->old_data ?? '{}',
                true
            );

            if (
                json_last_error() !== JSON_ERROR_NONE
            ) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Invalid approval request data.'
                );
            }


            /*
                project module
            */
            if ($req->module === 'PROJECT') {

                // UPDATE REQUEST
                if ($req->action === 'update_request') {

                    $project = Project::where(
                        'projectid',
                        $req->recordid
                    )->first();

                    if (!$project) {

                        DB::rollBack();

                        return back()->with(
                            'error',
                            'Project not found.'
                        );
                    }

                    $project->update($newData);

                    AuditService::log(
                        'UPDATE_APPROVED',
                        'PROJECT',
                        'Project update approved',
                        $oldData,
                        $newData
                    );
                }


                // delete request
                if ($req->action === 'delete_request') {

                    $project = Project::where(
                        'projectid',
                        $req->recordid
                    )->first();

                    if (!$project) {

                        DB::rollBack();

                        return back()->with(
                            'error',
                            'Project not found.'
                        );
                    }

                    $project->delete();

                    AuditService::log(
                        'DELETE_APPROVED',
                        'PROJECT',
                        'Project delete approved',
                        $oldData,
                        null
                    );
                }
            }


            /*
                gis module
            */
            if ($req->module === 'GIS') {

                // GIS UPLOAD APPROVAL
                if ($req->action === 'upload_request') {

                    $projectid = $newData['projectid'] ?? null;
                    $layername = $newData['layername'] ?? null;
                    $geojson   = $newData['geojson'] ?? null;

                    // GeoJSON validation
                    if (
                        !$geojson ||
                        !isset($geojson['features'])
                    ) {

                        throw new \Exception(
                            'Invalid GeoJSON structure.'
                        );
                    }

                    foreach (
                        $geojson['features']
                        as $feature
                    ) {

                        if (
                            !isset($feature['geometry'])
                        ) {
                            continue;
                        }

                        DB::table('project_gis_data')
                            ->insert([

                                'projectid' => $projectid,

                                'layername' => $layername,

                                'geometry' => DB::raw(
                                    "ST_SetSRID(
                                        ST_GeomFromGeoJSON('" .
                                        json_encode(
                                            $feature['geometry']
                                        ) .
                                        "'),
                                    4326)"
                                ),

                                'attributes' => json_encode(
                                    $feature['properties'] ?? []
                                )

                            ]);
                    }

                    AuditService::log(
                        'CREATE_APPROVED',
                        'GIS',
                        'GIS upload approved',
                        null,
                        $newData
                    );
                }


                // gis delete request
                if ($req->action === 'delete_request') {

                    DB::table('project_gis_data')
                        ->where(
                            'projectid',
                            $req->recordid
                        )
                        ->where(
                            'layername',
                            $req->layername
                        )
                        ->delete();

                    AuditService::log(
                        'DELETE_APPROVED',
                        'GIS',
                        'GIS delete approved',
                        $oldData,
                        null
                    );
                }
            }


            /*
                Status upadte
            */
            DB::table('approval_requests')
                ->where('requestid', $requestid)
                ->update([

                    'status' => 'approved',

                    'approved_by' => auth()->user()->userid,

                    'approved_at' => now()

                ]);


            DB::commit();

            return back()->with(
                'success',
                'Request approved successfully.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Approval Failed', [

                'message' => $e->getMessage(),

                'trace' => $e->getTraceAsString(),

                'requestid' => $requestid,

                'approved_by' => auth()->id()

            ]);

            return back()->with(
                'error',
                'Approval failed.'
            );
        }
    }


    // reject request
    public function reject($requestid)
    {
        try {

            $this->authorizeAdmin();

            DB::beginTransaction();

            $req = DB::table('approval_requests')
                ->where('requestid', $requestid)
                ->lockForUpdate()
                ->first();

            // Request not found
            if (!$req) {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Request not found.'
                );
            }

            // Prevent double rejection
            if ($req->status !== 'pending') {

                DB::rollBack();

                return back()->with(
                    'error',
                    'Request already processed.'
                );
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

                'Approval request rejected',

                json_decode(
                    $req->old_data ?? '[]',
                    true
                ),

                json_decode(
                    $req->new_data ?? '[]',
                    true
                )

            );

            DB::commit();

            return back()->with(
                'success',
                'Request rejected successfully.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Reject Request Error', [

                'message' => $e->getMessage(),

                'trace' => $e->getTraceAsString(),

                'requestid' => $requestid,

                'rejected_by' => auth()->id()

            ]);

            return back()->with(
                'error',
                'Unable to reject request.'
            );
        }
    }
}