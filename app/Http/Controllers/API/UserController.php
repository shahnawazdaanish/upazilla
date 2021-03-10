<?php

namespace App\Http\Controllers\API;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('auth:admin_api');
        $this->middleware('permission:users.create,admin|user', ['only' => ['store']]);
        $this->middleware('permission:users.update,admin|user', ['only' => ['update']]);
        $this->middleware('permission:users.view,admin|user', ['only' => ['index']]);
        $this->middleware('permission:users.delete,admin|user', ['only' => ['delete']]);
    }

    /**
     * List all users
     *
     * @return JsonResponse
     * */
    function index()
    {
        $action = "SHOW USERS";
        /*$hasAccess = $this->checkPermission('users.view');
        if(!$hasAccess) {
            return response()->json('Access Denied', 403);
        }*/

        try {
            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $user = User::query();

            // If admin searches for any name
            if (!empty($search)) {
                $user = $user->where(function ($query) use ($search) {
                    return $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('username', 'LIKE', '%' . $search . '%');
                });
            }

            // get role of this user
//            $user = $user->with(['roles:id', 'roles:name', 'merchant:id', 'merchant:name']);
            $user = $user->with(['roles' => function ($query) {
                $query->select('id', 'name');
            }, 'merchant' => function ($query) {
                $query->select('id', 'name');
            }]);

            // Provide based on paging or not
            if ($isPaging) {
                $user = $user->paginate();
            } else {
                $user = $user->get();
            }

            // If permission has data
            if ($user) {
                $this->lg("Shown users list successfully", 'info', $action, 200);
                return response()->json($user);
            } else {
                $this->lg("Users table is empty", 'warning', $action, 404);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $action = "CREATE USER";
        $request->merge([
            'username' => strip_tags(
                strtolower(preg_replace('/[^ \w-]/', '-', $request->get('username')))
            )
        ]);
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username|min:4',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'role.name' => 'required|exists:roles,name,guard_name,user',
            'merchant.name' => 'required|exists:merchants,name,status,ACTIVE',
        ]);

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = preg_replace('/[^ \w-]/', ' ', $request->get('name'));
            $user->username = $request->get('username');
            $user->email = $request->get('email');
            $user->merchant_id = $request->input('merchant.id');
            $user->password = Hash::make($request->get('password'));
            $user->save();
            if ($user) {
                $assignRole = $user->assignRole($request->input('role.id'));
                if ($assignRole) {
                    DB::commit();
                    $this->lg("User created successfully", 'info', $action, 201, 'interface', $user);
                    return response()->json($user, 201);
                } else {
                    $this->lg("User creation fails, Role can't be assigned", 'warning', $action, 422);
                    return response()->json("User creation fails, Role can't be assigned", 422);
                }
            } else {
                $this->lg("User creation fails, DB issue", 'warning', $action, 422);
                return response()->json("User creation fails, DB issue", 422);
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
        $action = "UPDATE USER";
        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|confirmed|min:6',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
            'role.name' => 'required|exists:roles,name,guard_name,user',
            'merchant.name' => 'required|exists:merchants,name,status,ACTIVE',
        ]);

        try {
            $user = User::query()->find($id);
            if ($user) {
                if (!empty($request->get('name'))) {
                    $user->name = $request->get('name');
                }
                if (!empty($request->get('email'))) {
                    $user->email = $request->get('email');
                }
                if (!empty($request->get('password'))) {
                    $user->password = Hash::make($request->get('password'));
                }
                if (!empty($request->get('status'))) {
                    $user->status = $request->get('status');
                }
                $user->save();
                if ($user) {
                    $user->syncRoles([$request->input('role.id')]);

                    $finaluser = User::query()->whereId($user->id)->api()->with(['roles' => function ($query) {
                        $query->select('id', 'name');
                    }, 'merchant' => function ($query) {
                        $query->select('id', 'name');
                    }])->first();

                    $this->lg("User updated successfully", 'info', $action, 200, 'interface', $user);
                    return response()->json($finaluser, 200);
                } else {
                    $this->lg("Could not update the user", 'warning', $action, 422);
                    return response()->json("Could not update the user", 422);
                }
            } else {
                $this->lg("User cannot be found by this ID", 'warning', $action, 404);
                return response()->json("User cannot be found by this ID", 404);
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
        $action = "DELETE USER";
        try {
            $user = User::query()->find($id);
            if ($user) {
                $delete = $user->delete();
                if ($delete) {
                    $this->lg("Successfully deleted", 'info', $action, 200);
                    return response()->json("Successfully deleted", 200);
                } else {
                    $this->lg("Could not delete the user", 'warning', $action, 422);
                    return response()->json("Could not delete the user", 422);
                }
            } else {
                $this->lg("User cannot be found by this ID", 'warning', $action, 404);
                return response()->json("User cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }


    /**
     * Get Merchant List of User
     *
     * @param Request $request
     * @return JsonResponse
     * */
    function getUserMerchants(Request $request)
    {
        $user = auth()->guard('api')->user();
        if($user) {
            $this->lg("Merchant show of User successfully", 'info', "SHOW USERS MERCHANT", 200);
            $merchants = Merchant::query()->where('status', 'ACTIVE')
                ->where('id', $user->merchant_id)
                ->select(['id', 'name'])->get();
            return response()->json($merchants, 200);
        } else {
            return response()->json([], 422);
        }
    }
    /**
     * Get Merchant List of User
     *
     * @param Request $request
     * @return JsonResponse
     * */
    function getUserAdminMerchants(Request $request)
    {
        $this->lg("Merchant show of User successfully", 'info', "SHOW USERS MERCHANT", 200);
        $merchants = Merchant::query()->where('status', 'ACTIVE')
            ->select(['id', 'name'])->get();
        return response()->json($merchants, 200);
    }
}
