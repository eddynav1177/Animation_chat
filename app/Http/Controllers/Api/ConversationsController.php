<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationsModel;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class ConversationsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    //
    // ConversationsController: Controleur pour la gestion conversations entre les clients et les animatrices
    public function viewConversations(Request $request) {

        if (Auth::check()) {
            $id_user        = auth()->user()->id;
            $conversations  = ConversationsModel::where(['id_user' => $id_user])->orWhere(['id_destination' => $id_user])->first(['id_destination']);
            // $conversations  = DB::table('conversations')->join('fack_users', 'conversations.id_user', 'fack_users.id_user')->where(['conversations.id_user' => $id_user])->orWhere(['conversations.id_destination' => $id_user])->pluck('conversations.id_destination', 'fack_users.id');
            $conversations  = ($conversations) ? $conversations : 'Aucune conversation';

            return response([
                'conversations' => $conversations,
                'user'          => auth()->user()
            ]);
        }

    }
}
