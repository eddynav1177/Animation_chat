<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FackUsersModel extends Model
{
    //
    protected $table='fack_users';

    protected $fillable = [
        'id_user', 'username', 'age', 'picture', 'description'
    ];
}
