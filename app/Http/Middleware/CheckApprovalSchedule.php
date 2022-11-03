<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\OrderSchedule;
use CRUDBooster;

class CheckApprovalSchedule
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
        $current_schedule = OrderSchedule::where('status','ACTIVE')->orderBy('id','desc')->first();
        
        if($current_date->between(Carbon::parse($current_schedule->start_date), Carbon::parse($current_schedule->end_date))){
            return $next($request);
        }
        
        $data['page_title']='Order Approval';
        //update order schedule
        $order_update = OrderSchedule::where('status','ACTIVE')->update(['status'=>'INACTIVE']);
        \Log::info('Deactivate schedules: '.$order_update);
        return response()->view('errors.page-expired', $data);
    }
}
