<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationsModel;
use App\Models\FackUsersModel;
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
    public function viewAllConversations(Request $request) {

        $user           = auth()->user();
        $conversations  = ConversationsModel::where(['id_user' => $user->id])
                        ->orWhere(['id_destination' => $user->id])
                        ->first(['id_destination']);
        $conversations  = ($conversations) ? $conversations : 'Aucune conversation';

        return response([
            'conversations' => $conversations,
            'user'          => $user
        ]);

    }

}
