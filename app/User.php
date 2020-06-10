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

    public static function get_users_connected($id_user, $is_admin = '') {
        $user  = User::where(['isonline' => 1, 'id' => $id_user])->first();
        if (empty($user)) {
            throw new Exception('L\'utilisateur en question n\'est pas connectée');
        }
        // Lister les autres users connectés à part l'user en question
        if (!empty($is_admin)) {
            $users  = User::where(['isonline'=> 1, 'is_admin'=> 0])->where('id', '!=', $id_user)->pluck('id');
        } else {
            $users  = User::where(['isonline' => 1])->pluck('id');
        }
        $string_to_replace  = ["[","]","\""];
        $users              = str_replace($string_to_replace, '', $users);
        $users              = explode(',', $users);

        return $users;
    }

    public static function get_status_user($user) {

        $status = User::where(['isonline' => 1, 'id' => $user])->first();
        $status = (!empty($status->id)) ? $status->id : null;
        return $status;

    }

    public static function get_admin_user($user) {
        $is_admin = User::where(['is_admin' => 1, 'id' => $user])->first(['id']);
        return $is_admin;
    }

    public static function usersIsAdmin ($destination) {
        $user                   = auth()->user();
        $is_admin_destination   = User::get_admin_user($destination);
        if (!$is_admin_destination && !empty($user->is_admin)) {
            // throw new Exception('L\'user connecté et la destination sont des clients');
            return $destination;
        }

        $status_destinataire    = User::where(['isonline' => 1, 'id' => $destination])
                                ->first();
        if ($status_destinataire) {
            // Verification si l'animatrice est connectée
            $destination = $status_destinataire->id;
        }
        // Sinon, envoi du message à une autre animatrice connectée
        $first_animator_connected   = User::where(['isonline' => 1, 'is_admin' => 1])
                                    ->where('id', '<>', $user->id)
                                    ->orderBy('id')
                                    ->first();
        $destination                = $first_animator_connected->id;
        return $destination;

    }

}
