<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FackUsersModel;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FackUsersController extends Controller
{
    /*
    FackUsersController : controleur pour la gestion des faux utilisateurs incarnés par les animatrices
    */

    public function showProfileFakeUser($fake_user_id) {
        $fake_user_profile = FackUsersModel::where(['id' => $fake_user_id])
                            ->first(['username', 'age', 'picture', 'description']);
        if (!$fake_user_profile) {
            // throw new Exception('La personne n\'existe pas');
            return 'La personne n\'existe pas';
        }
        return response([
            'fake_user_profile' => $fake_user_profile
        ]);
    }


    public function fakeUsersList() {
        return FackUsersModel::all(['username', 'age', 'picture', 'description']);
    }

    public function createFakeUser(Request $request) {
        $user_id            = auth()->user()->id;
        $validate_username  = $request->validate([
            'username' => 'required'
        ]);

        $is_admin           = User::get_admin_user($user_id);
        if (!$is_admin) {
            throw new Exception('Vous ne pouvez pas créer un avatar');
        }
        if (!$validate_username) {
            throw new Exception('Formulaire invalide, erreur de création du faux_utilisateur');
        }
        $fake_user  = FackUsersModel::create([
            'created_by'        => $user_id,
            'username'          => $validate_username['username'],
            'age'               => $request->age,
            'picture'           => $request->picture,
            'description'       => $request->description,
        ]);
        return response([
            'fake_user' => $fake_user
        ]);
    }

}
