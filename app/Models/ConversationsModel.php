<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationsModel extends Model
{
    //
    protected $table    = 'conversations';

    protected $fillable = [
        'id_user', 'destination'
    ];

    /*public function user() {
        return $this->belongsTo(User::class);
    }*/

}
