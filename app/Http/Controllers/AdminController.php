<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\admin;
use Illuminate\Validation\Rule;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminRegistrationMail;



class AdminController extends Controller
{
   
     public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }
   /**
 * Register an Admin.
 *
 * @return \Illuminate\Http\JsonResponse
 */
/*public function signup(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nom' => 'required',
        'prenom' => 'required',
        'login' => 'required',
        'password' => 'required',
        'email' => 'required|email',
        'company.nom' => 'required',
        'company.adresse' => 'required',
        'company.subdomaine' => 'required',
        'company.logo' => 'nullable',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $data = $validator->validated();
    $existingAdmin = Admin::where('email', $data['email'])->first();

    if ($existingAdmin) {
        return response()->json(['error' => 'Email already exists'], 400);
    }

    $company = Company::create([
        'nom' => $data['company']['nom'],
        'adresse' => $data['company']['adresse'],
        'subdomaine' => $data['company']['subdomaine'],
        'logo' => $request->file('company.logo') ? $request->file('company.logo')->store('logos') : null,
    ]);

    $data['password'] = bcrypt($data['password']);

    $admin = Admin::create([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'login' => $data['login'],
        'password' => $data['password'],
        'email' => $data['email'],
        'company_id' => $company->id,
    ]);

    // Envoyer un e-mail d'inscription
    Mail::to($admin->email)->send(new AdminRegistrationMail($admin, $data['password']));

    return response()->json([
        'message' => 'Signup successful',
        'admin' => $admin,
        'company' => $company,
    ], 201);
}*/
public function signup(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nom' => 'required',
        'prenom' => 'required',
        'login' => 'required',
        'password' => 'required',
        'email' => 'required|email',
        'company.nom' => 'required',
        'company.adresse' => 'required',
        'company.subdomaine' => 'required',
        'company.logo' => 'nullable',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $data = $validator->validated();
    $existingAdmin = Admin::where('email', $data['email'])->first();

    if ($existingAdmin) {
        return response()->json(['error' => 'Email already exists'], 400);
    }

    $existingCompany = Company::where('nom', $data['company']['nom'])
                                ->where('adresse', $data['company']['adresse'])
                                ->where('subdomaine', $data['company']['subdomaine'])
                                ->whereHas('admins')
                                ->first();

    if ($existingCompany) {
        return response()->json(['error' => 'This company is already associated with another admin'], 400);
    }

    $company = Company::create([
        'nom' => $data['company']['nom'],
        'adresse' => $data['company']['adresse'],
        'subdomaine' => $data['company']['subdomaine'],
        'logo' => $request->file('company.logo') ? $request->file('company.logo')->store('logos') : null,
    ]);

    $data['password'] = bcrypt($data['password']);

    $admin = Admin::create([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'login' => $data['login'],
        'password' => $data['password'],
        'email' => $data['email'],
        'company_id' => $company->id,
    ]);

    return response()->json([
        'message' => 'Signup successful',
        'admin' => $admin,
        'company' => $company,
    ], 201);
}


 /**
     * Log the admin out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Admin successfully signed out']);
    }
/**
 * Refresh a token.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function refresh()
{
    return response()->json([
        'status' => 'success',
        'user' => Auth::user(),
        'authorisation' => [
            'token' => Auth::refresh(),
            'type' => 'bearer',
        ]
    ]);
}

/**
 * Get the authenticated Admin.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function userProfile()
{
    return response()->json(auth()->user());
}
/**
 * Create a new token array structure.
 *
 * @param  string $token
 *
 * @return \Illuminate\Http\JsonResponse
 */


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
   /* public function update(Request $request, $id)
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
}*/
public function update(Request $request, $id)
{
    $admin = admin::find($id);
    if (!$admin) {
        return response()->json(['message' => 'Admin not found'], 404);
    }

    $data = $request->validate([
        'nom' => 'required',
        'prenom' => 'required',
        'login' => 'required',
        'email' => 'required',
        'company_id' => 'required|exists:companies,id', 
    ]);

  

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
