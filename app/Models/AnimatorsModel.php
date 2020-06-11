<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimatorsModel extends Model
{
    //
    protected $table    = "animators";
    protected $fillable = ["name", "email", "password"];
    protected $hidden   = ["password", "remember_token"];
}
