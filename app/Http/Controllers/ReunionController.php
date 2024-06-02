<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Reunion;
use App\Models\Presence;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log; 

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
            'titre' => 'required',
            'description' => 'nullable|string',
            'date' => 'required|date', // Validation de la date
            'participants.*' => 'required', // Assurez-vous que les ID des utilisateurs sont des entiers
        ]);
        
        $date = Carbon::parse($request->date);
        
        $reunion = Reunion::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date' => $date, 
            'id_admin' => $id, // Assigner l'id de l'administrateur connecté à id_admin
        ]);
        
        $users = $request->input('participants');
        $reunion->users()->attach($users);
        
        foreach ($users as $userId) {
            $user = User::find($userId);
            if ($user) {
                Mail::to($user->email)->send(new InvitationMail($reunion, $userId));
            }
        }
                return response()->json(['message' => 'Réunion créée avec succès et invitations envoyées', 'reunion' => $reunion], 200);
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
            return response()->json(['error' => 'Reunion not found'], 404);
        }
    
        $users = DB::table('presence')
                    ->where('reunion_id', $id)
                    ->join('users', 'presence.user_id', '=', 'users.id')
                    ->select('users.*', 'presence.status', 'presence.raison')
                    ->get();
    
        return response()->json(['users' => $users], 200);
    }
    
   
public function show($id)
{
    $reunion = Reunion::findOrFail($id);

    if ($reunion) {
        return response()->json(['reunion' => $reunion], 200);
    } else {
        return response()->json(['message' => 'Réunion non trouvée'], 404);
    }
}

public function confirmParticipation(Request $request, $reunionId) {
    $request->validate([
        'userId' => 'required',
        'status' => 'required|boolean', // Assurez-vous que status est un booléen
        'raison' => 'nullable|string',
    ]);

    $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN);

    Presence::create([
        'reunion_id' => $reunionId,
        'user_id' => $request->input('userId'),
        'status' => $status,
        'raison' => $request->input('raison'),
    ]);

    return response()->json(['message' => 'Confirmation de participation enregistrée avec succès'], 200);
}
}