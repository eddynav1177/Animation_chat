<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MessagesModel;
use App\User;
use App\Http\Controllers\Api\UsersController;
use Auth;
use App\Http\Controllers\Api\AuthController;
use App\Events\MessagesEvent;
use App\Events\NewMessageEvent;
use Carbon\Carbon;

class MessagesController extends Controller
{
    /*
    MessagesController: Controller pour la gestion des messages envoyés par les client et/ou les animatrices
    */

    public function __construct() {
        $this->middleware('auth');
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
                                // Verification si l'animatrice est connectée
                                $destination = $status_destinataire->id;
                            } else {
                                // Sinon, envoyer le message à une autre animatrice connectée
                                $get_first_animator_connected   = User::whereRaw('isonline = 1 AND is_admin = 1')->orderByRaw('id')->first();
                                $destination                    = $get_first_animator_connected->id;
                            }

                            // Verification de la derniere date d'envoi d'un message
                            $status_message_destinataire = $this->verificationMessagesStatusByUsers($request, $destination);
                            $status_message_destinataire = $status_message_destinataire->original['last_message'];
                            $status_message_destinataire = (!empty($status_message_destinataire)) ? true : false;

                            $message           = MessagesModel::create([
                                'title'         => $request->title,
                                'content'       => $request->content,
                                'sender'        => $id_user,
                                'destination'   => $destination,
                                // 'read_at'       => Carbon::now()->toDateTimeString(),
                            ]);

                            $status_message = $this->verificationMessagesStatusByUsers($request, $destination);
                            $status_message = $status_message->original['last_message'];

                            if ($message) {
                                // Envoi des events vers pusher
                                broadcast(new NewMessageEvent($message))->toOthers();

                                return response([
                                    'message'               => $message,
                                    'statut_destination'    => $status_message_destinataire,
                                    'status_message'        => $status_message,
                                    'user'                  => auth()->user()
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
            $user_status    = new UsersController;
            $user_status    = $user_status->get_status_user($id_user);

            if ($id_user != $destination) {
                if (!empty($user_status)) {
                    $status_message = $this->verificationMessagesStatusByUsers($request, $destination);
                    $status_message = ($status_message) ? $status_message->original['last_message'] : '';
                    $messages       = MessagesModel::whereRaw("(sender = $id_user AND destination = $destination) OR ( sender = $destination AND destination = $id_user)")->pluck('content', 'created_at');
                    $messages       = ($messages->count() > 0) ? $messages : 'Aucun message';
                    return response([
                        'messages'          => $messages,
                        'status_message'    => $status_message,
                        'user'              => auth()->user(),
                    ]);
                }
            }
        }

    }

    public function verificationMessagesStatusByUsers(Request $request, $id_user) {

        if (Auth::check()) {
            $user_status = new UsersController;
            $user_status = $user_status->get_status_user($id_user);

            if (!empty($user_status)) {
                //Verification de la date d'envoi du dernier message
                $last_message = MessagesModel::whereRaw('(sender = ' . $id_user . ' OR destination = ' . $id_user . ') AND created_at BETWEEN (NOW() - INTERVAL 30 MINUTE) AND (NOW() + INTERVAL 30 MINUTE) ORDER BY created_at DESC')->first();

                $last_message = (!empty($last_message)) ? $last_message->id : '';
                return response([
                    'last_message' => $last_message
                ]);
            }
        }

    }

    public function viewConversation(Request $request) {

        // TODO: à décommenter et retirer la récupération de l'user connecté via la méthode get_status_user de UsersController
        if (Auth::check()) {
            $id_user        = auth()->user()->id;
            $user_status    = new UsersController;
            $user_status    = $user_status->get_status_user($id_user);

            if (!empty($user_status)) {
                $status_message         = $this->verificationMessagesStatusByUsers($request, $id_user);
                // Eviter d'envoyer dans la liste, la conversation contenant soit-même
                $conversation_message   = MessagesModel::whereRaw('(sender = ' . $id_user . ' AND destination != ' . $id_user . ') GROUP BY sender, destination ORDER BY destination')->get();
                $conversation_message   = ($conversation_message) ? $conversation_message->pluck(['destination']) : 0;

                return response([
                    'conversation'     => $conversation_message,
                    'status_message'   => $status_message->original['last_message'],
                ]);
            }
        }

    }
}
