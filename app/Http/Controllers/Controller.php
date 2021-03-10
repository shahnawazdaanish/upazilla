<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected $accessDenied = 'Access denied! You do not have proper permission to access this';
    protected $experDifficulties = 'There was an issue to serve your request, Try contacting admin';

    /*
     * Error Code
     * take errorCode and errorMessage to create an array
     *
     * @param string $errorCode
     * @param string $message
     * @return array
     * */
    function error(string $errorCode, string $message): array
    {
        return [
            "errorCode" => $errorCode,
            "errorMessage" => $message
        ];
    }

    /*
     * Check Permission
     *
     * @param string $permission
     * @return Boolean
     * */
    function checkPermission(string $permission): bool
    {
        try {
            $has = request()->user()->hasPermissionTo($permission);
            if(!$has) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Write Log
     *
     * @param string $message
     * @param string $level
     * @param string $action
     * @param string $type
     * @param mixed $extra
     * @param int $statusCode
     * @return void
     * */
    public function lg($message,$level='debug', $action="", $statusCode=0, $type='interface', $extra=[]) {
        Log::{$level}($message, ['action'=> $action, 'status_code' => $statusCode, 'type' => $type, 'extra' => $extra]);
    }
}
