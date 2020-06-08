<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use HasApiTokens;

class AuthController extends Controller
{
    /*
    AuthController: Controleur pour l'authentification en utilisant passport
    */

    private function register_all(Request $request, $is_admin) {

        $validate_data = $request->validate([
            'name'      => 'required|max:255',
            'email'     => 'email|required|unique:users',
            'password'  => 'required|confirmed',
        ]);

        $validate_data['password'] = bcrypt($request->password);
        if ($validate_data) {
            $user           = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $validate_data['password'],
                'isonline'  => 1,
                'is_admin'  => $is_admin,
            ]);
            if ($user) {
                $access_token   = $user->createToken('authToken')->accessToken;

                return response([
                    'user'          => $user,
                    'access_token'  => $access_token
                ]);
            }
        } else {
            return response([
                'message' => 'invalid creation'
            ]);
        }

    }

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

    public function logout(Request $request) {

        if (Auth::check()) {
            auth()->user()->update(['isonline' => 0]);
            Auth::logout();
            /*$user = Auth::user()->token();
            return response(['user' => $user]);*/
        }

    }

    public function userRegister(Request $request) {
        return $this->register_all($request, 0);
    }

    public function animatorRegister(Request $request) {
        return $this->register_all($request, 1);
    }
}
