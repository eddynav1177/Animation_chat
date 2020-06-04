<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagesModel extends Model
{
    //
    protected $table='messages';

    protected $fillable = [
        'title', 'content', 'sender', 'destination', 'read_at', 'id_conversation'
    ];

}
