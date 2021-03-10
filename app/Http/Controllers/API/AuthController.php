<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        try {
            $credentials = request(['username', 'password']);
            if (!Auth::attempt($credentials, $request->get('remember'))) {
                return response()->json([
                    'message' => 'Unable to login! Please check your credentials'
                ], 401);
            }

            if (auth()->user()->status == 'ACTIVE') {
                if (auth()->user()->merchant->status == "ACTIVE") {
                    $user = $request->user();
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;

                    if ($request->remember) {
                        $token->expires_at = Carbon::now()->addWeeks(1);
                    }
                    $token->save();

                    return response()->json([
                        'token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'user_type' => 'user',
                        'expires_at' => Carbon::parse(
                            $tokenResult->token->expires_at
                        )->toDateTimeString()
                    ]);
                } else {
                    auth()->logout();
                    return response()->json([
                        'message' => 'Merchant is disabled'
                    ], 401);
                }
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
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json($this->experDifficulties, 500);
        }
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

    public function getUserMerchantCredentials()
    {
        if(auth()->user()->merchant) {
            return response()->json([
                'app_key' => auth()->user()->merchant->app_key,
                'app_secret' => auth()->user()->merchant->app_secret != '' ? 'EXISTS' : '',
                'username' => auth()->user()->merchant->bkash_username,
                'password' => auth()->user()->merchant->bkash_password != '' ? 'EXISTS' : ''
            ], 200);
        } else {
            return response()->json("Invalid merchant", 422);
        }
    }
    public function changeMerchantCredentials(Request $request)
    {
        $this->validate($request, [
            'app_key' => 'required',
            'username' => 'required'
        ]);

        if(auth()->user()->merchant) {
            $merchant = auth()->user()->merchant;
            if (!empty($request->get('app_key'))) {
                $merchant->app_key = strip_tags($request->get('app_key'));
            }
            if (!empty($request->get('app_secret')) && $request->get('app_secret') != 'EXISTS') {
                $merchant->app_secret = Crypt::encrypt(strip_tags($request->get('app_secret')));
            }
            if (!empty($request->get('username'))) {
                $merchant->bkash_username = strip_tags($request->get('username'));
            }
            if (!empty($request->get('password')) && $request->get('password') != 'EXISTS') {
                $merchant->bkash_password = Crypt::encrypt(strip_tags($request->get('password')));
            }

            $merchant->save();
            if($merchant) {
                return response()->json([
                    'app_key' => $merchant->app_key,
                    'app_secret' => $merchant->app_secret != '' ? 'EXISTS' : '',
                    'username' => $merchant->bkash_username,
                    'password' => $merchant->bkash_password != '' ? 'EXISTS' : ''
                ], 200);
            } else {
                return response()->json("Unable to update merchant credentials", 422);
            }
        } else {
            // invalid merchant
            return response()->json("Invalid merchant", 422);
        }

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

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse [json] user object
     */
    public function user(Request $request)
    {
        // return response()->json($request->user());

        $user = User::query()->select('id', 'name', 'username')->find($request->user()->id);
        if ($user) {
            $user['permissions'] = $user->getPermissionsViaRoles()->map(function ($perms) {
                return $perms->name;
            });
            $user['myroles'] = $user->getRoleNames()->map(function ($role) {
                return $role;
            });
        }
        unset($user->roles);
        return response()->json($user);
    }
}
