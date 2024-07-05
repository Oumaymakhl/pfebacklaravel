<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\Hash;
use PharIo\Manifest\Email;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\admin;
use App\Models\passwordreset;

use Illuminate\Validation\Rule;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminRegistrationMail;
use Illuminate\Support\Facades\Log; 
use App\Models\User;
use Illuminate\Support\Str;

use App\Models\Sadmin;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use App\Mail\ResetPassword;


class AdminController extends Controller
{
   
public function ajoutadmin(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nom' => 'required',
        'prenom' => 'required',
        'login' => 'required',
        'password' => 'required',
        'email' => 'required',
        'company.nom' => 'required',
        'company.subdomaine' => 'required',
        'company.logo' => 'required|image',
        'company.adresse' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }
    $data = $validator->validated();

    $existingAdminOrCompany = Admin::where('email', $data['email'])
        ->orWhere('login', $data['login']) // Vérifie si le login existe également
        ->orWhereHas('company', function($query) use ($data) {
            $query->where('nom', $data['company']['nom']);
        })->first();
    
    if ($existingAdminOrCompany) {
        $message = ($existingAdminOrCompany->email === $data['email'] || $existingAdminOrCompany->login === $data['login']) ? 'Email or login already exists' : 'Company already exists';
        return response()->json(['error' => $message], 400);
    }
    
    if ( User::where('email', $data['email'])->orWhere('login', $data['login'])->exists() ||
       Sadmin::where('email', $data['email'])->orWhere('login', $data['login'])->exists()) {
    return response()->json(['message' => 'Email or login already exists in other roles'], 422);
}

    if ($request->hasFile('company.logo')) {
        $logo = $request->file('company.logo');
        $logoPath = $logo->store('public'); // Stocke le fichier dans le dossier storage/app/public avec le nom de fichier original
        $logoPath = str_replace('public/', '/storage/', $logoPath); // Remplace 'public/' par '/storage/' dans le chemin
    } else {
        $logoPath = null;
    }

    $company = Company::create([
        'nom' => $data['company']['nom'],
        'subdomaine' => $data['company']['subdomaine'],
        'logo' => $logoPath, // Enregistre le chemin du logo dans la base de données
        'adresse' => $data['company']['adresse']
    ]);

    // Enregistrement de l'administrateur avec le mot de passe non crypté
    $admin = Admin::create([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'login' => $data['login'],
        'password' => bcrypt($data['password']),
        'email' => $data['email'],
        'company_id' => $company->id,
    ]);
    
    // Envoyer l'e-mail après la création de l'administrateur
    Mail::to($admin->email)->send(new AdminRegistrationMail($admin, $data['password']));

    // Crypter le mot de passe après l'envoi de l'e-mail


    return response()->json([
        'message' => 'Signup successful',
        'admin' => $admin,
        'company' => $company,
    ], 201);
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
   
if (User::where('email', $data['email'])->orWhere('login', $data['login'])->exists() ||
Sadmin::where('email', $data['email'])->orWhere('login', $data['login'])->exists()) {
return response()->json(['message' => 'Email or login already exists in other roles'], 422);
}


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