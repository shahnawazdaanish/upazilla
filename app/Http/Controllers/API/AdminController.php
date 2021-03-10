<?php

namespace App\Http\Controllers\API;

use App\Admin;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin_api')->except(['login', 'logout']);
        $this->middleware('permission:admins.create,admin', ['only' => ['store']]);
        $this->middleware('permission:admins.update,admin', ['only' => ['update']]);
        $this->middleware('permission:admins.view,admin', ['only' => ['index']]);
        $this->middleware('permission:admins.delete,admin', ['only' => ['delete']]);
    }

    /**
     * Login admin and create token
     *
     * @param  [string] username
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] token
     * @return [string] token_type
     * @return [string] user_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        // Validate Request
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'store_id' => 'nullable|numeric',
            'remember' => 'boolean'
        ]);

        try {
            // Change the driver to session to check login
            $credentials = request(['username', 'password']);
            config(['auth.guards.admin_api.driver' => 'session']);

            // Check login details
            if (!Auth::guard('admin_api')->attempt($credentials, $request->get('remember'))) {
                // Login fails
                return response()->json([
                    'message' => 'Unable to login! Please check your credentials'
                ], 401);
            }

            // Store Checking
            if(!empty($request->get("store_id"))) {
                if(auth()->guard("admin_api")->user()->store_id == $request->get("store_id")) {
                    // pass
                } else {
                    return response()->json([
                        'message' => 'Unable to login! Please check your store'
                    ], 401);
                }
            }


            if (auth()->guard('admin_api')->user()->status == 'ACTIVE') {
                // If successfully logged in
                $user = auth()->guard('admin_api')->user();
                $tokenResult = $user->createToken('Admin Access Token');
                $token = $tokenResult->token;

                if ($request->remember) {
                    $token->expires_at = Carbon::now()->addWeeks(1);
                }
                $token->save();

                return response()->json([
                    'user' => $user,
                    'token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'user_type' => 'admin',
                    'store' => $user->store_id ?? 0,
                    'store_name' => $user->store->name ?? "None",
//                    'store_logo' => $user->store->logo ?? "None",
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ]);
            } else {
                auth()->logout();
                return response()->json([
                    'message' => 'User is disabled'
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return JsonResponse [string] message
     */
    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $request->user()->token()->revoke();
            }
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse [json] user object
     */
    public function user(Request $request)
    {
        $admin = Admin::query()->select('id', 'name', 'username')->find($request->user()->id);
        if ($admin) {
            $admin['permissions'] = $admin->getPermissionsViaRoles()->map(function ($perms) {
                return $perms->name;
            });
            $admin['myroles'] = $admin->getRoleNames()->map(function ($role) {
                return $role;
            });
        }
        unset($admin->roles);
        return response()->json($admin);
    }

    /**
     * Change Password
     * @return JsonResponse
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);

        // Validation passed
        try {
            $user = $request->user();
            if (Hash::check($request->get('old_password'), $user->password)) {
                $user->password = Hash::make($request->get('new_password'));
                $user->save();
                if ($user) {
                    return response()->json("Password changed successfully");
                } else {
                    return response()->json('Cannot update password now, Try again', 422);
                }
            } else {
                return response()->json("Your provided old password is incorrect", 422);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json($this->experDifficulties, 500);
        }
    }


    function index(Request $request)
    {
        $action = 'GET ADMINS';
        try {
            $this->lg('Showing admin list with pagination', 'info', $action);
            // Permission check
//        if (!$request->user()->can('admins.view','admin')) {
//            return response()->json($this->accessDenied, 403);
//        }

            $search = request()->get("search");
            $isPaging = request()->exists("page");

            $admins = Admin::query()->where('id', '>', 0);

            // If admin searches for any name
            if (!empty($search)) {
                $admins = $admins->where(function ($query) use ($search) {
                    return $query->where('name', $search)
                        ->orWhere('username', $search);
                });
            }


            // get role of this admin
            $admins = $admins->api()->with(['roles' => function ($query) {
                $query->select('id', 'name');
            }]);

            // Provide based on paging or not
            if ($isPaging) {
                $admins = $admins->paginate();
            } else {
                $admins = $admins->get();
            }

            // If permission has data
            if ($admins) {
                $this->lg('Admin list is shown to requester', 'info', $action, 200);
                return response()->json($admins);
            } else {
                $this->lg('Admins could not be found, admins: ' . json_encode($admins), 'warning', $action, 404);
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
        $action = "CREATE ADMIN";
        $request->merge([
            'username' => strip_tags(
                strtolower(preg_replace('/[^ \w-]/', '-', $request->get('username')))
            )
        ]);

        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:admin_users,username|min:4',
            'password' => 'required|confirmed|min:6',
            'role.name' => 'required|exists:roles,name,guard_name,admin'
        ]);

        DB::beginTransaction();
        try {
            $this->lg('Creating an admin', 'info', $action);

            $admin = new Admin();
            $admin->username = $request->get('username');
            $admin->name = $request->get('name');
            $admin->password = Hash::make($request->get('password'));
            $admin->store_id = auth()->guard('admin_api')->user()->store_id;
            $admin->save();

            if ($admin) {
                $assignRole = $admin->assignRole((int)$request->input('role.id'));
                if ($assignRole) {
                    DB::commit();
                    $this->lg('Admin created successfully, admin: ' . json_encode($admin), 'info', $action, 201);
                    return response()->json($admin, 201);
                } else {
                    $this->lg('User creation fails, Role can not be assigned now.', 'warning', $action, 422);
                    return response()->json("User creation fails, Role can't be assigned now.", 422);
                }
            } else {
                $this->lg('User creation fails, DB issue.', 'warning', $action, 422);
                return response()->json("User creation fails, DB issue", 422);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            DB::rollBack();
            return response()->json("User creation fails, " . $e->getMessage(), 422);
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
        $action = "UPDATING AN ADMIN";
        $request->validate([
            'password' => 'nullable|confirmed|min:6',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
            'role.name' => 'required|exists:roles,name,guard_name,admin'
        ]);

        try {
            DB::beginTransaction();
            $admin = Admin::query()->find($id);
            if ($admin) {
                if (!empty($request->get('name'))) {
                    $admin->name = $request->get('name');
                }
                if (!empty($request->get('password'))) {
                    $admin->password = Hash::make($request->get('password'));
                }
                if (!empty($request->get('status'))) {
                    $admin->status = $request->get('status');
                }
                $admin->save();
                if ($admin) {
                    $admin->syncRoles([$request->input('role.id')]);
                    DB::commit();

                    $this->lg('Updated admin successfully', 'info', $action, 200);
                    $finalAdminUser = Admin::query()->find($admin->id)->with('roles');
                    return response()->json($finalAdminUser, 200);
                } else {
                    $this->lg('Could not update the admin', 'info', $action, 422);
                    return response()->json("Could not update the admin", 422);
                }
            } else {
                $this->lg('Admin cannot be found by this ID', 'info', $action, 404);
                return response()->json("Admin cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            DB::rollBack();
            Log::error($e);
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
        $action = "DELETE ADMIN";
        try {
            $admin = Admin::query()->find($id);
            if ($admin) {
                $delete = $admin->delete();
                if ($delete) {
                    $this->lg('Successfully deleted', 'info', $action, 200);
                    return response()->json("Successfully deleted", 200);
                } else {
                    $this->lg('Could not delete the admin', 'info', $action, 422);
                    return response()->json("Could not delete the admin", 422);
                }
            } else {
                $this->lg('Admin cannot be found by this ID', 'info', $action, 404);
                return response()->json("Admin cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            Log::error($e);
            return response()->json($this->experDifficulties, 500);
        }
    }

    public function sync(Request $request) {
        $validation = Validator::make($request->all(), [
           'table' => 'required'
        ]);

        $store_id = request()->hasHeader("X-APP-STORE") ? request()->header("X-APP-STORE") : "";

        if($validation->fails()) {
            return response()->json("Table name is required", 422);
        }

        $isColExist = Schema::connection("mysql")->hasColumn($request->input("table"),'store_id');

        $query = DB::table($request->input("table"));
        if($isColExist){
            $query->where('store_id',$store_id);
        }

        $records = $query->get();

        return response()->json($records, 200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }
}
