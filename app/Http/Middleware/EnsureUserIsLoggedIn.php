<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the 'userType' session variable exists
        if (!$request->session()->has('userType')) {
            // Redirect to the login page or home page with an error message
            return redirect()->route("home")->with('msg', 'You must be logged in to access this page.');
        }

        // If the user is logged in, proceed to the next request
        return $next($request);
    }
}
