<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$userTypes)
    {
        $loggedInUserType = $request->session()->get("userType");

        if (in_array($loggedInUserType, $userTypes)) {
            return $next($request);
        }

        return redirect()->route('home')->with('msg', 'Unauthorized access.');
    }
}
