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

    private function getFackUsersStatus($is_online) {
        $fack_users_affected = DB::table('fake_users')
                                ->join('users', 'fake_users.id_user', 'users.id')
                                ->where(['users.isonline' => $is_online, 'users.is_admin' => 1])
                                ->pluck('fake_users.id');
        if (!$fack_users_affected) {
            throw new Exception('Aucune animatrice connectée');
        }
        return $fack_users_affected;
    }

    public function showProfileFackUser($id_fc_user) {
        $fack_user_profile = FackUsersModel::where(['id' => $id_fc_user])
                            ->first(['username', 'age', 'picture', 'description']);
        if (!$fack_user_profile) {
            // throw new Exception('La personne n\'existe pas');
            return 'La personne n\'existe pas';
        }
        return response([
            'fack_user_profile' => $fack_user_profile
        ]);
    }

    public function getFackUsersAllAffected() {
        return $this->getFackUsersStatus(1);
    }

    public function getFackUsersAllNotAffected() {
        $id_user    = auth()->user()->id;
        $is_admin   = User::get_admin_user($id_user);
        if (!$is_admin) {
            throw new Exception('Vous n\'êtes pas connecté en tant qu\'admin');
        }
        return $this->getFackUsersStatus(0);
    }

    public function createFackUser(Request $request) {
        $id_user = auth()->user()->id;
        $validate_username  = $request->validate([
            'username' => 'required'
        ]);

        $is_admin           = User::get_admin_user($id_user);
        if (!$is_admin) {
            throw new Exception('Vous ne pouvez pas créer un avatar');
        }
        if (!$validate_username) {
            throw new Exception('Erreur de création du faux_utilisateur');
        }
        $fack_user  = FackUsersModel::create([
            'id_user'           => $id_user,
            'username'          => $validate_username['username'],
            'age'               => $request->age,
            'picture'           => $request->picture,
            'description'       => $request->description,
        ]);
        return response([
            'fack_user' => $fack_user
        ]);
    }

    public function chooseFackUserByAdmin(Request $request, $id_fc_user) {
        $id_user    = auth()->user()->id;
        // Vérifier que l'user est connecté en tant qu'admin
        $is_admin   = User::get_admin_user($id_user);
        if (!$is_admin) {
            throw new Exception('Vous n\'avez pas le droit de choisir l\'avatar, vous n\'êtes pas admin');
        }
        $fack_user = FackUsersModel::where(['id' => $id_fc_user])
                    ->first('id_user');
        if (!$fack_user) {
            throw new Exception('Le faux_utilisateur n\'existe pas');
        }
        $status_user = User::get_users_connected($fack_user);
        if ($status_user) {
            throw new Exception('Le faux_utilisateur séléctionné est déjà prix par une animatrice connectée');
        }
        // Si l'user n'est pas connecté, on pourra choisir le faux_user
        $current_fack_user = FackUsersModel::where(['id' => $id_fc_user])
                            ->update(['id_user' => $id_user]);
        return response([
            'fack_user' => $current_fack_user
        ]);
    }
}
