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
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /*
    MessagesController: Controller pour la gestion des messages envoyés par les client et/ou les animatrices
    */

    public function __construct() {
        // $this->middleware('auth:api');
        $this->middleware('auth');
    }

    private function verificationMessagesStatusByUsers($id_user) {

        if (Auth::check()) {
            $user_status = User::get_status_user($id_user);

            if (!empty($user_status)) {
                //Verification de la date d'envoi du dernier message
                $last_message = MessagesModel::where(['sender_id' => $id_user])
                                ->orWhere(['recipient_id' => $id_user])
                                ->where('created_at', 'BETWEEN (NOW() - INTERVAL 30 MINUTE) AND (NOW() + INTERVAL 30 MINUTE)')
                                ->orderBy('created_at', 'desc')
                                ->first();

                $last_message = (!empty($last_message)) ? $last_message->id : '';
                return $last_message;
            }
        }

    }

    public function sendMessage(Request $request, $destination, $fk_user) {

        if (Auth::check()) {
            $user           = auth()->user();
            $id_user        = $user->id;
            // Envoi d'un message uniquement si l'id_user est différent de la fake_user
            if ($id_user != $destination) {
                $is_admin_destination   = User::get_admin_user($destination);

                // Envoyer message si destination n'est pas admin user n'est pas un admin
                if (!$is_admin_destination || empty($user->is_admin)) {
                    $fk_user                = (($is_admin_destination && $user->is_admin == 1) || (!$is_admin_destination && (empty($user->is_admin)))) ? 0 : $fk_user;
                    // Liste de tous les utilisateurs connectés pour l'envoi d'un message
                    /*$list_users             = User::get_users_connected($id_user);

                    if (count($list_users) > 0) {}*/
                    $validate_contenu = $request->validate([
                        'body' => 'required'
                    ]);

                    if ($validate_contenu) {
                        // Affectation du message à une animatrice connectée
                        if ($is_admin_destination || !empty($user->is_admin)) {
                            $status_destinataire    = User::where(['isonline' => 1, 'id' => $destination])
                                                    ->first();
                            if ($status_destinataire) {
                                // Verification si l'animatrice est connectée
                                $destination = $status_destinataire->id;
                            } else {
                                // Sinon, envoi du message à une autre animatrice connectée
                                $first_animator_connected   = User::where(['isonline' => 1, 'is_admin' => 1])
                                                                ->where('id', '<>', $id_user)
                                                                ->orderBy('id')
                                                                ->first();
                                $destination                = $first_animator_connected->id;
                            }
                        }

                        // Vérification si la conversation existe déjà ou non
                        $conversation           = ConversationsModel::where(['id_user' => $id_user, 'id_destination' => $destination])
                                                ->orWhereRaw('(id_user = ' . $id_user . ' AND id_destination = ' . $destination .')')
                                                ->first();
                        if (!empty($conversation)) {
                            $id_conversation        = $conversation->id;
                        } else {
                            // Création de la conversation
                            $create_conversation    = ConversationsModel::create([
                                'id_user'           => $id_user,
                                'id_destination'    => $destination,
                            ]);
                            $id_conversation        = $create_conversation->id;
                        }
                        $message                = MessagesModel::create([
                            'body'              => $request->body,
                            'sender_id'         => $id_user,
                            'recipient_id'      => $destination,
                            'spamscore'         => 0,
                            'status'            => 0,
                            'read'              => 1,
                            'conversation_id'   => $id_conversation,
                            'moderated_at'      => $request->moderated_at,
                            'fack_user_id'      => $fk_user
                        ]);

                        // Vérification de la derniere date d'envoi d'un message
                        $status_message         = $this->verificationMessagesStatusByUsers($destination);

                        if ($message) {
                            // Envoi des events vers pusher
                            broadcast(new NewMessageEvent($message))->toOthers();
                            return response([
                                'message'           => $message,
                                'id_status_message' => $status_message,
                                'user'              => auth()->user(),
                            ]);
                        }
                    }
                } else return response([
                    'message' => 'Errer d\'envoi d\'un message'
                ]);

            }

        }

    }

    public function viewMessages(Request $request, $destination, $id_fk_user) {

        if (Auth::check()) {
            $user        = auth()->user();

            if ($user->id != $destination) {
                $is_admin_destination   = User::get_admin_user($destination);
                // Envoyer message si destination n'est pas admin user n'est pas un admin
                if (!$is_admin_destination || empty($user->is_admin)) {
                    $status_message = $this->verificationMessagesStatusByUsers($destination);

                    if ($id_fk_user != 0) {
                        $messages = MessagesModel::where(['fack_user_id' => $id_fk_user])
                                    ->where(['sender_id' => $user->id, 'recipient_id' => $destination])
                                    ->orWhereRaw('(sender_id = ' . $destination . ' AND recipient_id = ' . $user->id .')')
                                    ->orderBy('created_at')
                                    ->pluck('body', 'created_at');
                    }
                    // Si l'id_fk_user == 0 => les users se tchattent en tant que client
                    $messages       = MessagesModel::where(['sender_id' => $user->id, 'recipient_id' => $destination])
                                    ->orWhereRaw('(sender_id = ' . $destination . ' AND recipient_id = ' . $user->id .')')
                                    ->orderBy('created_at')
                                    ->pluck('body', 'created_at');

                    return response([
                        'messages'          => $messages,
                        'id_status_message' => $status_message,
                        'user'              => $user,
                    ]);
                }
                return response([
                    'messages'          => null
                ]);
            }
        }

    }

}
