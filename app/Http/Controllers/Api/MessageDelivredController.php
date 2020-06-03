<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MessagesModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\MessageDeliveredEvent;

class MessageDelivredController extends Controller
{
    //
    public $message;

    public function __invoke(MessagesModel $message)
    {
        $message->read_at = Carbon::now();
        $message->save();

        broadcast(new MessageDeliveredEvent($message));
    }
}
