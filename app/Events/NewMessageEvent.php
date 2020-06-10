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

    public $message;
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
            'sender_id'     => $this->message->sender_id,
            'recipient_id'  => $this->message->recipient_id,
            'user_id'       => $this->message->user_id,
            'body'          => $this->message->body,
            'read'          => $this->message->read,
            'user'          => [
                'id'    => $this->message->sender
            ],
        ];
    }

    public function broadcastOn()
    {
        // return new PrivateChannel('chat.'.$this->message->id);
        return new PrivateChannel('chat');
    }

    public function broadcastAs() {
        return 'message-'.$this->message->id.' sended';
    }
}
