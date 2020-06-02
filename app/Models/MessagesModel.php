<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MessagesModel extends Model
{
    //
    protected $table='messages';

    protected $fillable = [
        'title', 'content', 'sender', 'destination', 'read_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
