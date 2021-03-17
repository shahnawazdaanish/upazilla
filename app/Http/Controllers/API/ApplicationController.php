<?php

namespace App\Http\Controllers\API;

use App\Application;
use App\Approval;
use App\Exports\ApplicationReport;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | mixed
     */
    public function index()
    {
        $action = "SHOW APPLICATIONS";
        try {
            $search = request()->get("search");
            $type = request()->get("type");
            $status = request()->get("status");
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $application = Application::query();

            // If admin searches for any name
            if (!empty($search)) {
                $application = $application->where(function ($q) use ($search) {
                    return $q->where("applicant_name_en", $search . '%')
                        ->orWhere("applicant_name_bn", 'LIKE', $search . '%')
                        ->orWhere("nid_no", $search)
                        ->orWhere("mobile_no");
                });
            }

            if (!empty($type)) {
                $application = $application->where('application_type', $type);
            }

            if (!empty($status) && $status == 'shortlisted') {
                $application = $application->where('short_listed', 1);
            }
            if (!empty($status) && $status == 'approved') {
                $application = $application->where('first_step_approval', 1);
            }
            if (!empty($status) && $status == 'denied') {
                $application = $application->where('first_step_approval', 0);
            }

            $auth_guard = Auth::guard();
            $auth_id = Auth::id();

            if (Auth::user() instanceof User) {
                $application = $application->whereWaitingForApproval($auth_id);
            }

            // Provide based on paging or not
            if ($isPaging) {
                $application = $application
                    ->orderBy('id', 'desc')->paginate();
            } else {
                $application = $application
                    ->orderBy('id', 'desc')->get();
            }

            // If application has data
            if ($application) {
                $this->lg('Applications list shown', 'info', $action, 200);
                return response()->json($application);
            } else {
                $this->lg('Applications list not found', 'warning', $action, 404);
                return response()->json("Not found", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $action = "SHOW APPLICATION";
        try {
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $application = Application::query()->whereApplicationId($id);


            $auth_guard = Auth::guard();
            $auth_id = Auth::id();
            if ($auth_guard !== 'admin_api') {
                $application = $application->whereWaitingForApproval($auth_id);
            }

            $application = $application->first();

            $data = [];
            if (isset($application->application_type)) {
                $appController = new \App\Http\Controllers\ApplicationController();
                $form = $appController->SwitchForm($application->application_type);

                $data["আবেদনের বিষয়"] = $application->application_type;
                $data["বরাবর"] = $application->application_to;

                foreach ($form as $f) {
                    if (isset($f['title'])) {
                        $data[$f['title']] = isset($f['name']) ? $application[$f['name']] : '';
                    }
                    if (isset($f['sub-form'])) {
                        foreach ($f['sub-form'] as $fL2) {
                            if (isset($fL2['title'])) {
                                $data[$fL2['title']] = isset($fL2['name']) ? $application[$fL2['name']] : '';
                            }
                            if (isset($fL2['sub-form'])) {
                                foreach ($fL2['sub-form'] as $fL3) {
                                    if (isset($fL3['title'])) {
                                        $data[$fL3['title']] = isset($fL3['name']) ? $application[$fL3['name']] : '';
                                    }
                                }
                            }
                        }
                    }
                }

                $data['status'] = $application->status;
                $data['approvals'] = $application->approvals();
                $data['has_my_approval'] = $application->approvals()->contains("id", Auth::id());
            }

            // If application has data
            if ($data) {
                $this->lg('Applications list shown', 'info', $action, 200);
                return response()->json($data);
            } else {
                $this->lg('Applications list not found', 'warning', $action, 404);
                return response()->json("Not found", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    public function TakeAction()
    {
        $validator = Validator::make(request()->all(), [
            'action' => 'required:in:shortlist,approve,deny',
            'id' => 'required:exists:applications,application_id',
            'selected_user' => 'nullable|exists:users,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $application = Application::query()->where('application_id', request()->get('id'))->first();
        $guard = Auth::guard();
        if ($guard === 'admin_api' && request()->get("action") === 'shortlist') {
            // short list
            if ($application->short_listed) {
                return response()->json("Already short listed", 422);
            }
            $application->short_listed = 1;
            $application->save();
        }
        if (request()->get("action") === 'approve') {
            // approve
            $approval = new Approval();
            $approval->application_id = request()->get("id");
            $approval->user_id = Auth::id();
            $approval->approval_type = "APPROVED";
            $approval->save();

            $application->first_step_approval = 1;
            $application->save();
        }

        if (request()->get("action") === 'deny') {
            // Denied
            $approval = new Approval();
            $approval->application_id = request()->get("id");
            $approval->user_id = Auth::id();
            $approval->approval_type = "DENIED";
            $approval->save();

            $application->first_step_approval = 0;
            $application->save();
        }


        if (request()->get("action") === 'forward') {
            // Denied
            if (request()->get("selected_user") != null) {
                $application->waiting_for_approval = request()->get("selected_user");
                $application->save();
            } else {
                return response()->json("Please select an user to send", 422);
            }
        }

        return response()->json("Action performed successfully", 200);
    }

    public function GetUsers()
    {
        return User::query()->where("id", "!=", Auth::id())->get();
    }

    function downloadReport(Request $request)
    {
        $action = "DOWNLOAD REPORT";
        // Check validation
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required'
        ]);

        // Validation passed
        $start = $request->get('start');
        $end = $request->get('end');
        $status = $request->get('search_status');
        $type = $request->get('search_type');

        try {
            // Permission check
            if (!auth()->user()->can('applications.view')) {
                $this->lg($this->accessDenied . ', PERMISSION: applications.download', 'alert', $action, 403);
                return response()->json($this->accessDenied, 403);
            }


            $start_date = Carbon::parse($start);
            $end_date = Carbon::parse($end);

            $isThreeMonthsOld = $start_date->toDate() > Carbon::now()->subMonths(3)->toDate();
            $isNotInFuture = $end_date->toDate() <= Carbon::now()->toDate();

            if ($isThreeMonthsOld and $isNotInFuture) {
                // Fetch Application
                $application = Application::query();

                if (!empty($type)) {
                    $application = $application->where('application_type', $type);
                }

                if (!empty($status) && $status == 'shortlisted') {
                    $application = $application->where('short_listed', 1);
                }
                if (!empty($status) && $status == 'approved') {
                    $application = $application->where('first_step_approval', 1);
                }
                if (!empty($status) && $status == 'denied') {
                    $application = $application->where('first_step_approval', 0);
                }

                $auth_guard = Auth::guard();
                $auth_id = Auth::id();
                if (Auth::user() instanceof User) {
                    $application = $application->whereWaitingForApproval($auth_id);
                }


                $application = $application
                    ->whereBetween('created_at',
                        [$start_date->toDateString(), $end_date->toDateString()])
                    ->orderBy('id', 'desc')->get();
//                echo $start_date->toDateString();
//                echo $application->toSql();
//                return response()->json("No data available to download", 200);


                if ($application) {
                    $this->lg('Report downloaded successfully', 'info', $action, 200);
                    return Excel::download(new ApplicationReport($application), 'all_applications.xlsx');
                } else {
                    $this->lg('No data available to download', 'warning', $action, 404);
                    return response()->json("No data available to download", 422);
                }
            } else {
                $this->lg('Date range is out of allowed range', 'warning', $action, 404);
                return response()->json("Date range is out of allowed range", 422);
            }

        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
