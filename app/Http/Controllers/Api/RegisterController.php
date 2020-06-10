<?php

namespace App\Http\Controllers\Api;

use App\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    private function registerAll(Request $request, $is_admin) {

        $validate_data = $request->validate([
            'name'      => 'required|max:255',
            'email'     => 'email|required|unique:users',
            'password'  => 'required|confirmed',
        ]);

        $validate_data['password'] = bcrypt($request->password);
        if (!$validate_data) {
            throw new Exception('Erreur de création de l\'utilisateur');
        }
        $user           = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $validate_data['password'],
            'isonline'  => 1,
            'is_admin'  => $is_admin,
        ]);
        $access_token   = $user->createToken('authToken')->accessToken;
        return response([
            'user'          => $user,
            'access_token'  => $access_token
        ]);

    }

    public function userRegister(Request $request) {
        return $this->registerAll($request, 0);
    }

    public function animatorRegister(Request $request) {
        return $this->registerAll($request, 1);
    }
}
