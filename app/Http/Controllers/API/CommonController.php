<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CommonController extends Controller
{
    protected $page;
    protected $loadParam;
    protected $model;
    protected $classTitle;

    public function __construct()
    {
        $uri = request()->route()->uri();
        $uri_arr = explode('/', $uri);
        $perm_identifier = str_replace(['{', '}'], ['', ''], $uri_arr[count($uri_arr) - 1] ?? "");
        $perm_identifier = substr($perm_identifier, -1) == 's' ? $perm_identifier : $perm_identifier . 's';

        $this->classTitle = ucwords(substr($perm_identifier, 0, strlen($perm_identifier) - 1));
        $this->model = '\\App\\' . $this->classTitle;
        $this->page = $perm_identifier;

        $this->loadParam = $this->loadCreateUpdateParameters();

        $this->middleware('auth:admin_api');
        $this->middleware("permission:$perm_identifier.create,admin", ['only' => ['store']]);
        $this->middleware("permission:$perm_identifier.update,admin", ['only' => ['update']]);
        $this->middleware("permission:$perm_identifier.view,admin", ['only' => ['index']]);
        $this->middleware("permission:$perm_identifier.delete,admin", ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // return Response::json(request()->route(), 200);
        $action = "SHOW " . $this->page;
        try {
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $model = $this->model::query();

            // If admin searches for any name
            if (!empty($search)) {
                $model = $model->whereName($search);
            }

            if (isset($this->loadParam['read'])) {
                if(isset($this->loadParam['read']['with'])) {
                    $withs = explode(',', $this->loadParam['read']['with']);
                    foreach ($withs as $with) {
                        $model = $model->with($with);
                    }
                }
            }

            // Provide based on paging or not
            if ($isPaging) {
                $model = $model
                    ->orderBy('id', 'desc')->paginate();
            } else {
                $model = $model
                    ->orderBy('id', 'desc')->get();
            }

            // If permission has data
            if ($model) {
                $this->lg($this->page . ' list shown', 'info', $action, 200);
                return response()->json($model);
            } else {
                $this->lg($this->page . ' list not found', 'warning', $action, 404);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // return Response::json(request()->route(), 200);
        $action = "ADD " . $this->page;

        // Validating request
        if (!isset($this->loadParam['create'])) {
            $this->lg('No validation has been set for inputs', 'warning', $action, 422);
            return response()->json('No validation has been set for inputs', 422);
        }
        $request->validate($this->loadParam['create']);

        // Validation passed
        try {
            $model = new $this->model();

            foreach ($this->loadParam['create'] as $key => $value) {
                if ($request->has($key)) {
                    if(is_string($request->get($key))) {
                        $value = strip_tags($request->get($key));
                        if ($value == "0") {
                            $value = 0;
                        }
                        $model->$key = $value;
                    }
                    if(is_array($request->get($key))) {
                        if(isset($request->get($key)['id'])) {
                            $model->$key = $request->get($key)['id'];
                        } else {
                            $model->$key = json_encode($request->get($key));
                        }
                    }
                }
            }
            // $store->title = strip_tags($request->get('title'));

            $model->save();
            if ($model) {
                $this->lg($this->classTitle . ' created successfully, measurement: ' . json_encode($model), 'info', $action, 201);
                return response()->json($model, 201);
            } else {
                $this->lg($this->classTitle . ' cannot be added now, try contacting admin', 'warning', $action, 422);
                return response()->json($this->classTitle . ' cannot be added now, try contacting admin', 422);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
//         return Response::json(request()->all(), 200);
        $action = "UPDATE " . $this->page;


        // Validating request
        if (!isset($this->loadParam['update'])) {
            $this->lg('No validation has been set for inputs', 'warning', $action, 422);
            return response()->json('No validation has been set for inputs', 422);
        }
        $request->validate($this->loadParam['update']);

        try {
            $model = $this->model::query()->find($id);
            if ($model) {

                foreach ($this->loadParam['update'] as $key => $value) {
                    if ($request->has($key)) {
                        if (!empty($request->get($key))) {
                            if(is_string($request->get($key))) {
                                $value = strip_tags($request->get($key));
                                if ($value === "0") {
                                    $value = 0;
                                }
                                $model->$key = $value;
                            }
                            if(is_array($request->get($key))) {
                                if(isset($request->get($key)['id'])) {
                                    $model->$key = $request->get($key)['id'];
                                } else {
                                    $model->$key = json_encode($request->get($key));
                                }
                            }
                        }
                    }
                }

                $model->save();

                if ($model) {
                    $this->lg($this->classTitle . " information updated successfully", 'info', $action, 200);
                    return response()->json($model, 200);
                } else {
                    $this->lg("Update fails, contact admin", 'warning', $action, 422);
                    return response()->json("Update fails, contact admin", 422);

                }
            } else {
                $this->lg($this->classTitle . " information not found", 'warning', $action, 422);
                return response()->json($this->classTitle . " information not found", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $action = "DELETE " . $this->page;
        try {
            $model = $this->model::query()->find($id);
            if ($model) {
                $delete = $model->delete();
                if ($delete) {
                    $this->lg("Successfully deleted", 'info', $action, 200);
                    return response()->json("Successfully deleted", 200);
                } else {
                    $this->lg("Could not delete the " . $this->page, 'warning', $action, 422);
                    return response()->json("Could not delete the " . $this->page, 422);
                }
            } else {
                $this->lg($this->page . " cannot be found by this ID", 'warning', $action, 404);
                return response()->json($this->page . " cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }


    public function loadCreateUpdateParameters()
    {
        $params = [
            'stores' => [
                'create' => [
                    'name' => 'required',
                    'address' => 'required',
                    'mobile_no' => 'required',
                    'status' => 'required'
                ],
                'update' => [
                    'address' => 'required',
                    'mobile_no' => 'required',
                    'status' => 'required',
                ]
            ],
            'measurements' => [
                'create' => [
                    'title' => 'required',
                    'allow_fraction' => 'required',
                ],
                'update' => [
                    'title' => 'required',
                    'allow_fraction' => 'required',
                ]
            ],
            'products' => [
                'read' => [
                    'with' => 'unit'
                ],
                'create' => [
                    'title' => 'required',
                    'brand' => 'required',
                    'unit' => 'required'
                ],
                'update' => [
                    'title' => 'required',
                    'brand' => 'required',
                    'unit' => 'required'
                ]
            ]
        ];

        $page = $this->page;
        if ($page) {
            return $params[$page];
        } else {
            return [];
        }
    }
}
