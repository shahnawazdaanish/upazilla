<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionGuardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param $permission
     * @param $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $permission, $guard="")
    {
        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        if(!empty($guard)) {
            $guards = is_array($guard)
                ? $guard
                : explode('|', $guard);
        }

        foreach ($permissions as $permission) {
            if(isset($guards) && !empty($guards)) {
                foreach ($guards as $guard) {
                    if (app('auth')->user()->hasPermissionTo($permission, $guard)) {
                        return $next($request);
                    }
                }
            } else {
                if (app('auth')->user()->can($permission)) {
                    return $next($request);
                }
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
