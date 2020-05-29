<?php

namespace App\Events;

use App\Models\MessagesModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MessagesModel $message)
    {
        //
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastWith() {
        return [
            'id'            => $this->message->id,
            'sender'        => $this->message->sender,
            'destination'   => $this->message->destination,
            'title'         => $this->message->title,
            'read_at'       => $this->message->read_at,
            'content'       => $this->message->content,
            'user'          => [
                'id'    => $this->message->sender
            ],
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.'.$this->message->id);
    }

    public function broadcastAs() {
        return 'message.send';
    }
}
