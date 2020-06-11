<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationsModel;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class ConversationsController extends Controller
{

    // ConversationsController: Controleur pour la gestion conversations entre clients et/ou animatrices
    public function viewAllConversations(Request $request) {

        $current_user           = auth()->user();
        $conversations_list     = ConversationsModel::where(['user_id' => $current_user->id])
                                ->orWhere(['destination_id' => $current_user->id])
                                ->get(['destination_id', 'fake_user_id', 'user_id']);
        if (empty($conversations_list)) {
            return;
        }

        return response([
            'conversations' => $conversations_list,
            'user'          => $current_user
        ]);

    }

}
