<?php

namespace App\Listeners;

use App\Events\EmployeeNotifi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class SendEmpNotifi
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\EmployeeNotifi  $event
     * @return void
     */
    public function handle(EmployeeNotifi $event)
    {
        $last = DB::table('vrp_history')->latest()->first();
        if (!$last) {
            // Do stuff if it doesn't exist.
            }else{
            $lastdate=$last->created_at;
            $lastModifiy = Carbon::parse($lastdate)->format("Y-m-d");
            $cuurent=Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
            if( Carbon::parse($cuurent)->gt($lastModifiy) ){
                 //send notification to employee
                }
            }
    }
}
