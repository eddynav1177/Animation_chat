<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuthModel extends Model
{
    //
    protected $table = "users";
    protected $fillable = [
        'name', 'email', 'password'
    ];
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function getUsersConnected() {
        return Auth::user()->id;

        // return Cache::has('user-is-online-' . $this->id);

    }
}
