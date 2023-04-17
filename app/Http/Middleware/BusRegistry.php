<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusRegistry
{
    public function handle(Request $request, Closure $next)
    {
        $std = Auth::user()->ownerable;
        if ($std->bus_registry === true) {
            return $next($request);
        } else {
            return redirect()->route('logout1');
        }
    }
}
