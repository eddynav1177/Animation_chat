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

    public function __construct() {
        $this->middleware('auth');
    }

    public function listUsersConnected($id_user) {
        if (Auth::check()) {
            return User::get_users_connected($id_user);
        }
    }

    public function listAnimatorsConnected($id_animator) {
        if (Auth::check()) {
            return User::get_users_connected($id_animator, 1);
        }
    }
}
