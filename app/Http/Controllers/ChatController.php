<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use App\Events\ChatSent;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        event(new ChatSent($request->message, $request->receiver_id));

        return response()->json($chat);
    }

    public function getMessages($senderId, $receiverId)
    {
        $messages = Chat::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
