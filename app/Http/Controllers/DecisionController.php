<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Decision;
use Illuminate\Http\Request;
use App\Models\Like;
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
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $decision = Decision::create($request->all());
        return response()->json(['message' => 'Decision created successfully', 'decision' => $decision], 201);
    }

    public function show($id)
    {
        $decision = Decision::findOrFail($id);
       
        $decision->likes_count = $decision->likes;
        $decision->dislikes_count = $decision->dislikes;
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
        // Vérifier l'authentification de l'utilisateur
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = Auth::id();
    
        // Récupérer la décision
        $decision = Decision::findOrFail($decisionId);
    
        // Ajouter le like associé à l'utilisateur
        $decision->likes()->create([
            'user_id' => $userId,
            'like' => true,
            'dislike' => false,
        ]);
    
        // Mettre à jour le compteur de likes
        $decision->increment('likes_count');
    
        return response()->json(['message' => 'Decision liked successfully'], 200);
    }
    
    public function dislikeDecision($decisionId, Request $request)
    {
        // Vérifier l'authentification de l'utilisateur
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = Auth::id();
    
        // Récupérer la décision
        $decision = Decision::findOrFail($decisionId);
    
        // Ajouter le dislike associé à l'utilisateur
        $decision->likes()->create([
            'user_id' => $userId,
            'like' => false,
            'dislike' => true,
        ]);
    
        // Mettre à jour le compteur de dislikes
        $decision->increment('dislikes_count');
    
        return response()->json(['message' => 'Decision disliked successfully'], 200);
    }

    
}    
    
