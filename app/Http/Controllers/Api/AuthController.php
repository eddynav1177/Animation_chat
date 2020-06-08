<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    /*
    AuthController: Controleur pour l'authentification en utilisant passport
    */

    private function login_all(Request $request, $is_admin='') {

        $login_data = $request->validate([
            'email'     => 'email|required',
            'password'  => 'required',
        ]);

        if (!auth()->attempt($login_data)) {
            return response(['message' => 'Invalid login or password']);
        }

        $access_token = auth()->user()->createToken('authToken')->accessToken;

        if (!empty($is_admin)) {
            $users  = User::where(['isonline' => 1, 'is_admin' => 0])->get();
        } else {
            $users  = User::where(['isonline' => 1])->get();
        }

        // Update users status to online
        auth()->user()->update(['isonline' => 1]);

        return response([
            'user'                  => auth()->user(),
            'access_token'          => $access_token
        ]);

    }

    public function userLogin(Request $request) {
        return $this->login_all($request);
    }

    public function animatorLogin(Request $request) {
        return $this->login_all($request, 1);
    }

    public function logout(Request $request, $id) {
        Auth::logout();
        if (!empty($id)) {
            auth()->user()->update(['isonline' => 0]);
        }
    }

}
