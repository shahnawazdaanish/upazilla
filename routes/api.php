<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('merchant-webhook', 'API\WebHookController@addWebHookPayment');

Route::get('/stores', function() {
   return \Illuminate\Support\Facades\Response::json(\App\Store::query()->select('id', 'name')->get(), 200);
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'API\AuthController@login');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'API\AuthController@logout');
        Route::get('user/me', 'API\AuthController@user');
        Route::get('user/merchant/credentials', 'API\AuthController@getUserMerchantCredentials');
        Route::post('user/merchant/change_credentials', 'API\AuthController@changeMerchantCredentials');
        Route::resource('users', 'API\MerchantUserController');
        Route::post('user/change_password', 'API\AuthController@changePassword');
    });
});

Route::group([
    'prefix' => 'payment',
    'middleware' => ['auth:api']
], function () {
    Route::get('search/{reference}', 'API\PaymentController@search');
    Route::put('update/{id}', 'API\PaymentController@update');

    Route::get('missed/{reference}', 'API\PaymentController@missed');
    Route::get('all', 'API\PaymentController@allPayments');
    Route::post('download_report', 'API\PaymentController@downloadReport');
});

Route::group([
    'prefix' => 'payment/admin',
    'middleware' => ['auth:admin_api']
], function () {
    Route::get('search/{reference}', 'API\PaymentController@search');
    Route::put('update/{id}', 'API\PaymentController@update');

    Route::get('missed/{reference}', 'API\PaymentController@missed');
    Route::get('all', 'API\PaymentController@allPayments');
    Route::post('download_report', 'API\PaymentController@downloadReport');
});



/*
 * Admin Routes
 *
 * All the routes are related with Admin activities
 */

Route::group([
    'prefix' => 'admin'
], function () {

    Route::post('login', 'API\AdminController@login');
    Route::get('logout', 'API\AdminController@logout');

    Route::group([
        'prefix' => '/',
        'middleware' => ['auth:admin_api']
    ], function(){

        Route::post('/sync', 'API\AdminController@sync');

        Route::get('user/me', 'API\AdminController@user');
        Route::post('user/change_password', 'API\AdminController@changePassword');


        Route::apiResources([
            'roles' => \API\RoleController::class,
            'permissions' => \API\PermissionController::class,
            'merchants' => \API\MerchantController::class,
            'measurements' => \API\CommonController::class,
            'inventories' => \API\CommonController::class,
            'stores' => \API\CommonController::class,
            'products' => \API\CommonController::class,
            'admins' => \API\AdminController::class,
            'users' => \API\UserController::class,
        ]);

        /*Route::resource('roles', 'API\RoleController');
        Route::resource('permissions', 'API\PermissionController');
        Route::resource('merchants', 'API\MerchantController');
        Route::resource('measurements', 'API\MeasurementController');
        Route::resource('stores', 'API\StoreController');
        Route::resource('admins', 'API\AdminController');
        Route::resource('users', 'API\UserController');*/

        Route::get('utility/get_units', 'API\UtilityController@getUnits');

        Route::get('payment/all', 'API\PaymentController@allPayments');
        Route::post('download_report', 'API\PaymentController@downloadReport');


        Route::get('get_admin_roles', 'API\RoleController@getAdminRoles');
    });
});

Route::group([
    'prefix' => 'combined/user',
    'middleware' => ['auth:api']
], function() {
    // CUSTOM ROUTES
    Route::get('get_user_roles', 'API\RoleController@getUserRoles');
    Route::get('get_user_merchant', 'API\UserController@getUserMerchants');
    Route::post('user/change_password', 'API\AdminController@changePassword');
});

Route::group([
    'prefix' => 'combined/admin',
    'middleware' => ['auth:admin_api']
], function() {
    // CUSTOM ROUTES
    Route::get('get_user_roles', 'API\RoleController@getUserRoles');
    Route::get('get_user_merchant', 'API\UserController@getUserAdminMerchants');
    Route::post('user/change_password', 'API\AdminController@changePassword');
});

Route::fallback(function(){
    return response()->json(['message' => 'Not Found.'], 404);
})->name('api.fallback.404');
