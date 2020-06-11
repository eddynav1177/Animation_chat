<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Exception;

class FackUsersModel extends Model
{
    //
    protected $table='fake_users';

    protected $fillable = [
        'created_by', 'username', 'age', 'picture', 'description'
    ];

}
