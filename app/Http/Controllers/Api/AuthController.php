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
        $user = auth()->user();
        $access_token = $user->createToken('authToken')->accessToken;

        // Update users status to online
        $user->update(['isonline' => 1]);

        return response([
            'user'                  => auth()->user(),
            'access_token'          => $access_token,
            'token_type'            => 'Bearer',
        ]);

    }

    /*public function userLogin(Request $request) {
        return $this->loginAll($request);
    }

    public function animatorLogin(Request $request) {
        return $this->loginAll($request, 1);
    }*/

    public function logout(Request $request) {

        if (Auth::check()) {
            auth()->user()->update(['isonline' => 0]);
            // Auth::logout();
            // Se déconnecter de tous les appareils
            DB::table('oauth_access_tokens')->where('user_id', Auth::user()->id)
            ->update(['revoked' => true]);
            // $request->user()->token()->revoke();
            return response([
                'user' => auth()->user()
            ]);
        }

    }
}
