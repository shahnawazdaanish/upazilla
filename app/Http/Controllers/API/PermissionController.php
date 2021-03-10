<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin_api');
        $this->middleware('permission:permissions.create,admin', ['only' => ['store']]);
        $this->middleware('permission:permissions.update,admin',   ['only' => ['update']]);
        $this->middleware('permission:permissions.view,admin',   ['only' => ['index']]);
        $this->middleware('permission:permissions.delete,admin',   ['only' => ['delete']]);
    }

    /**
     * List all permission
     *
     * @return JsonResponse
     * */
    function index()
    {
        $action = "SHOW PERMISSIONS";
        try {
            $search = request()->get("search");
            $guard_name = request()->get("guard_name");
            $isPaging = request()->exists("page");

            $permissions = Permission::query();

            // If admin searches for any name
            if (!empty($search)) {
                $permissions = $permissions->where(function ($query) use ($search) {
                    return $query->where('name', $search);
                });
            }

            // If filtered by guard name
            if (!empty($guard_name)) {
                $permissions = $permissions->where('guard_name', $guard_name);
            }

            // Provide based on paging or not
            if ($isPaging) {
                $permissions = $permissions->paginate();
            } else {
                $permissions = $permissions->get();
            }

            // If permission has data
            if ($permissions) {
                $this->lg($permissions, 'info', $action, 200);
                return response()->json($permissions);
            } else {
                $this->lg("Table is empty", 'warning', $action, 404);
                return response()->json("Not found", 404);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $action = "CREATE PERMISSION";
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'required|string|in:admin,user,all'
        ]);

        $permission = null; // initiate a variable for permission

        DB::beginTransaction();

        try {
            if ($request->get('guard_name') == 'all') {
                $guards = ['admin', 'user'];

                foreach ($guards as $guard) {

                    $permission = new Permission();
                    $permission->name = $request->get('name');
                    $permission->guard_name = $guard;
                    $permission->save();
                }

            } else {

                $permission = new Permission();
                $permission->name = $request->get('name');
                $permission->guard_name = $request->get('guard_name');
                $permission->save();
            }

            if ($permission) {
                DB::commit();
                $this->lg($permission, 'info', $action, 201);
                return response()->json($permission, 201);
            } else {
                $this->lg("Unable to add this permission right now.", 'warning', $action,422);
                return response()->json("Unable to add this permission right now.", 422);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            DB::rollBack();
            return response()->json($this->experDifficulties, 500);
        }
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
        $action = "UPDATE PERMISSION";
        $request->validate([
            'name' => 'required|string',
            'guard_name' => 'required|string'
        ]);

        try {
            $permission = Permission::findById($id, $request->get('guard_name'));
            if ($permission) {
                if (!empty($request->get('name'))) {
                    $permission->name = $request->get('name');
                }
                if (!empty($request->get('guard_name'))) {
                    $permission->guard_name = $request->get('guard_name');
                }
                $permission->save();
                if ($permission) {
                    $this->lg($permission, 'info', $action, 200);
                    return response()->json($permission, 200);
                } else {
                    $this->lg("Could not update the permission", 'warning', $action, 422);
                    return response()->json("Could not update the permission", 422);
                }
            } else {
                $this->lg("Permission cannot be found by this ID", 'warning', $action, 404);
                return response()->json("Permission cannot be found by this ID", 404);
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
        $action = "DELETE PERMISSION";
        try {
            $permission = Permission::findById($id, request()->get('guard_name'));
            if ($permission) {
                $delete = $permission->delete();
                if ($delete) {
                    $this->lg("Successfully deleted", 'info', $action, 200);
                    return response()->json("Successfully deleted", 200);
                } else {
                    $this->lg("Could not delete the permission", 'warning', $action, 422);
                    return response()->json("Could not delete the permission", 422);
                }
            } else {
                $this->lg("Permission cannot be found by this ID", 'warning', $action, 422);
                return response()->json("Permission cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }
}
