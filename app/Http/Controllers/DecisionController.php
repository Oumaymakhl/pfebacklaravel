<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Support\Facades\Auth;
use App\Models\Decision;
use Illuminate\Http\Request;
use App\Models\Like;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
class DecisionController extends Controller
{
    public function index()
{
    $decisions = Decision::all();

    
    return response()->json(['decisions' => $decisions], 200);
}

    
public function store(Request $request)
{
    // Validation de la requête
    $request->validate([
        'title' => 'required',
        'description' => 'required',
    ]);

    // Extraction et vérification du token
    try {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid token'], 401);
    }

    // Vérification du type d'utilisateur
    $type = $payload['type'];
    $id = $payload['sub'];

    if ($type !== 'admin') {
        return response()->json(['error' => 'Only admins can create decisions.'], 403);
    }

    // Récupération de l'utilisateur admin
    $user = Admin::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Vérification de l'association de l'utilisateur avec une entreprise
    if (!$user->company_id) {
        return response()->json(['error' => 'Admin must be associated with a company to create decisions.'], 403);
    }

    // Création de la décision
    $decision = Decision::create([
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'company_id' => $user->company_id,
    ]);

    return response()->json(['message' => 'Decision created successfully', 'decision' => $decision], 201);
}


    public function show($id)
    {
        $decision = Decision::withCount('likes')->findOrFail($id);
        return response()->json(['decision' => $decision], 200);
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $decision = Decision::findOrFail($id);
        $decision->update($request->all());

        return response()->json(['message' => 'Decision updated successfully', 'decision' => $decision], 200);
    }

    public function destroy($id)
    {
        $decision = Decision::findOrFail($id);
        $decision->delete();

        return response()->json(['message' => 'Decision deleted successfully'], 200);
    }


    


    public function likeDecision($decisionId, Request $request)
    {
        // Extraction et vérification du token
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    
        // Vérification du type d'utilisateur et de son ID
        $userId = $payload['sub'];
        $userType = $payload['type'];
    
        // Récupération de l'utilisateur depuis la base de données
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Récupération de la décision
        $decision = Decision::findOrFail($decisionId);
    
        // Vérification si l'utilisateur est autorisé à liker la décision
        if ($userType === 'user' && $user->company_id === $decision->company_id) {
            // Vérifier si l'utilisateur a déjà liké ou disliké cette décision
            $existingLike = $decision->likes()->where('user_id', $userId)->first();
            if ($existingLike) {
                return response()->json(['error' => 'User has already liked or disliked this decision'], 400);
            }
    
            // Vérifier si l'utilisateur a déjà disliké cette décision
            $existingDislike = $decision->likes()->where('user_id', $userId)->where('dislike', true)->first();
            if ($existingDislike) {
                return response()->json(['error' => 'User has already disliked this decision'], 400);
            }
    
            // Ajouter le like associé à l'utilisateur
            $decision->likes()->create([
                'user_id' => $userId,
                'like' => true,
                'dislike' => false,
            ]);
    
            // Incrémenter le compteur de likes dans la table decisions
            $decision->increment('likes');
    
            return response()->json(['message' => 'Decision liked successfully'], 200);
        } else {
            return response()->json(['error' => 'User is not allowed to like this decision'], 403);
        }
    }
    
    public function dislikeDecision($decisionId, Request $request)
    {
        // Extraction et vérification du token
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    
        // Vérification du type d'utilisateur et de son ID
        $userId = $payload['sub'];
        $userType = $payload['type'];
    
        // Récupération de l'utilisateur depuis la base de données
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Récupération de la décision
        $decision = Decision::findOrFail($decisionId);
    
        // Vérification si l'utilisateur est autorisé à disliker la décision
        if ($userType === 'user' && $user->company_id === $decision->company_id) {
            // Vérifier si l'utilisateur a déjà liké ou disliké cette décision
            $existingLike = $decision->likes()->where('user_id', $userId)->first();
            if ($existingLike) {
                return response()->json(['error' => 'User has already liked or disliked this decision'], 400);
            }
    
            // Vérifier si l'utilisateur a déjà liké cette décision
            $existingLike = $decision->likes()->where('user_id', $userId)->where('like', true)->first();
            if ($existingLike) {
                return response()->json(['error' => 'User has already liked this decision'], 400);
            }
    
            // Ajouter le dislike associé à l'utilisateur
            $decision->likes()->create([
                'user_id' => $userId,
                'like' => false,
                'dislike' => true,
            ]);
    
            // Incrémenter le compteur de dislikes dans la table decisions
            $decision->increment('dislikes');
    
            return response()->json(['message' => 'Decision disliked successfully'], 200);
        } else {
            return response()->json(['error' => 'User is not allowed to dislike this decision'], 403);
        }
    }
    
}