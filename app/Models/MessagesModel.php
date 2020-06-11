<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class MessagesModel extends Model
{
    //
    protected $table='messages';

    protected $fillable = [
        'sender_id', 'recipient_id', 'client_id', 'body', 'spamscore', 'status', 'read', 'sent_from', 'moderated_at', 'fake_user_id', 'conversation_id'
    ];

    public static function get_last_message_time_id_send_by_user($user_id) {
        $last_message_time = MessagesModel::where(['sender_id' => $user_id])
                            ->orWhere(['recipient_id' => $user_id])
                            ->where('created_at', 'BETWEEN (NOW() - INTERVAL 30 MINUTE) AND (NOW() + INTERVAL 30 MINUTE)')
                            ->orderBy('created_at', 'desc')
                            ->first();
        if (empty($last_message_time)) {
            // throw new Exception('Il n\'y a pas de message(s) qui ont été envoyés ces 30 dernières minutes');
            return;
        }

        return $last_message_time->id;
    }

    public static function count_messages_send_by_user($client_id, $fake_user_id) {
        $count_messages = MessagesModel::where(['client_id' => $client_id])
                        ->where(['fake_user_id' => $fake_user_id])
                        ->get();
        if (empty($count_messages)) {
            return;
        }
        return $count_messages->count();
    }

}
