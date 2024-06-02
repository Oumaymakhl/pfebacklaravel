<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingInvitation;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeetingController extends Controller
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
        'date' => 'required|date',
        'link' => 'nullable|url',
        'participants.*' => 'required|exists:users,id',
    ]);

    // Création de la réunion
    $meeting = Meeting::create([
        'titre' => $request->titre,
        'description' => $request->description,
        'date' => $request->date,
        'link' => $request->link,
        'id_admin' => $id, // Assigner l'id de l'administrateur connecté à id_admin
    ]);

    // Ajouter les utilisateurs invités
    $users = $request->input('participants');
    $meeting->users()->attach($users);

    // Envoyer des emails aux participants
  
    foreach ($users as $userId) {
        $user = User::find($userId);
        if ($user) {
            Mail::to($user->email)->send(new MeetingInvitation($meeting));
        }
    }
    // Retourner une réponse
    return response()->json(['message' => 'Réunion créée avec succès et invitations envoyées', 'meeting' => $meeting], 200);
}


    public function index()
    {
        $meetings = Meeting::all();
        return response()->json(['meetings' => $meetings], 200);
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'link' => 'required|url',
        ]);

        $meeting->update($data);

        return response()->json(['message' => 'Meeting updated successfully', 'meeting' => $meeting], 200);
    }

    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return response()->json(['message' => 'Meeting deleted successfully'], 200);
    }

    public function getInvitedUsers($companyId, $meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);

        $invitedUsers = User::where('company_id', $companyId)->get();

        return response()->json(['invited_users' => $invitedUsers, 'meeting' => $meeting], 200);
    }

    
    public function inviteUsers(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $meeting = Meeting::find($request->meeting_id);
            $user = User::find($request->user_id);
    
            if ($user) {
                Mail::to($user->email)->send(new InvitationMail($meeting));
            }
    
            return response()->json(['message' => 'Invitation sent successfully to ' . $user->email], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send invitation', 'error' => $e->getMessage()], 500);
        }
    }
}
