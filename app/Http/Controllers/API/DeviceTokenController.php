<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DeviceTokenController extends Controller
{
    //
    public function store(Request $request)
    {
        $user = Auth::guard("sanctum")->user();
        $user_device = User::where("id", $user->id)->first();
        $exists = $user_device->deviceTokens()
            ->where("token", "=", $request->post("token"))
            ->exists();
        if (!$exists) {
            $user_device->deviceTokens()->create([
                "token"  => $request->post("token"),
                "device" => $request->post("device"),
            ]);
        }
        $message = [" Token saved successfuly :)"];
        return response()->json($message, 201);
    }
}
