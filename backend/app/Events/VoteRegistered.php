<?php

namespace App\Events;

use App\Models\Poll;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteRegistered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $poll;

    // Create a new event instance.
    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    // Get the channels the event should broadcast on.
    public function broadcastOn(): array
    {
        return [
            new Channel('poll.' . $this->poll->id),
        ];
    }

    // The event's broadcast name.
    public function broadcastAs(): string
    {
        return 'vote.registered';
    }
}
