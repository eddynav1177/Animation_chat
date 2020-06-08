<?php

namespace App\Http\Controllers\Api;

use App\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

class RegisterController extends Controller
{
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

    public function userRegister(Request $request) {
        return $this->register_all($request, 0);
    }

    public function animatorRegister(Request $request) {
        return $this->register_all($request, 1);
    }
}
