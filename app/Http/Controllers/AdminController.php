<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminRegistrationMail;
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

        $existingUser = Admin::where('email', $data['email'])
            ->orWhere('login', $data['login'])
            ->first();

        if ($existingUser) {
            return response()->json(['message' => 'User already exists'], 422);
        }

        $password = $data['password'];

        $data['password'] = bcrypt($data['password']);

        $admin = Admin::create($data);

        // Envoi de l'e-mail de confirmation
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
