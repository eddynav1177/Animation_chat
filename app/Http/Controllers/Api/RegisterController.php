<?php

namespace App\Http\Controllers\Api;

use App\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private function registerAll(Request $request, $is_admin, $is_animator) {

        $validate_data = $request->validate([
            'name'      => 'required|max:255',
            'email'     => 'email|required|unique:users',
            'password'  => 'required|confirmed',
        ]);

        $validate_data['password'] = bcrypt($request->password);
        if (!$validate_data) {
            throw new Exception('Formulaire invalide, erreur de création de l\'utilisateur');
        }
        $user_created           = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => $validate_data['password'],
            'isonline'      => 1,
            'is_admin'      => $is_admin,
            'is_animator'   => $is_animator,
        ]);
        $access_token   = $user_created->createToken('authToken')->accessToken;
        return response([
            'user'          => $user_created,
            'access_token'  => $access_token
        ]);

    }

    public function userRegister(Request $request) {
        return $this->registerAll($request, 0, true);
    }

    public function animatorRegister(Request $request) {
        return $this->registerAll($request, 1, false);
    }
}
