<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin_api');
        $this->middleware('permission:roles.create,admin', ['only' => ['store']]);
        $this->middleware('permission:roles.update,admin', ['only' => ['update']]);
        $this->middleware('permission:roles.view,admin', ['only' => ['index']]);
        $this->middleware('permission:roles.delete,admin', ['only' => ['delete']]);
    }


    /**
     * List all roles
     *
     * @return JsonResponse
     * */
    function index()
    {
        $action = "SHOW ROLES";
        try {
            $search = request()->get("search");
            $guard_name = request()->get("guard_name");
            $isPaging = request()->exists("page");

            $roles = Role::query();

            // If admin searches for any name
            if (!empty($search)) {
                $roles = $roles->where(function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%');
                });
            }

            // If filtered by guard name
            if (!empty($guard_name)) {
                $roles = $roles->where('guard_name', $guard_name);
            }

            // get all permissions of a role
            $roles = $roles->with('permissions:id,name');

            // Provide based on paging or not
            if ($isPaging) {
                $roles = $roles->paginate();
            } else {
                $roles = $roles->get();
            }

            $hasPaging = $roles instanceof \Illuminate\Pagination\LengthAwarePaginator;
            if ($hasPaging) {
                $count = $roles->total();
            } else {
                $count = count($roles->toArray());
            }

            // If permission has data
            if ($count > 0) {
                $this->lg("Role list shown", 'info', $action, 200);
                return response()->json($roles);
            } else {
                $this->lg("Yet to get an entry", 'warning', $action, 404);
                return response()->json("Yet to get an entry!", 404);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $action = "CREATE ROLE";
        // Validating request
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'required|string',
            'permissions' => 'required|array'
        ]);

        // Validation Successful

        // For keeping consistency in DB insertion, we use DB transactions
        DB::beginTransaction();

        try {
            $role = new Role();
            $role->name = $request->get('name');
            $role->guard_name = $request->get('guard_name');
            $role->save();
            if ($role) {
                $permissions = $this->preparePermissionFromRequest($request);
                $role->syncPermissions($permissions);

                DB::commit();
                $this->lg($role, 'info', $action, 201);
                return response()->json($role, 201);
            } else {
                $this->lg("Unable to add these permissions right now.", 'warning', $action, 422);
                return response()->json("Unable to add these permissions right now.", 422);
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
        $action = "UPDATE ROLE";
        try {
            DB::beginTransaction();
            $role = Role::findById($id, $request->get('guard_name'));
            if ($role) {
                if (!empty($request->get('name'))) {
                    $role->name = $request->get('name');
                }
                if (!empty($request->get('guard_name'))) {
                    $role->guard_name = $request->get('guard_name');
                }
                $role->save();
                if ($role) {
                    $permissions = $this->preparePermissionFromRequest($request);
                    DB::commit();

                    $role->syncPermissions($permissions);

//                    $finalRole = Role::findById($id);
                    $this->lg("Permission updated successfully", 'info', $action, 200);
                    return response()->json($role, 200);
                } else {
                    $this->lg("Could not update the role", 'warning', $action, 422);
                    return response()->json("Could not update the role", 422);
                }
            } else {
                $this->lg("Role cannot be found by this ID", 'warning', $action, 404);
                return response()->json("Role cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            DB::rollBack();
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
        $action = "DELETE ROLE";
        try {
            $role = Role::findById($id);
            if ($role) {
                $delete = $role->delete();
                if ($delete) {
                    $this->lg("Successfully deleted", 'info', $action, 200);
                    return response()->json("Successfully deleted", 200);
                } else {
                    $this->lg("Could not delete the role", 'warning', $action, 422);
                    return response()->json("Could not delete the role", 422);
                }
            } else {
                $this->lg("Role cannot be found by this ID", 'warning', $action, 404);
                return response()->json("Role cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /*
     * Prepare Permissions
     * Create permissions array from request
     *
     * @param Request $request
     * @return array
     * */
    public function preparePermissionFromRequest(Request $request): array
    {
        $permissions = [];
        foreach ($request->get('permissions') as $perm) {
            if (isset($perm['id'])) {
                array_push($permissions, $perm['id']);
            }
        }
        return $permissions;
    }


    /*
     * Get User Roles
     *
     * select all roles using user guard
     *
     * @return Collection
     * */
    public function getUserRoles()
    {
        $this->lg("Showing user roles", 'info', "GET USER ROLES", 200);
        return Role::query()->where('guard_name', 'user')->select(['id', 'name'])->get();
    }

    /*
     * Get Admin Roles
     *
     * select all roles using admin guard
     *
     * @return Collection
     * */
    public function getAdminRoles()
    {
        $this->lg("Showing admin roles", 'info', "GET ADMIN ROLES", 200);
        return Role::query()->where('guard_name', 'admin')->select(['id', 'name'])->get();
    }
}
