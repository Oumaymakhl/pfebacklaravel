<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sadmin;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class SadminController extends Controller
{
    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required',
            'password' => 'required',
            'email' => 'required',
        ]);

        $existingUser = Sadmin::where('email', $data['email'])
            ->orWhere('login', $data['login'])
            ->first();

        if ($existingUser) {
            return response()->json(['message' => 'User already exists'], 422);
        }

        if (User::where('email', $data['email'])->orWhere('login', $data['login'])->exists() ||
            Admin::where('email', $data['email'])->orWhere('login', $data['login'])->exists()) {
            return response()->json(['message' => 'Email or login already exists in other roles'], 422);
        }

        $password = $data['password'];
        $data['password'] = bcrypt($data['password']);
        $sadmin = Sadmin::create($data);

        return response()->json(['message' => 'Signup successful', 'sadmin' => $sadmin], 201);
    }
}