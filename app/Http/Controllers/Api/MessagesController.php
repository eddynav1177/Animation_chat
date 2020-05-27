<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MessagesModel;
use App\User;
use App\Http\Controllers\Api\UsersController;
use Auth;
use App\Events\MessagesEvent;

class MessagesController extends Controller
{
    /*
    MessagesController: Controleur pour la gestion des messages envoyés par les client et/ou les animatrices
    */
    //
    public function viewMessage() {
        // return view('message/viewmessage');
    }

    public function sendMessage(Request $request, $destination) {

        if (Auth::check()) {
            $id_user    = auth()->user()->id;

            // Envoyer un message uniquement si l'id_user est différent de la destination
            if ($id_user != $destination) {
                // Liste de tous les utilisateurs connectés pour l'envoi des messages
                $list_users = new UsersController;
                $list_users = $list_users->listUsersConnected($id_user);
                $list_users = $list_users->original['all_users_connected'];
                $list_users = json_decode(json_encode($list_users), true);

                if (count($list_users) > 0) {
                    if (in_array($destination, $list_users)) {
                        $validate_contenu = $request->validate([
                            'content' => 'required'
                        ]);

                        if ($validate_contenu) {
                            $status_destinataire    = User::whereRaw('isonline = 1 AND id = ' . $destination)->first();
                            if ($status_destinataire->count() > 0) {
                                // Verification si le destinataire est connecté
                                $destination = $status_destinataire->id;
                            } else {
                                // Sinon, envoyer le message à une autre animatrice connectée
                                $get_first_animator_connected   = User::whereRaw('isonline = 1 AND is_admin = 1')->orderByRaw('id')->first();
                                $destination                    = $get_first_animator_connected->id;
                            }

                            $message           = MessagesModel::create([
                                'title'         => $request->title,
                                'content'       => $request->content,
                                'sender'        => $id_user,
                                'destination'   => $destination,
                            ]);

                            // event(new MessagesEvent($request->content));

                            if ($message) {
                                return response([
                                    'message' => $message
                                ]);
                            }
                        }
                    }
                }
            }
        }

    }

    public function viewAnimatriceMessages(Request $request, $id_animatrice) {

        // TODO: à décommenter et retirer la récupération de l'user connecté via la méthode get_status_user de UsersController
        // if (Auth::check()) {

        // }

        $animatrice_status = new UsersController;
        $animatrice_status = $animatrice_status->get_status_user($id_animatrice);

        if (!empty($animatrice_status)) {
            $messages  = MessagesModel::whereRaw('sender = ' . $id_animatrice . ' OR destination = ' . $id_animatrice)->get();
            $messages  = $messages->pluck('id');
            return response([
                'messages'   => $messages
            ]);
        }
    }

    public function sendMessageUserByUsers() {

    }

    public function chatByUser() {

    }
}
