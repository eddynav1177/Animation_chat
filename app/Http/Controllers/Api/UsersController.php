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
        $id_user = auth()->user()->id;
        $list_users_connected = User::get_users_connected($id_user);
        if (!$list_users_connected) {
            throw new Exception('Aucun client connecté');
        }
        return $list_users_connected;
    }

    public function listAnimatorsConnected() {
        $id_animator = auth()->user()->id;
        $list_users_connected = User::get_users_connected($id_animator, 1);
        if (!$list_users_connected) {
            throw new Exception('Aucune animatrice connectée');
        }
        return $list_users_connected;
    }

    public function showUserProfile($id_user) {
        $user = User::where(['id' => $id_user])->first(['name', 'isonline']);
        if (empty($user)) {
            throw new Exception('Impossible d\'afficher le profile de l\'utilisateur');
        }
        return response([
            'user' => $user
        ]);
    }
}
