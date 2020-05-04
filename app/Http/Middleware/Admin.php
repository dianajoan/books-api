<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

// adding the auth import for current user
use Auth;
// adding the resource for status codes
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // @TODO implement
        if (!Auth::user()) {
            return response()->json([
                'error'  => 'Unauthorized Access'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Auth::user()->is_admin) {
            return response()->json([
                'error'  => 'Restricted Access'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);       
    }
}
