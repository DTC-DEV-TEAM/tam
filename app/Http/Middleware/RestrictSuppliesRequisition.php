<?php

namespace App\Http\Middleware;
use DB;
use Closure;
use Illuminate\Http\Request;

class RestrictSuppliesRequisition
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
        $usersPrivilege = DB::table('cms_privileges')->select('id')->whereNull('cannot_create')->get();
        
        if($usersPrivilege->isNotEmpty()){
            return $next($request);
        }else{
            return response()->view('assets.add-service-unavailable');
        }   
    }
}
