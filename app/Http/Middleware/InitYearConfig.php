<?php

namespace App\Http\Middleware;

use App\Models\YearConfig;
use Closure;
use Illuminate\Http\Request;

class InitYearConfig
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
        $config=YearConfig::all()->count();
        if($config !=0){
            return $next($request);
        }else{
            return response()->json(['data'=>'Enter fees config first !!','status'=>400],400);
        }
    }
}
