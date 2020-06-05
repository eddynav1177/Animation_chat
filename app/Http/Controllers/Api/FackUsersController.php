<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FackUsersModel;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class FackUsersController extends Controller
{
    /*
    FackUsersController : controleur pour la gestion des faux utilisateurs incarnés par les animatrices
    */
    //

    public function __construct() {
        $this->middleware('auth');
    }

    private function getFackUsersStatus($is_online) {

        if (Auth::check()) {
            $fack_users_affected = DB::table('fack_users')->join('users', 'fack_users.id_user', 'users.id')->where(['users.isonline' => $is_online, 'users.is_admin' => 1])->pluck('fack_users.id');
            return $fack_users_affected;
        }

    }

    public function showProfileFackUser($id_fc_user) {

        if (Auth::check()) {
            $fack_user_profile = FackUsersModel::where(['id' => $id_fc_user])->first(['username', 'age', 'picture', 'description']);
            if ($fack_user_profile) {
                return response([
                    'fack_user_profile' => $fack_user_profile
                ]);
            }
        }

    }

    public function getFackUsersAllAffected() {
        return $this->getFackUsersStatus(1);
    }

    public function getFackUsersAllNotAffected() {

        $id_user    = auth()->user()->id;
        $is_admin   = User::get_admin_user($id_user);
        if ($is_admin) {
            return $this->getFackUsersStatus(0);
        }

    }

    public function createFackUser(Request $request, $id_user) {

        if (Auth::check()) {
            $validate_username  = $request->validate([
                'username' => 'required'
            ]);

            $is_admin           = User::get_admin_user($id_user);
            if ($is_admin) {
                if ($validate_username) {

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
            }
        }

    }

    public function chooseFackUserByAdmin(Request $request, $id_fc_user) {

        if (Auth::check()) {
            $id_user    = auth()->user()->id;

            // Vérifier que l'user est connecté en tant qu'admin
            $is_admin   = User::get_admin_user($id_user);
            if ($is_admin) {
                // Vérifier si le fack_user à sélectionner n'est pas utilisé par une animatrice déjà connectée
                $fack_user = FackUsersModel::where(['id' => $id_fc_user])->first('id_user');
                if ($fack_user) {
                    $status_user = User::get_users_connected($fack_user);
                    if (empty($status_user)) {
                        // Si l'user n'est pas connecté, on pourra choisir le faux_user
                        $current_fack_user = FackUsersModel::where(['id' => $id_fc_user])->update(['id_user' => $id_user]);
                        return response([
                            'fack_user' => $current_fack_user
                        ]);
                    }
                    return response([
                        'fack_user' => 'Faux utilisateur déjà affecté à une animatrice'
                    ]);
                }
            }
        }
    }

}
