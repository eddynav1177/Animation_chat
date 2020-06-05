<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagesModel extends Model
{
    //
    protected $table='messages';

    protected $fillable = [
        'sender_id', 'recipient_id', 'body', 'spamscore', 'status', 'read', 'sent_from', 'moderated_at', 'fack_user_id', 'conversation_id'
    ];

}
