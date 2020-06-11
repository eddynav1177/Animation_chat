<?php

namespace App;

use Cache;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Auth;
use Exception;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'isonline', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function get_users_connected($user_id, $is_admin = '') {
        // Lister les autres users connectés à part l'user en question
        if (!empty($is_admin)) {
            $users  = User::where(['isonline'=> 1, 'is_admin'=> 0])->where('id', '!=', $user_id)->pluck('id');
        } else {
            $users  = User::where(['isonline' => 1])->where('id', '!=', $user_id)->pluck('id');
        }
        if (!$users) {
            return ;
        }
        $str_to_replace     = ["[","]","\""];
        $users              = str_replace($str_to_replace, '', $users);
        $users              = explode(',', $users);

        return $users;
    }

    public static function get_admin_user($user) {
        $is_admin = User::where(['is_admin' => 1, 'id' => $user])->first(['id']);
        if (!$is_admin) {
            return;
        }
        return $is_admin;
    }

    public static function get_first_animator_connected() {
        $current_user               = auth()->user();
        $first_animator_connected   = User::where(['isonline' => 1, 'is_admin' => 1])
                                    ->where('id', '<>', $current_user->id)
                                    ->orderBy('id')
                                    ->first();
        $destination                = $first_animator_connected->id;
        return $destination;
    }

    public static function users_is_admin ($destination) {
        $current_user           = auth()->user();
        $is_admin_destination   = User::get_admin_user($destination);
        if (!$is_admin_destination && !empty($current_user->is_admin)) {
            return $destination;
        }

        $status_destinataire    = User::where(['isonline' => 1, 'id' => $destination])
                                ->first();
        if ($status_destinataire) {
            // Verification si l'animatrice est connectée
            $destination = $status_destinataire->id;
        }
        $destination            = User::get_first_animator_connected();
        return $destination;

    }

}
