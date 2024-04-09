<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin;
use App\Models\User;
use App\Models\Sadmin;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
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
     public function index()
    {
        $admins = Admin::all();
        return response()->json(['admins' => $admins], 200);
    }

    public function show($id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }
        return response()->json(['admin' => $admin], 200);
    }
    public function edit($id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }
        return response()->json(['admin' => $admin], 200);
    }
    public function update(Request $request, $id)
{
    $admin = Admin::find($id);
    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }

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

    // Mise à jour de l'entreprise liée à l'administrateur
    $companyData = [
        'nom' => $data['company']['nom'],
        'adresse' => $data['company']['adresse'],
        'subdomaine' => $data['company']['subdomaine'],
        'logo' => $data['company']['logo'] ? $data['company']['logo']->store('logos') : null,
    ];

    $admin->company()->update($companyData); // Utiliser la méthode relationship pour mettre à jour la compagnie

    
    unset($data['company']); 
    $admin->update($data);

    return response()->json(['message' => 'Admin updated successfully', 'admin' => $admin], 200);
}
public function destroy($id)
{
    $admin = Admin::find($id);
    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    } 
    if ($admin->company) {
        $admin->company->delete();
    }

    $admin->delete();

    return response()->json(['message' => 'Admin and associated company deleted successfully'], 200);
}

}
