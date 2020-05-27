<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;

class UsersController extends Controller
{
    //
    /*
    UsersController : Controller pour tout type d'utilisateurs connectés
    */

    public function get_users_connected($id_user, $is_admin = '') {

        // Verifier si l'id_user est en statut online
        $user  = User::where(['isonline' => 1, 'id' => $id_user])->get();
        if ($user->count() > 0) {

            // Lister les autres users connectés à part l'user en question
            if (!empty($is_admin)) {
                $users  = User::whereRaw('isonline = 1 AND is_admin = 0 AND id <> ' . $id_user)->get();
            } else {
                // $users  = User::where(['isonline' => 1])->get();
                $users  = User::whereRaw('isonline = 1 AND id <> ' . $id_user)->get();
            }

            $data = $users->pluck('id');

            return response([
                'user'                  => auth()->user(),
                'all_users_connected'   => $data
            ]);
        }

    }

    public function listUsersConnected($id_user) {
        return $this->get_users_connected($id_user);
    }

    public function listAnimatorsConnected($id_animator) {
        return $this->get_users_connected($id_animator, 1);
    }

}
