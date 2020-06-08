<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AnimateursModel extends Authenticatable
{
    //
    use Notifiable;

    protected $table    = "animateurs";
    protected $fillable = ["name", "email", "password"];
    protected $hidden   = ["password", "remember_token"];
}
