<?php
    namespace App\Http\Controllers;
    
    use Illuminate\Support\Facades\Hash;
    
    use Tymon\JWTAuth\Facades\JWTAuth;
    use Illuminate\Http\Request;
    use App\Models\admin;
    
    use App\Models\Company;
    
    use Illuminate\Support\Facades\Log; 
    use App\Models\User;
    
    
    use App\Models\Sadmin;
class LoginController extends Controller
{
    
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
                    $user = User::where('login', $username)->first();            }
        
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
               
                $token = JWTAuth::claims(['type' => $type,'group'=>$user->company_id])->fromUser($user);
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