<?php

namespace App\Events;

use App\Models\BusTrack;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusLocationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lat;

    public $lng;

    protected $busTrack;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BusTrack $busTrack, $lat, $lng)
    {
        $this->busTrack = $busTrack;
        $this->lat = (float) $lat;
        $this->lng = (float) $lng;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('busTrack' . $this->busTrack->bus_id);
    }

    public function broadcastWith()
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
