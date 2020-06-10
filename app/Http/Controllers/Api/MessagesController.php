<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MessagesModel;
use App\User;
use App\Events\NewMessageEvent;
use App\Models\ConversationsModel;
use Exception;

class MessagesController extends Controller
{
    /*
    MessagesController: Controller pour la gestion des messages envoyés par les client et/ou les animatrices
    */

    private function verificationMessagesStatusByUsers($id_user) {
        /*if (empty($id_user)) {
            throw new Exception('Les messages qui ont été envoyés datent de plus de 30minutes');
        }*/

        //Verification de la date d'envoi du dernier message
        $last_message = MessagesModel::where(['sender_id' => $id_user])
                        ->orWhere(['recipient_id' => $id_user])
                        ->where('created_at', 'BETWEEN (NOW() - INTERVAL 30 MINUTE) AND (NOW() + INTERVAL 30 MINUTE)')
                        ->orderBy('created_at', 'desc')
                        ->first();
        if (empty($last_message)) {
            throw new Exception('Il n\'y a pas de messages qui ont été envoyés ces 30 dernières minutes');
        }

        return $last_message->id;
    }

    public function sendMessage(Request $request, $destination, $fk_user) {
        $user           = auth()->user();
        $id_user        = $user->id;

        if ($id_user == $destination) {
            throw new Exception('Vous n\'avez pas le droit d\'envoyer un message à vous même');
        }

        $is_admin_destination   = User::get_admin_user($destination);
        if ($is_admin_destination && !empty($user->is_admin)) {
            throw new Exception('Vous ne pouvez pas vous envoyer de messages en tant qu\'animatrice');
        }
        $fk_user                = (!$is_admin_destination && (empty($user->is_admin))) ? 0 : $fk_user;
        if ($fk_user == 0) {
            $user_id = 0;
        }
        $user_id = (!$is_admin_destination) ? $user_id = $destination : $user->id;

        $validate_contenu = $request->validate([
            'body' => 'required'
        ]);

        if (!$validate_contenu) {
            throw new Exception('Formulaire invalide pour l\'insertion d\'un message');
        }

        $destination            = User::usersIsAdmin($destination);
        // Vérification si la conversation existe déjà ou non
        $conversation           = ConversationsModel::where(['id_user' => $id_user, 'id_destination' => $destination])
                                ->orWhereRaw('(id_user = ' . $id_user . ' AND id_destination = ' . $destination .')')
                                ->first();
        if (!empty($conversation)) {
            $id_conversation        = $conversation->id;
        }
        $create_conversation    = ConversationsModel::create([
            'id_user'           => $id_user,
            'id_destination'    => $destination,
        ]);
        $id_conversation        = $create_conversation->id;
        $message                = MessagesModel::create([
            'body'              => $request->body,
            'sender_id'         => $id_user,
            'recipient_id'      => $destination,
            'spamscore'         => 0,
            'status'            => 0,
            'read'              => 1,
            'conversation_id'   => $id_conversation,
            'moderated_at'      => $request->moderated_at,
            'fack_user_id'      => $fk_user,
            'user_id'           => $user_id,
        ]);
        // Envoi des events vers pusher
        broadcast(new NewMessageEvent($message))->toOthers();
        // Vérification de la derniere date d'envoi d'un message
        // $status_message         = $this->verificationMessagesStatusByUsers($destination);
        /*if (!$status_message) {
            $status_message = '';
        }*/
        return response([
            'message'           => $message,
            // 'id_status_message' => $status_message,
            'user'              => auth()->user(),
        ]);
    }

    public function viewMessages(Request $request, $destination, $id_fk_user, $id_animator) {
        $user        = auth()->user();
        if ($user->id == $destination) {
            throw new Exception('Impossible, vous consultez la liste des messages envoyés par vous même');
        }
        $is_admin_destination   = User::get_admin_user($destination);
        if ($is_admin_destination && !empty($user->is_admin)) {
            throw new Exception('Impossible de consulter les messages en tant qu\'animatrice');
        }
        /*$status_message = $this->verificationMessagesStatusByUsers($destination);
        if (!$status_message) {
            $status_message = '';
        }*/
        if ($id_fk_user != 0 && $id_animator != 0) {
            $messages = MessagesModel::where(['fack_user_id' => $id_fk_user])
                        ->where(['user_id' => $id_animator])
                        ->orderBy('created_at')
                        ->pluck('body', 'created_at');
        }
        // Si l'id_fk_user === 0 => les users se tchattent en tant que client
        $messages       = MessagesModel::where(['sender_id' => $user->id, 'recipient_id' => $destination])
                        ->orWhereRaw('(sender_id = ' . $destination . ' AND recipient_id = ' . $user->id .')')
                        ->orderBy('created_at')
                        ->pluck('body', 'created_at');

        return response([
            'messages'          => $messages,
            // 'id_status_message' => $status_message,
            'user'              => $user,
        ]);
    }
}
