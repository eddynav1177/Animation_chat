<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class FackUsersModel extends Model
{
    //
    protected $table='fake_users';

    protected $fillable = [
        'id_user', 'username', 'age', 'picture', 'description'
    ];

    public static function get_fack_users_by_user($id_user) {

        $fake_user_list     = FackUsersModel::where(['id_user' => $id_user])->pluck('id');
        $string_to_replace  = ["[","]","\""];
        $fake_users         = str_replace($string_to_replace, '', $fake_user_list);
        $fake_users         = explode(',', $fake_users);

        return $fake_users;

    }
}
