<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller
{
    //
    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email',
        ]);

        $existingUser = admin::where('email', $data['email'])
        ->orWhere('login', $data['login'])
        ->first();

    if ($existingUser) {
        return response()->json(['message' => 'User already exists'], 422);
    }

    $data['password'] = bcrypt($data['password']);

    $admin = admin::create($data);

    return response()->json(['message' => 'Signup successful', 'admin' => $admin], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();

            return response()->json(['admin' => $admin], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }
    }
}
