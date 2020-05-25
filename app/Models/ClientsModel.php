<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ClientsModel extends Authenticatable
{
    //
    use Notifiable;
    
    protected $guard    = "super_clients";
    protected $table    = "super_clients";
    protected $fillable = ["name", "email", "password"];
    protected $hidden   = ["password", "remember_token"];
    

}
