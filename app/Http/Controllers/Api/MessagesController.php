<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MessagesModel;
use App\User;
use Auth;
use App\Events\NewMessageEvent;
use App\Models\ConversationsModel;
use Carbon\Carbon;

class MessagesController extends Controller
{
    /*
    MessagesController: Controller pour la gestion des messages envoyés par les client et/ou les animatrices
    */

    public function __construct() {
        $this->middleware('auth');
    }

    private function verificationMessagesStatusByUsers($id_user) {

        if (Auth::check()) {
            $user_status = User::get_status_user($id_user);

            if (!empty($user_status)) {
                //Verification de la date d'envoi du dernier message
                $last_message = MessagesModel::where(['sender' => $id_user])->orWhere(['destination' => $id_user])->where('created_at', 'BETWEEN (NOW() - INTERVAL 30 MINUTE) AND (NOW() + INTERVAL 30 MINUTE)')->orderBy('created_at', 'desc')->first();

                $last_message = (!empty($last_message)) ? $last_message->id : '';
                return $last_message;
            }
        }

    }

    public function sendMessage(Request $request, $destination) {

        if (Auth::check()) {
            $id_user    = auth()->user()->id;

            // Envoi d'un message uniquement si l'id_user est différent de la destination
            if ($id_user != $destination) {
                // Liste de tous les utilisateurs connectés pour l'envoi des messages
                $list_users     = User::get_users_connected($id_user);

                if (count($list_users) > 0) {
                    if (in_array($destination, $list_users)) {
                        $validate_contenu = $request->validate([
                            'content' => 'required'
                        ]);

                        if ($validate_contenu) {
                            $status_destinataire    = User::where(['isonline' => 1, 'id' => $destination])->first();
                            if ($status_destinataire) {
                                // Verification si l'animatrice est connectée
                                $destination = $status_destinataire->id;
                            } else {
                                // Sinon, envoi du message à une autre animatrice connectée
                                $get_first_animator_connected   = User::where(['isonline' => 1, 'is_admin' => 1])->orderBy('id')->first();
                                $destination                    = $get_first_animator_connected->id;
                            }

                            // Vérification si la conversation existe déjà ou non
                            $conversation           = ConversationsModel::where(['id_user' => $id_user, 'destination' => $destination])->orWhere(['id_user' => $destination, 'destination' => $id_user])->first();

                            if (!empty($conversation)) {
                                $id_conversation        = $conversation->id;
                            } else {
                                // Création de la conversation
                                $create_conversation    = ConversationsModel::create([
                                    'id_user'       => $id_user,
                                    'destination'   => $destination,
                                ]);
                                $id_conversation        = $create_conversation->id;
                            }

                            $message                = MessagesModel::create([
                                'title'             => $request->title,
                                'content'           => $request->content,
                                'sender'            => $id_user,
                                'id_conversation'   => $id_conversation,
                                'destination'       => $destination,
                                // 'read_at'       => Carbon::now()->toDateTimeString(),
                            ]);

                            // Vérification de la derniere date d'envoi d'un message
                            $status_message         = $this->verificationMessagesStatusByUsers($destination);

                            if ($message) {
                                // Envoi des events vers pusher
                                broadcast(new NewMessageEvent($message))->toOthers();

                                return response([
                                    'message'           => $message,
                                    'id_status_message' => $status_message,
                                    'user'              => auth()->user()
                                ]);
                            }
                        }
                    }
                }
            }
        }

    }

    public function viewMessage(Request $request, $destination) {

        if (Auth::check()) {
            $id_user        = auth()->user()->id;

            if ($id_user != $destination) {
                $status_message = $this->verificationMessagesStatusByUsers($destination);
                $messages       = MessagesModel::where(['sender' => $id_user, 'destination' => $destination])->orWhere(['sender' => $destination, 'destination' => $id_user])->pluck('content', 'created_at');
                $messages       = ($messages) ? $messages : 'Aucun message';
                return response([
                    'messages'          => $messages,
                    'id_status_message' => $status_message,
                    'user'              => auth()->user(),
                ]);
            }
        }

    }

}
