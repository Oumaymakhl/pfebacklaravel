<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\ChatSent;
use App\Models\admin;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
    $token = JWTAuth::getToken();
    $payload = JWTAuth::getPayload($token)->toArray();
    $group=$payload['group'];
    $id = $payload['sub'];
    $type=$payload['type'];
    if($type=="user"){
      $user=  User::find($id);
    }else{
        $user=  admin::find($id);
    }
        $chat = Chat::create([
            'sender_id' => $id,
            'receiver_id' => $group,
            'message' => $request->message,
            'name'=>$user->nom,
            'photo_url'=>$user->profile_photo,
            'email'=>$user->email,
        ]);
        $pusher = new Pusher(env("PUSHER_APP_KEY"),env("PUSHER_APP_SECRET"),env("PUSHER_APP_ID"),[
            'cluster' => 'mt1',
            'useTLS' => true,
                    ],);
        $pusher->trigger('groupChanel'.$group, 'my-event', $chat);
        return response()->json($chat);
    }

    public function getMessages(Request $request)
    {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
        $group=$payload['group'];
        $id = $payload['sub'];
        $type = $payload['type'];
        if($type=="user"){
            $email=  User::find($id)->email;
          }else{
              $email= admin::find($id)->email;
          }
        $messages = Chat::where('receiver_id','=',$group)->get();
       
        return response()->json(['message'=>$messages,'my'=>$id,"group"=>$group,"email"=>$email],200);
    }
}
