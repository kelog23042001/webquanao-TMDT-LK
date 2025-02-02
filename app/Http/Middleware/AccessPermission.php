<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AccessPermission
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
        if (Auth::user()->hasRole('admin')) {
            return $next($request);
        }
        return redirect('/dashboard');
        $actions =  Route::getCurrentRoute()->getAction();

        $roles = isset($actions['roles']) ? $actions['roles'] : null;

        if ($this->admin->hasRole($roles) || !$roles) {
            return $next($request);
        } else {
            return abort(401);
        }
    }
}
