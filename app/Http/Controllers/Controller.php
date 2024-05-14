<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Sadmin;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistrationMail;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\DB; 

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function signup(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required',
            'password' => 'required',
            'email' => 'required',
            'company_id' => 'required',
    
        ]);
        $existingUser = User::where('email', $data['email'])
        ->orWhere('login', $data['login'])
        ->first();
    
    if ($existingUser) {
        return response()->json(['message' => 'User already exists'], 422);
    }
        if (Admin::where('email', $data['email'])->orWhere('login', $data['login'])->exists() ||
            Sadmin::where('email', $data['email'])->orWhere('login', $data['login'])->exists()) {
            return response()->json(['message' => 'Email or login already exists in other roles'], 422);
        }
    
        $password = $data['password'];
    
        $data['password'] = bcrypt($data['password']);
    
        $user = User::create($data);
        Mail::to($user->email)->send(new UserRegistrationMail($user, $password));
    
        return response()->json(['message' => 'Signup successful', 'user' => $user], 201);
    }

       public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['user' => $user], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'login' => 'required',
            'email' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        $user->update($data);

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    public function userLogout(Request $request)
    {
        $request->session()->invalidate();

        return response()->json(['message' => 'User logged out successfully'], 200);
    }
    
  
}