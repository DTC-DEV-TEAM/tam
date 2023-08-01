<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\OrderSchedule;
use DB;
use CRUDBooster;

class CheckOrderSchedule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $current_date = Carbon::now();
        $end_schedule = Carbon::now();
        $current_schedule = OrderSchedule::where('status','ACTIVE')->orderBy('id','desc')->first();
        //$current_schedule = OrderSchedule::where('status','ACTIVE')->whereDate('start_date','<=',$current_date)->whereDate('end_date','>=',$current_date)->orderBy('id','desc')->first();
        
        $privileges_list = array_map('intval',explode(",",$current_schedule->privilege_id)); //additional code 20200624
        
        if($current_date->lte($current_schedule->end_date)) {
		    if($current_schedule->period == "HOUR") {
		        $end_schedule = Carbon::parse($current_schedule->end_date)->subHours($current_schedule->time_unit);
		    }
		    else {
		        $end_schedule = Carbon::parse($current_schedule->end_date)->subDays($current_schedule->time_unit);
		    }
			
		}

        //if($current_date->between(Carbon::parse($current_schedule->start_date), Carbon::parse($current_schedule->end_date))){
        if($current_schedule){
            if($current_date->between(Carbon::parse($current_schedule->start_date), $end_schedule)) {
                if(empty($privileges_list) || in_array(CRUDBooster::myPrivilegeId(), $privileges_list) || $privileges_list['0'] == 0) { //additional code 20200624
                    return $next($request);
                }
                else {
                    return response()->view('errors.add-service-unavailable');
                }
            }
        }
        // if(empty($privileges_list) || in_array(CRUDBooster::myPrivilegeId(), $privileges_list) || $privileges_list['0'] == 0) { //additional code 20200624
        //     return $next($request);
        // }
        // else {
        //     return response()->view('assets.add-service-unavailable');
        // }

        return response()->view('errors.add-service-unavailable');
        
    }
}
