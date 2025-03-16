<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $userType)
    {
        // Check if the session variable 'userType' matches the required $userType
        if ($request->session()->get('userType') == $userType) {
            return $next($request); // Allow access to the route
        }

        // Redirect or deny access if the userType doesn't match
        return back()->with('msg', 'You do not have permission to access this page.');
    }
}
