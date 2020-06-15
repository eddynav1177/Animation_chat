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
        'name', 'email', 'password', 'isonline', 'is_admin', 'is_animator'
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

    public static function user_is_online($user_id, $is_animator) {
        $user_connected = User::where(['isonline' => 1, 'id' => $user_id])
                            ->where(['is_animator' => $is_animator])
                            ->first(['isonline']);
        return $user_connected;
    }

    public static function get_users_connected($user_id, $is_animator = '') {
        // Lister les autres users connectés à part l'user en question
        if (!empty($is_animator)) {
            $users  = User::where(['isonline' => 1, 'is_animator' => $is_animator]);
        } else {
            $users  = User::where(['isonline' => 1]);
        }
        $users = $users->where('id', '!=', $user_id)->pluck('id');
        if (!$users) {
            return ;
        }
        $str_to_replace     = ["[","]","\""];
        $users              = str_replace($str_to_replace, '', $users);
        $users              = explode(',', $users);

        return $users;
    }

    public static function user_is_animator($user_id) {
        $is_animator = User::where(['is_animator' => 1, 'id' => $user_id])->first(['id']);
        if (!$is_animator) {
            return;
        }
        return $is_animator;
    }

    public static function get_first_animator_connected() {
        $current_user               = auth()->user();
        $first_animator_connected   = User::where(['isonline' => 1, 'is_animator' => 1])
                                    // ->where('id', '<>', $current_user->id)
                                    ->orderBy('id')
                                    ->first();
        if (!$first_animator_connected) {
            throw new Exception('Aucune animatrice connectée');
        }
        $destination                = $first_animator_connected->id;
        return $destination;
    }

    public static function assign_message_to_animator($destination) {
        $current_user           = auth()->user();
        $is_animator_destination   = User::user_is_animator($destination);
        if (!$is_animator_destination && !empty($current_user->is_animator)) {
            return $destination;
        }

        $recipient_status       = User::where(['isonline' => 1, 'id' => $destination])
                                ->first();
        if ($recipient_status) {
            // Verification si l'animatrice est connectée
            $destination = $recipient_status->id;
        }
        $destination            = User::get_first_animator_connected();
        return $destination;

    }

}
