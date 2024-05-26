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
   
    /* public function login(Request $request)
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

    }*/
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
        ->orWhereHas('company', function($query) use ($data) {
            $query->where('nom', $data['company']['nom'])
                  ->where('adresse', $data['company']['adresse'])
                  ->where('subdomaine', $data['company']['subdomaine']);
        })->first();

    if ($existingAdminOrCompany) {
        $message = $existingAdminOrCompany->email === $data['email'] ? 'Email already exists' : 'Company already exists';
        return response()->json(['error' => $message], 400);
    }

    // Gestion du logo de l'entreprise
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
        'password' => $data['password'],
        'email' => $data['email'],
        'company_id' => $company->id,
    ]);
    
    // Envoyer l'e-mail après la création de l'administrateur
    Mail::to($admin->email)->send(new AdminRegistrationMail($admin, $data['password']));

    // Crypter le mot de passe après l'envoi de l'e-mail
    $admin->password = bcrypt($data['password']);
    $admin->save();

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
/*public function authenticate(Request $request)
{
    $request->validate([
        'login' => 'required',
        'password' => 'required',
    ]);

    $user = null;
    $type = null;

    if (!$user) {
        $user = User::where('login', $request->login)->first();

        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            Log::info("Username without domain: ".$username); // Log the username without domain
            $user = User::where('login', $username)->first();
            Log::info("User found with username: ".json_encode($user)); // Log the user found with username
        }

        $type = $user ? 'user' : $type;
    }

    if (!$user) {
        $user = Admin::where('login', $request->login)->first();
        $type = $user ? 'admin' : $type;

        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            $user = Admin::where('login', $username)->first();
        }
    }

    if (!$user) {
        $user = Sadmin::where('login', $request->login)->first();
        $type = $user ? 'superadmin' : $type;

        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            $user = Sadmin::where('login', $username)->first();
        }
    }

    if ($user && Hash::check($request->password, $user->password)) {
        $token = JWTAuth::fromUser($user);
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'type' => $type,
            'token' => $token
        ]);
    }
        return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
}*/
public function authenticate(Request $request)
{
    $request->validate([
        'login' => 'required',
        'password' => 'required',
    ]);

    $user = null;
    $type = null;

    if (!$user) {
        $user = User::where('login', $request->login)->first();

        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            Log::info("Username without domain: ".$username); // Log the username without domain
            $user = User::where('login', $username)->first();
            Log::info("User found with username: ".json_encode($user)); // Log the user found with username
        }

        $type = $user ? 'user' : $type;
    }

    if (!$user) {
        $user = Admin::where('login', $request->login)->first();
        $type = $user ? 'admin' : $type;

        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            $user = Admin::where('login', $username)->first();
        }
    }

    if (!$user) {
        $user = Sadmin::where('login', $request->login)->first();
        $type = $user ? 'superadmin' : $type;

        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            $user = Sadmin::where('login', $username)->first();
        }
    }

    if ($user && Hash::check($request->password, $user->password)) {
        $token = JWTAuth::claims(['type' => $type])->fromUser($user);
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'type' => $type,
            'token' => $token
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
}

/*public function profile()
{
    $token = JWTAuth::getToken();
    $payload = JWTAuth::getPayload($token)->toArray();
    $type = $payload['type'];
    $id = $payload['sub'];

    switch ($type) {
        case 'admin':
            $profile = Admin::find($id);
            break;
        case 'superadmin':
            $profile = Sadmin::find($id);
            break;
        case 'user':
        default:
            $profile = User::find($id);
            break;
    }

    if (!$profile) {
        return response()->json(['message' => 'Profile not found'], 404);
    }

    return response()->json(['profile' => $profile], 200);
}


/*public function profile($id)
{
    $user = null;
    $type = null;

    if (!$user) {
        $user = User::find($id);
        $type = $user ? 'user' : $type;
    }

    if (!$user) {
        $user = Admin::find($id);
        $type = $user ? 'admin' : $type;
    }

    if (!$user) {
        $user = Sadmin::find($id);
        $type = $user ? 'superadmin' : $type;
    }

    if (!$type) {
        return response()->json(['message' => 'User not found'], 404);
    }

    switch ($type) {
        case 'user':
            $profile = User::find($id);
            break;
        case 'admin':
            $profile = Admin::find($id);
            break;
        case 'superadmin':
            $profile = Sadmin::find($id);
            break;
    }

    if (!$profile) {
        return response()->json(['message' => 'Profile not found'], 404);
    }

    return response()->json(['profile' => $profile], 200);
}
*/
public function profile(Request $request)
{
    $token = JWTAuth::getToken();
    $payload = JWTAuth::getPayload($token)->toArray();
    $type = $payload['type'];
    $id = $payload['sub'];

    switch ($type) {
        case 'admin':
            $user = Admin::find($id);
            break;
        case 'superadmin':
            $user = Sadmin::find($id);
            break;
        case 'user':
        default:
            $user = User::find($id);
            break;
    }

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Mise à jour de la photo de profil si une nouvelle photo est téléversée
    if ($request->hasFile('profile_photo')) {
        $photo = $request->file('profile_photo');
        $photoPath = $photo->store('public/profile_photos'); // Stocke le fichier dans le dossier storage/app/public/profile_photos avec le nom de fichier original
        $photoPath = str_replace('public/', '/storage/', $photoPath); // Remplace 'public/' par '/storage/' dans le chemin

        $user->profile_photo = $photoPath;
        $user->save();
    }

    // Récupération des détails du profil
    $profile = [
        'id' => $user->id,
        'nom' => $user->nom,
        'prenom' => $user->prenom,
        'login' => $user->login,
        'email' => $user->email,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'company_id' => $user->company_id,
        'profile_photo' => $user->profile_photo ? asset($user->profile_photo) : null,
    ];

    if ($user->company_id) {
        $company = Company::find($user->company_id);
        if ($company) {
            $profile['company'] = [
                'id' => $company->id,
                'nom' => $company->nom,
                'subdomaine' => $company->subdomaine,
                'logo' => $company->logo ? asset($company->logo) : null,
                'created_at' => $company->created_at,
                'updated_at' => $company->updated_at,
                'adresse' => $company->adresse,
            ];
        }
    }

    return response()->json(['profile' => $profile], 200);
}
public function updateprofile(Request $request)
{
    $token = JWTAuth::getToken();
    $payload = JWTAuth::getPayload($token)->toArray();
    $type = $payload['type'];
    $id = $payload['sub'];

    switch ($type) {
        case 'admin':
            $user = Admin::find($id);
            break;
        case 'superadmin':
            $user = Sadmin::find($id);
            break;
        case 'user':
        default:
            $user = User::find($id);
            break;
    }

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Mise à jour des données du profil
    $user->nom = $request->input('nom', $user->nom);
    $user->prenom = $request->input('prenom', $user->prenom);
    $user->login = $request->input('login', $user->login);
    $user->email = $request->input('email', $user->email);

    if ($request->hasFile('profile_photo')) {
        $photo = $request->file('profile_photo');
        $photoPath = $photo->store('public/profile_photos');
        $photoPath = str_replace('public/', '/storage/', $photoPath);

        $user->profile_photo = $photoPath;
    }

    $user->save();

    return response()->json(['message' => 'Profile updated successfully', 'profile' => $user], 200);
}}