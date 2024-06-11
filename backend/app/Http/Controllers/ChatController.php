<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function messages(Request $request)
    {
        $request->validate([
            'sender_id' => 'required',
            'receiver_id' => 'required',
        ]);

        $messages = Chat::where(function ($query) use ($request) {
            $query->whereIn("sender_id", [$request->sender_id, $request->receiver_id])
            ->orWhereIn("receiver_id", [$request->sender_id, $request->receiver_id]);
        })->get();

        return response()->json($messages, 200);
    }
}
