<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sadmin;
use Illuminate\Support\Facades\Auth;


class SadminController extends Controller
{
    //
    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required|unique:sadmins',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:sadmins',
        ]);

        $data['password'] = bcrypt($data['password']);

        $sadmin = Sadmin::create($data);

        return response()->json(['message' => 'Signup successful', 'sadmin' => $sadmin], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('sadmin')->attempt($credentials)) {
            $sadmin = Auth::guard('sadmin')->user();

            return response()->json(['sadmin' => $sadmin], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }
    }
}
