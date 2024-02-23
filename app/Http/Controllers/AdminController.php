<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminRegistrationMail;
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
        'login' => 'required',
        'password' => 'required|min:8',
        'email' => 'required|email',
    ]);
    $existingUser = Admin::where('email', $data['email'])
            ->orWhere('login', $data['login'])
            ->first();

        if ($existingUser) {
            return response()->json(['message' => 'User already exists'], 422);
        }
    // Vérifier si l'email ou le login est déjà utilisé dans les autres rôles
    if (User::where('email', $data['email'])->orWhere('login', $data['login'])->exists() ||
        Sadmin::where('email', $data['email'])->orWhere('login', $data['login'])->exists()) {
        return response()->json(['message' => 'Email or login already exists in other roles'], 422);
    }

    $password = $data['password'];

    $data['password'] = bcrypt($data['password']);

    $admin = Admin::create($data);
    Mail::to($admin->email)->send(new AdminRegistrationMail($admin, $password));

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
