<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationsModel extends Model
{
    //
    protected $table    = 'conversations';

    protected $fillable = [
        'user_id', 'destination_id', 'fake_user_id', 'animator_id'
    ];

}
