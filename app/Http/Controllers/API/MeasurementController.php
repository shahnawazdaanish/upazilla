<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin_api');
        $this->middleware('permission:measurements.create,admin', ['only' => ['store']]);
        $this->middleware('permission:measurements.update,admin', ['only' => ['update']]);
        $this->middleware('permission:measurements.view,admin', ['only' => ['index']]);
        $this->middleware('permission:measurements.delete,admin', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $action = "SHOW MEASUREMENTS";
        try {
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $measurement = Measurement::query();

            // If admin searches for any name
            if (!empty($search)) {
                $measurement = $measurement->whereTitle($search);
            }

            // Provide based on paging or not
            if ($isPaging) {
                $measurement = $measurement
                    ->orderBy('id', 'desc')->paginate();
            } else {
                $measurement = $measurement
                    ->orderBy('id', 'desc')->get();
            }

            // If permission has data
            if ($measurement) {
                $this->lg('Measurements list shown', 'info', $action, 200);
                return response()->json($measurement);
            } else {
                $this->lg('Measurements list not found', 'warning', $action, 404);
                return response()->json("Not found", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $action = "ADD Measurement";
        // Validating request
        $request->validate([
            'title' => 'required|string'
        ]);

        // Validation passed
        try {
            $measurement = new Measurement();
            $measurement->title = strip_tags($request->get('title'));

            $measurement->save();
            if ($measurement) {
                $this->lg('Measurement created successfully, measurement: ' . json_encode($measurement), 'info', $action, 201);
                return response()->json($measurement, 201);
            } else {
                $this->lg('Measurement cannot be added now, try contacting admin', 'warning', $action, 422);
                return response()->json('Measurement cannot be added now, try contacting admin', 422);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $action = "UPDATE Measurement";
        try {
            $measurement = Measurement::query()->find($id);
            if ($measurement) {
                if (!empty($request->get('title'))) {
                    $measurement->title = $request->get('title');
                }

                $measurement->save();

                if ($measurement) {
                    $this->lg("Measurement information updated successfully", 'info', $action, 200);
                    return response()->json($measurement, 200);
                } else {
                    $this->lg("Update fails, contact admin", 'warning', $action, 422);
                    return response()->json("Update fails, contact admin", 422);

                }
            } else {
                $this->lg("Measurement information not found", 'warning', $action, 422);
                return response()->json("Measurement information not found", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
