<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunicationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $name;
    public string $time;
    public $mazeId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $name, string $time, $mazeId)
    {
        $this->name = $name;
        $this->time = $time;
        $this->mazeId = $mazeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('multi-player.' . $this->mazeId),
        ];
    }

    public function broadcastWith()
    {
        return [
            'user_name' => $this->name,
            'time' => $this->time,
        ];
    }

    public function broadcastAs()
    {
        return 'UserFinished';
    }
}
