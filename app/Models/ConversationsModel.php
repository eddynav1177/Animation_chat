<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationsModel extends Model
{
    //
    protected $table    = 'conversations';

    protected $fillable = [
        'id_user', 'id_destination'
    ];

}
