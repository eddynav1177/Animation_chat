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

    public function sendMessage(Request $request, $destination, $fake_user_id) {
        $current_user           = auth()->user();
        $current_user_id        = $current_user->id;

        if ($current_user_id == $destination) {
            throw new Exception('Vous n\'avez pas le droit d\'envoyer un message à vous même');
        }

        $is_admin_destination   = User::get_admin_user($destination);
        if ($is_admin_destination && !empty($current_user->is_admin)) {
            throw new Exception('Vous ne pouvez pas vous envoyer de messages en tant qu\'animatrice');
        }
        $fake_user_id           = (!$is_admin_destination && (empty($current_user->is_admin))) ? 0 : $fake_user_id;
        $client_id              = (!$is_admin_destination) ? $destination : $current_user_id;

        $validate_form = $request->validate([
            'body' => 'required'
        ]);

        if (!$validate_form) {
            throw new Exception('Formulaire invalide, Erreur de création du message');
        }
        $last_message_id        = MessagesModel::get_last_message_time_id_send_by_user($destination);
        $count_messages         = MessagesModel::count_messages_send_by_user($client_id, $fake_user_id);
        /*return $count_messages;
        if (empty($last_message_id) && !(empty($count_messages))) {
            $destination = User::get_first_animator_connected();
        }*/

        $destination            = User::users_is_admin($destination);
        $conversation           = ConversationsModel::where(['user_id' => $current_user_id, 'destination_id' => $destination])
                                ->orWhereRaw('(user_id = ' . $destination . ' AND destination_id = ' . $current_user_id .')')
                                ->first();
        if (!empty($conversation)) {
            $conversation_id    = $conversation->id;
        } else {
            $conversation_created   = ConversationsModel::create([
                'user_id'           => $current_user_id,
                'destination_id'    => $destination,
                'fake_user_id'      => $fake_user_id,
            ]);
            $conversation_id        = $conversation_created->id;
        }

        $message                = MessagesModel::create([
            'body'              => $request->body,
            'sender_id'         => $current_user_id,
            'recipient_id'      => $destination,
            'spamscore'         => 0,
            'status'            => 0,
            'read'              => 1,
            'conversation_id'   => $conversation_id,
            'moderated_at'      => $request->moderated_at,
            'fake_user_id'      => $fake_user_id,
            'client_id'         => $client_id,
        ]);
        broadcast(new NewMessageEvent($message))->toOthers();
        return response([
            'message'           => $message,
            'user'              => $current_user,
        ]);
    }

    public function viewMessages(Request $request, $destination, $fake_user_id, $client_id) {
        $current_user           = auth()->user();
        if ($current_user->id == $destination) {
            throw new Exception('Impossible, vous consultez la liste des messages envoyés par vous même');
        }
        $is_admin_destination   = User::get_admin_user($destination);
        if ($is_admin_destination && !empty($current_user->is_admin)) {
            throw new Exception('Impossible de consulter les messages en tant qu\'animatrice');
        }
        if ($fake_user_id != 0) {
            $messages = MessagesModel::where(['fake_user_id' => $fake_user_id])
                        ->where(['client_id' => $client_id]);
        } else {
            $messages = MessagesModel::where(['sender_id' => $current_user->id, 'recipient_id' => $destination])
                        ->orWhereRaw('(sender_id = ' . $destination . ' AND recipient_id = ' . $current_user->id .')');
        }
        $messages               = $messages->orderBy('created_at')->pluck('body', 'created_at');

        return response([
            'messages'          => $messages,
            'user'              => $current_user,
        ]);
    }

}
