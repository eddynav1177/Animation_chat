<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Exception;

class UsersController extends Controller
{
    /*
    UsersController : Controller pour tout type d'utilisateurs connectés
    */
    public function listUsersConnected() {
        $current_user_id        = auth()->user()->id;
        $list_users_connected   = User::get_users_connected($current_user_id);
        if (!$list_users_connected) {
            throw new Exception('Aucun client connecté');
        }
        return $list_users_connected;
    }

    public function listAnimatorsConnected() {
        $current_animator_id        = auth()->user()->id;
        $list_animators_connected   = User::get_users_connected($current_animator_id, 1);
        if (!$list_animators_connected) {
            throw new Exception('Aucune animatrice connectée');
        }
        return $list_animators_connected;
    }

    public function showUserProfile($user_id) {
        $user_profile = User::where(['id' => $user_id])->first(['name', 'isonline']);
        if (empty($user_profile)) {
            throw new Exception('Impossible d\'afficher le profile de l\'utilisateur');
        }
        return response([
            'user_profile' => $user_profile
        ]);
    }
}
