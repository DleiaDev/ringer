<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessagesReceivedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messagesIDs;
    public $contactWhoSent;

    public function __construct($contactWhoSent, $messagesIDs)
    {
        $this->messagesIDs = $messagesIDs;
        $this->contactWhoSent = $contactWhoSent;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('messages.'.$this->contactWhoSent);
    }

    public function broadcastWith()
    {
        return [
          'contact_id' => auth()->user()->id,
          'messagesIDs' => $this->messagesIDs
        ];
    }

    public function broadcastAs()
    {
        return 'MessagesReceivedEvent';
    }
}
