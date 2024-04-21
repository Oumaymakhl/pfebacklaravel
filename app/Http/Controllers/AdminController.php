<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
use App\Models\User;
use App\Models\Sadmin;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class AdminController extends Controller
{
    //
   /* public function signup(Request $request)
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
    }*/
    public function signup(Request $request)
{
    $data = $request->validate([
        'nom' => 'required',
        'prenom' => 'required',
        'login' => 'required',
        'password' => 'required|min:8',
        'email' => 'required',
        'company.nom' => 'required',
        'company.adresse' => 'required',
        'company.subdomaine' => 'required',
        'company.logo' => 'nullable|image',
    ]);

    
    $data['password'] = bcrypt($data['password']);

    $company = Company::create([
        'nom' => $data['company']['nom'],
        'adresse' => $data['company']['adresse'],
        'subdomaine' => $data['company']['subdomaine'],
        'logo' => $data['company']['logo'] ? $data['company']['logo']->store('logos') : null,
    ]);

    $admin = Admin::create([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'login' => $data['login'],
        'password' => $data['password'],
        'email' => $data['email'],
        'company_id' => $company->id,
    ]);

    return response()->json(['message' => 'Signup successful', 'admin' => $admin, 'company' => $company], 201);
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
    public function adminLogout(Request $request)
    {
        $request->session()->invalidate();

        return response()->json(['message' => 'Admin logged out successfully'], 200);
    }
}
