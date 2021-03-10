<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class MerchantController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin_api');
        $this->middleware('permission:merchants.create,admin', ['only' => ['store']]);
        $this->middleware('permission:merchants.update,admin', ['only' => ['update']]);
        $this->middleware('permission:merchants.view,admin', ['only' => ['index']]);
        $this->middleware('permission:merchants.delete,admin', ['only' => ['delete']]);
    }

    /**
     * Show all Merchants
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $action = "SHOW MERCHANTS";
        try {
            $search = request()->get("search");
            $guard_name = request()->get("guard_name");
            $isPaging = request()->exists("page");

            $merchant = Merchant::query();

            // If admin searches for any name
            if (!empty($search)) {
                $merchant = $merchant->whereNameOrSlugOrAccountNo($search, $search, $search);
            }

            // Provide based on paging or not
            if ($isPaging) {
                $merchant = $merchant
                    ->orderBy('id', 'desc')->paginate();
            } else {
                $merchant = $merchant
                    ->orderBy('id', 'desc')->get();
            }

            // If permission has data
            if ($merchant) {
                $this->lg('Merchant list shown', 'info', $action, 200);
                return response()->json($merchant);
            } else {
                $this->lg('Merchant list not found', 'warning', $action, 404);
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
        $action = "ADD MERCHANT";
        // Validating request
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string',
            'account_no' => 'required|string|unique:merchants',
//            'app_key' => 'required|string',
//            'app_secret' => 'required|string',
//            'bkash_username' => 'required|string',
//            'bkash_password' => 'required|string',
            'status' => 'required|string'
        ]);

        // Validation passed
        try {
            $merchant = new Merchant();
            $merchant->name = strip_tags($request->get('name'));
            $merchant->slug = strip_tags($request->get('slug'));
            $merchant->account_no = strip_tags($request->get('account_no'));
//            $merchant->app_key = strip_tags($request->get('app_key'));
//            $merchant->app_secret = Crypt::encrypt(strip_tags($request->get('app_secret')));
//            $merchant->bkash_username = strip_tags($request->get('bkash_username'));
//            $merchant->bkash_password = Crypt::encrypt(strip_tags($request->get('bkash_password')));
            $merchant->status = $request->get('status');

            $merchant->save();
            if ($merchant) {
                $this->lg('Merchant created successfully, merchant: ' . json_encode($merchant), 'info', $action, 201);
                return response()->json($merchant, 201);
            } else {
                $this->lg('Merchant cannot be added now, try contacting admin', 'warning', $action, 422);
                return response()->json('Merchant cannot be added now, try contacting admin', 422);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
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
        $action = "UPDATE MERCHANT";
        try {
            $merchant = Merchant::query()->find($id);
            if ($merchant) {
                if (!empty($request->get('name'))) {
                    $merchant->name = $request->get('name');
                }
                if (!empty($request->get('slug'))) {
                    $merchant->slug = $request->get('slug');
                }

                if (!empty($request->get('account_no'))) {
                    $merchant->account_no = $request->get('account_no');
                }
//                if (!empty($request->get('app_key'))) {
//                    $merchant->app_key = $request->get('app_key');
//                }
//                if (!empty($request->get('app_secret'))) {
//                    $merchant->app_secret = Crypt::encrypt($request->get('app_secret'));
//                }
//                if (!empty($request->get('bkash_username'))) {
//                    $merchant->bkash_username = $request->get('bkash_username');
//                }
//                if (!empty($request->get('bkash_password'))) {
//                    $merchant->bkash_password = Crypt::encrypt($request->get('bkash_password'));
//                }
                if (!empty($request->get('status'))) {
                    $merchant->status = $request->get('status');
                }
                $merchant->save();

                if ($merchant) {
                    $this->lg("Merchant information updated successfully", 'info', $action, 200);
                    return response()->json($merchant, 200);
                } else {
                    $this->lg("Update fails, contact admin", 'warning', $action, 422);
                    return response()->json("Update fails, contact admin", 422);

                }
            } else {
                $this->lg("Merchant information not found", 'warning', $action, 422);
                return response()->json("Merchant information not found", 404);
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
        $action = "DELETE MERCHANT";
        try {
            $merchant = Merchant::query()->find($id);
            if ($merchant) {
                $delete = $merchant->delete();
                if ($delete) {
                    $this->lg("Successfully deleted", 'info', $action, 200);
                    return response()->json("Successfully deleted", 200);
                } else {
                    $this->lg("Could not delete the merchant", 'warning', $action, 422);
                    return response()->json("Could not delete the merchant", 422);
                }
            } else {
                $this->lg("Merchant cannot be found by this ID", 'warning', $action, 404);
                return response()->json("Merchant cannot be found by this ID", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }
}
