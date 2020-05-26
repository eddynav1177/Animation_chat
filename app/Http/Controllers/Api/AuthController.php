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
    public function register(Request $request) {

        $validate_data = $request->validate([
            'name'      => 'required|max:255',
            'email'     => 'email|required|unique:users',
            'password'  => 'required|confirmed',
        ]);

        $validate_data['password'] = bcrypt($request->password);

        $user           = User::create($validate_data);
        $access_token   = $user->createToken('authToken')->accessToken;

        return response([
            'user'          => $user,
            'access_token'  => $access_token
        ]);

    }

    public function login(Request $request) {
        $login_data = $request->validate([
            'email'     => 'email|required',
            'password'  => 'required',
        ]);

        if (!auth()->attempt($login_data)) {
            return response(['message' => 'Invalid login or password']);
        }

        // $value = session('key', 'default');

        $access_token = auth()->user()->createToken('authToken')->accessToken;
        if (Auth::check()) {
            $data = new User;
            $data = $data->getUsersConnected();
            return response([
                'user'          => auth()->user(),
                'access_token'  => $access_token,
                'data'          => $data
            ]);
        }
    }

    public function redirectToHome() {

    }
}
