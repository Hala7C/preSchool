<?php

namespace App\Http\Middleware;

use App\Models\Bus;
use Closure;
use Illuminate\Http\Request;

class BusExist
{

    public function handle(Request $request, Closure $next)
    {
        $busCount=Bus::all()->count();
        if($busCount !=0){
            return $next($request);
        }else{
            return redirect()->back()->withErrors(['msg'=>'there is no buses yet to run script']);
        }
    }
}
