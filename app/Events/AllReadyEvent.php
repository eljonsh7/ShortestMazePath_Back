<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AllReadyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mazeId;

    /**
     * Create a new event instance.
     */
    public function __construct($mazeId)
    {
        $this->mazeId = $mazeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new Channel('multi-player.'. $this->mazeId);
    }

    public function broadcastWith()
    {
        return [
            //
        ];
    }

    public function broadcastAs()
    {
        return 'AllUsersReady';
    }
}
