<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MessagesModel extends Model
{
    //
    protected $table    = 'messages';

    protected $fillable = [
        'sender_id', 'recipient_id', 'animator_id', 'body', 'spamscore', 'status', 'read', 'sent_from', 'moderated_at', 'fake_user_id', 'conversation_id'
    ];

    public static function get_animator_message_affinity($animator_id) {
        $current_user = auth()->user();
        $count_message_affinity = Config::get('app.message_affinity');
        if ($current_user->id === $animator_id) {
            // throw new Exception('Il n\'est pas indispensable d\'affecter une conversation à vous même');
            return;
        }
        $animator_is_online = User::user_is_online($animator_id, 1);
        if (empty($animator_is_online)) {
            return;
        }
        $count_messages = MessagesModel::where(['animator_id' => $animator_id]);
        if ($count_messages->count() >= $count_message_affinity) {
            return $animator_id;
        }
    }

    public static function get_last_message_time_id_send_by_user($user_id) {
        $waiting_time       = Config::get('app.waiting_time');
        $last_message_time  = MessagesModel::where(['sender_id' => $user_id])
                            ->orWhere(['recipient_id' => $user_id])
                            ->whereRaw('created_at BETWEEN (NOW() - INTERVAL ' . $waiting_time . ' MINUTE) AND (NOW() + INTERVAL ' . $waiting_time .' MINUTE)')
                            ->orderBy('created_at', 'desc')
                            ->get();
        if (empty($last_message_time)) {
            // throw new Exception('Il n\'y a pas de message(s) qui ont été envoyés ces 30 dernières minutes');
            return;
        }
        return $last_message_time->count();
    }

    public static function count_messages_send_by_user($animator_id, $fake_user_id) {
        $count_messages = MessagesModel::where(['animator_id' => $animator_id])
                        ->where(['fake_user_id' => $fake_user_id])
                        ->get();
        if (empty($count_messages)) {
            return;
        }
        return $count_messages->count();
    }


}
