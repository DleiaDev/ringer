<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageReceivedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contactWhoSent;
    public $message_id;

    public function __construct($contactWhoSent, $message_id)
    {
        $this->contactWhoSent = $contactWhoSent;
        $this->message_id = $message_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('messages.'.$this->contactWhoSent);
    }

    public function broadcastWith()
    {
        return [
          'contact_id' => auth()->user()->id,
          'message_id' => $this->message_id
        ];
    }

    public function broadcastAs()
    {
        return 'MessageReceivedEvent';
    }
}
