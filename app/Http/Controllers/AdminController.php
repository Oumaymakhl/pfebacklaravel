<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
use App\Models\User;
use App\Models\Sadmin;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller
{
    //
    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required|unique:admins',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:admins',
        ]);

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
