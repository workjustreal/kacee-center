<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageEmployee
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
        if ((Auth::user()->isAdmin() || Auth::user()->roleAdmin() || Auth::user()->roleHR()) && !Auth::User()->hrReadonly()) {
            return $next($request);
        } else {
            return redirect('home')->with('error', "Only manage employee can access!");
        }
    }
}