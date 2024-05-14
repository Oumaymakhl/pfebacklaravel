<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sadmin;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;


class SadminController extends Controller
{
    //
    public function signup(Request $request)
{
    $data = $request->validate([
        'nom' => 'required',
        'prenom' => 'required',
        'login' => 'required',
        'password' => 'required',
        'email' => 'required|',
    ]);
    $existingUser = Sadmin::where('email', $data['email'])
    ->orWhere('login', $data['login'])
    ->first();

if ($existingUser) {
    return response()->json(['message' => 'User already exists'], 422);
}
    // Vérifier si l'email ou le login est déjà utilisé dans les autres rôles
    if (User::where('email', $data['email'])->orWhere('login', $data['login'])->exists() ||
        Admin::where('email', $data['email'])->orWhere('login', $data['login'])->exists()) {
        return response()->json(['message' => 'Email or login already exists in other roles'], 422);
    }

    $password = $data['password'];

    $data['password'] = bcrypt($data['password']);

    $sadmin = Sadmin::create($data);

    return response()->json(['message' => 'Signup successful', 'sadmin' => $sadmin], 201);
}
    /*public function login(Request $request)
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
    }*/
}
