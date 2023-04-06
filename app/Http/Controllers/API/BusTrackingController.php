<?php

namespace App\Http\Controllers\API;

use App\Events\BusLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\BusTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusTrackingController extends Controller
{
    public function show($id)
    {
        $delivery = BusTrack::query()->select([
            'id',
            'bus_id',
            'lng',
            'lat'
        ])->where('id', $id)
            ->firstOrFail();
        return $delivery;
    }
    public function update(Request $request, BusTrack $busTrack)
    {
        $request->validate([
            'lng' => ['required', 'numeric'],
            'lat' => ['required', 'numeric']
        ]);
        $busTrack->update($request->all());

        event(new BusLocationUpdated($busTrack, $request->lat, $request->lng));
        return $busTrack;
    }
}
