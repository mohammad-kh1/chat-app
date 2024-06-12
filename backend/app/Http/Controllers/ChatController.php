<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    public function messages(Request $request)
    {
        $request->validate([
            'sender_id' => 'required',
            'receiver_id' => 'required',
        ]);

        $messages = Chat::where(function ($query) use ($request) {
            $query->whereIn("receiver_id", [$request->sender_id, $request->receiver_id]);
            $query->whereIn("sender_id", [$request->sender_id, $request->receiver_id]);
        })->get();

        return response()->json($messages, 200);
    }

    public function online_users()
    {
        $users = Redis::lrange("online_users", 0, -1);
        return response()->json($users , 200);
    }

    public function online_disconnetd(Request $request)
    {
        Redis::lrem("online_users", 0, $request->user_id);
        return response()->json([], 200);
    }
}
