<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Sadmin;
use Illuminate\Support\Facades\Log;

class PasswordResetRequestController extends Controller
{  
        public function sendPasswordResetEmail(Request $request)
    {
        $email = $request->email;
        $user = null;
        $type = null;
    
        if (!$user) {
            $user = User::where('email', $email)->first();
            $type = $user ? 'user' : $type;
        }
    
        if (!$user) {
            $user = Admin::where('email', $email)->first();
            $type = $user ? 'admin' : $type;
        }
    
        if (!$user) {
            $user = Sadmin::where('email', $email)->first();
            $type = $user ? 'superadmin' : $type;
        }
    
        if ($user) {
            $this->sendMail($request->email);
            return response()->json([
                'message' => 'Check your inbox, we have sent a link to reset email.'
            ], Response::HTTP_OK);
        }
    
        return response()->json([
            'message' => 'Email does not exist.'
        ], Response::HTTP_NOT_FOUND);
    }
    
    public function sendMail($email){
        $token = $this->generateToken($email);
        Log::info("Token: $token");
        Mail::to($email)->send(new SendMail($token));
    }
    
        public function generateToken($email){
            $isOtherToken = DB::table('password_resets')->where('email', $email)->first();
            if($isOtherToken) {
                return $isOtherToken->token;
            }
            $token = Str::random(80);
            $this->storeToken($token, $email);
            return $token;
        }
        
        public function storeToken($token, $email){
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()            
            ]);
        }
        
    }        

