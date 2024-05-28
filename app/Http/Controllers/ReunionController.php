<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reunion;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\Auth;
class ReunionController extends Controller
{

    public function create(Request $request)
    {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
        $type = $payload['type'];
        $id = $payload['sub'];
        // Validation des données
      $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required',
            'participants.*' => 'required', // Assurez-vous que les ID des utilisateurs sont des entiers
        ]);
      // return response()->json($request);
        // Création de la réunion
        $reunion = Reunion::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date' => $request->date,
            'id_admin' => $id, // Assigner l'id de l'administrateur connecté à id_admin
        ]);
    
        // Ajouter les utilisateurs invités
      $users = $request->input('participants');
        $reunion->users()->attach($users);
    
        // Retourner une réponse
        return response()->json(['message' => 'Réunion créée avec succès', 'reunion' => $reunion], 200);
    }
    
    public function index()
    {
        $reunions = Reunion::all();

        return response()->json(['reunions' => $reunions], 200);
    }

    public function update(Request $request, $id)
    {
        $reunion = Reunion::findOrFail($id);

        $data = $request->validate([
            'titre' => 'required',
            'description' => 'nullable',
            'date' => 'required|date',
        ]);

        $reunion->update($data);

        return response()->json(['message' => 'Reunion updated successfully', 'reunion' => $reunion], 200);
    }

    public function destroy($id)
    {
        $reunion = Reunion::findOrFail($id);
        $reunion->delete();

        return response()->json(['message' => 'Reunion deleted successfully'], 200);
    }

    public function getInvitedUsers($companyId, $reunionId)
    {
        $reunion = Reunion::findOrFail($reunionId);

        $invitedUsers = User::where('company_id', $companyId)->get();

        return response()->json(['invited_users' => $invitedUsers, 'reunion' => $reunion], 200);
    }
    

    public function getEtat($id)
    {
        $reunion = Reunion::find($id);
    
       
        if (!$reunion) {
            return response()->json(['error' => 'Reunion not found'], 404); // Or handle the case where the reunion doesn't exist
        }
    
        // Retrieve users with their status for this reunion
        $users = DB::table('presence')
                    ->where('reunion_id', $id)
                    ->join('users', 'presence.user_id', '=', 'users.id')
                    ->select('users.*', 'presence.status')
                    ->get();
    
        // Assuming you want to return the users with their status as JSON
        return response()->json(['users' => $users], 200);
    }
    public function setEtat(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'user_id'=>'required',
            'status' => 'required',
            'raison' => 'required',
            'reunion_id' => 'required', // Ensure that the provided reunion_id exists in the reunions table
        ]);
    
        // Retrieve the user object
        $user = User::find($request->input('user_id'));
    
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Retrieve the reunion object
        $reunionId = $request->input('reunion_id');
    
        // Check if the reunion exists
        $reunionExists = DB::table('reunions')->where('id', $reunionId)->exists();
        if (!$reunionExists) {
            return response()->json(['error' => 'Reunion not found'], 404);
        }
    
        DB::table('presence')->updateOrInsert(
            ['user_id' => $user->id, 'reunion_id' => $reunionId],
            ['status' => $request->input('status'), 'raison' => $request->input('raison')]
        );
    
        return response()->json(['message' => 'Status updated successfully'], 200);
    }

    public function inviteUsers(Request $request)
    {
        
        $request->validate([
            'reunion_id' => 'required',
            'user_id' => 'required',
        ]);
        try {
            
            $reunion = Reunion::find($request->reunion_id);
            $userId = $request->user_id;
            
            $user = User::find($userId);    
                Mail::to($user->email)->send(new InvitationMail($reunion));
            
               return response()->json(['message' => 'Invitations sent successfully to ',$user->email ], 200);
        } catch (\Exception $e) {
            
            return response()->json(['message' => 'Failed to send invitations', 'error' => $e->getMessage()], 500);
        }
    }
    
}