<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin_api');
        $this->middleware('permission:stores.create,admin', ['only' => ['store']]);
        $this->middleware('permission:stores.update,admin', ['only' => ['update']]);
        $this->middleware('permission:stores.view,admin', ['only' => ['index']]);
        $this->middleware('permission:stores.delete,admin', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $action = "SHOW STORES";
        try {
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $store = Store::query();

            // If admin searches for any name
            if (!empty($search)) {
                $store = $store->whereTitle($search);
            }

            // Provide based on paging or not
            if ($isPaging) {
                $store = $store
                    ->orderBy('id', 'desc')->paginate();
            } else {
                $store = $store
                    ->orderBy('id', 'desc')->get();
            }

            // If permission has data
            if ($store) {
                $this->lg('STORES list shown', 'info', $action, 200);
                return response()->json($store);
            } else {
                $this->lg('STORES list not found', 'warning', $action, 404);
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
        $action = "ADD Store";
        // Validating request
        $request->validate([
            'title' => 'required|string'
        ]);

        // Validation passed
        try {
            $store = new Store();
            $store->title = strip_tags($request->get('title'));

            $store->save();
            if ($store) {
                $this->lg('Store created successfully, measurement: ' . json_encode($store), 'info', $action, 201);
                return response()->json($store, 201);
            } else {
                $this->lg('Store cannot be added now, try contacting admin', 'warning', $action, 422);
                return response()->json('Store cannot be added now, try contacting admin', 422);
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
        $action = "UPDATE Store";
        try {
            $store = Store::query()->find($id);
            if ($store) {
                if (!empty($request->get('title'))) {
                    $store->title = $request->get('title');
                }

                $store->save();

                if ($store) {
                    $this->lg("Store information updated successfully", 'info', $action, 200);
                    return response()->json($store, 200);
                } else {
                    $this->lg("Update fails, contact admin", 'warning', $action, 422);
                    return response()->json("Update fails, contact admin", 422);

                }
            } else {
                $this->lg("Store information not found", 'warning', $action, 422);
                return response()->json("Store information not found", 404);
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
