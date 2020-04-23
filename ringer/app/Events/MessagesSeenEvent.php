<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessagesSeenEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $seenFrom;
    public $seenTo;

    public function __construct($seenFrom, $seenTo)
    {
        $this->seenFrom = $seenFrom;
        $this->seenTo = $seenTo;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('messages.'.$this->seenTo);
    }

    public function broadcastWith()
    {
        return ['contact_id' => $this->seenFrom];
    }

    public function broadcastAs()
    {
        return 'MessagesSeenEvent';
    }
}
