<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;




class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required|unique:users',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json(['message' => 'Signup successful', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('user')->attempt($credentials)) {
            $user = Auth::guard('user')->user();

            return response()->json(['user' => $user], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }
    }
}
