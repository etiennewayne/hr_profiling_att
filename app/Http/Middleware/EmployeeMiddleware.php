<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;


class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if($user->is_approve === 0){
            return redirect('/pending');
        }


        if(strtolower($user->role) === 'employee'){
            return $next($request);
        }

        abort(403);
    }
}
