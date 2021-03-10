<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MerchantUserController extends Controller
{

    /**
     * List all users
     * @param Request $request
     * @return JsonResponse
     * */
    function index(Request $request)
    {
        /*$hasAccess = $this->checkPermission('users.view');
        if(!$hasAccess) {
            return response()->json('Access Denied', 403);
        }*/
        if ( !request()->user()->hasAnyPermission(['users.list','users.view']) ) {
            return response()->json($this->accessDenied, 403);
        }

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
            }])->authorized();

            // Provide based on paging or not
            if ($isPaging) {
                $user = $user->paginate();
            } else {
                $user = $user->get();
            }

            // If permission has data
            if ($user) {
                return response()->json($user);
            } else {
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
        if ( !request()->user()->hasAnyPermission(['users.create','users.store']) ) {
            return response()->json($this->accessDenied, 403);
        }

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
                    return response()->json($user, 201);
                } else {
                    return response()->json("User creation fails, Role can't be assigned", 422);
                }
            } else {
                return response()->json("User creation fails, DB issue", 422);
            }
        } catch (\Exception $e) {
            Log::error($e);
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
        if ( !request()->user()->hasAnyPermission(['users.update','users.edit']) ) {
            return response()->json($this->accessDenied, 403);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|confirmed|min:6',
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
                $user->save();
                if ($user) {
                    $user->syncRoles([$request->input('role.id')]);

                    $finaluser = User::query()->whereId($user->id)->api()->with(['roles' => function ($query) {
                        $query->select('id', 'name');
                    }, 'merchant' => function ($query) {
                        $query->select('id', 'name');
                    }])->first();

                    return response()->json($finaluser, 200);
                } else {
                    return response()->json("Could not update the permission", 417);
                }
            } else {
                return response()->json("Permission cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
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
        if ( !request()->user()->can('users.delete') ) {
            return response()->json($this->accessDenied, 403);
        }
        try {
            $user = User::query()->find($id);
            if ($user) {
                $delete = $user->delete();
                if ($delete) {
                    return response()->json("Successfully deleted", 200);
                } else {
                    return response()->json("Could not delete the user", 417);
                }
            } else {
                return response()->json("User cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            Log::error($e);
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
        $merchants = Merchant::query()->where('status', 'ACTIVE')->select(['id', 'name'])->get();
        return response()->json($merchants, 200);
    }
}
