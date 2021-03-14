<?php

namespace App\Http\Controllers\API;

use App\Application;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            $isPaging = request()->exists("page");

            $application = Application::query();

            // If admin searches for any name
            if (!empty($search)) {
                $application = $application->whereTitle($search);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $action = "SHOW APPLICATION";
        try {
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $application = Application::query()->whereApplicationId($id)->first();

            $data = [];
            if(isset($application->application_type)) {
                $appController = new \App\Http\Controllers\ApplicationController();
                $form = $appController->SwitchForm($application->application_type);

                $data["আবেদনের বিষয়"] = $application->application_type;
                $data["বরাবর"] = $application->application_to;

                foreach ($form as $f) {
                    if(isset($f['title'])) {
                        $data[$f['title']] = isset($f['name']) ? $application[$f['name']] : '';
                    }
                    if(isset($f['sub-form'])){
                        foreach ($f['sub-form'] as $fL2){
                            if(isset($fL2['title'])) {
                                $data[$fL2['title']] = isset($fL2['name']) ? $application[$fL2['name']] : '';
                            }
                            if(isset($fL2['sub-form'])){
                                foreach ($fL2['sub-form'] as $fL3){
                                    if(isset($fL3['title'])) {
                                        $data[$fL3['title']] = isset($fL3['name']) ? $application[$fL3['name']] : '';
                                    }
                                }
                            }
                        }
                    }
                }
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
