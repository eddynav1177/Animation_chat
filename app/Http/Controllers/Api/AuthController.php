<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use HasApiTokens;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    /*
    AuthController: Controleur pour l'authentification en utilisant passport
    */
    public function loginAll(Request $request) {
        $login_data = $request->validate([
            'email'     => 'email|required',
            'password'  => 'required',
        ]);

        if (!auth()->attempt($login_data)) {
            return response(['message' => 'Invalid login or password']);
        }
        $current_user = auth()->user();
        $access_token = $current_user->createToken('authToken')->accessToken;

        $current_user->update(['isonline' => 1]);

        return response([
            'user'                  => $current_user,
            'access_token'          => $access_token,
            'token_type'            => 'Bearer',
        ]);
    }

    public function logout(Request $request) {
        auth()->user()->update(['isonline' => 0]);
        // Se déconnecter de tous les appareils
        DB::table('oauth_access_tokens')->where('user_id', Auth::user()->id)
        ->update(['revoked' => true]);
        // $request->user()->token()->revoke();
        return response([
            'user' => auth()->user()
        ]);
    }
}
