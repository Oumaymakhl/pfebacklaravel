<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\admin;
use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Decision;
class LikeController extends Controller
{
 
    public function index()
    {
        $likes = Like::all();
        return response()->json(['likes' => $likes], 200);
    }
    

    public function likeDecision($decisionId, Request $request)
    {
        try {
            // Récupérer le token JWT et les informations de l'utilisateur
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            $userId = $payload['sub'];
            $userType = $payload['type'];
            
            // Trouver l'utilisateur
            $user = User::find($userId);
            
            // Vérifier si l'utilisateur existe
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            
            // Trouver la décision
            $decision = Decision::findOrFail($decisionId);
            
            // Vérifier si l'utilisateur est autorisé à aimer cette décision
            if ($userType === 'user' && $user->company_id === $decision->company_id) {
                // Vérifier si l'utilisateur a déjà aimé ou n'aime pas cette décision
                $existingLike = $decision->likes()->where('user_id', $userId)->first();
                if ($existingLike) {
                    return response()->json(['error' => 'User has already liked or disliked this decision'], 400);
                }
    
                $decision->likes()->create([
                    'user_id' => $userId,
                    'like' => true,
                    'dislike' => false,
                ]);
                
                // Mettre à jour le compteur de likes dans la table decisions
                $decision->increment('likes');
                
                // Récupérer les compteurs de likes et dislikes mis à jour
                $likesCount = $decision->likes()->where('like', true)->count();
                $dislikesCount = $decision->likes()->where('dislike', true)->count();
                
                // Retourner la réponse avec les compteurs de likes et dislikes
                return response()->json([
                    'message' => 'Decision liked successfully',
                    'likes_count' => $likesCount,
                    'dislikes_count' => $dislikesCount,
                ], 200);
            } else {
                return response()->json(['error' => 'User is not allowed to like this decision'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
    
    public function dislikeDecision($decisionId, Request $request)
    {
        try {
            // Récupérer le token JWT et les informations de l'utilisateur
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
            $userId = $payload['sub'];
            $userType = $payload['type'];
            
            // Trouver l'utilisateur
            $user = User::find($userId);
            
            // Vérifier si l'utilisateur existe
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            
            // Trouver la décision
            $decision = Decision::findOrFail($decisionId);
            
            // Vérifier si l'utilisateur est autorisé à ne pas aimer cette décision
            if ($userType === 'user' && $user->company_id === $decision->company_id) {
                // Vérifier si l'utilisateur a déjà aimé ou n'aime pas cette décision
                $existingLike = $decision->likes()->where('user_id', $userId)->first();
                if ($existingLike) {
                    return response()->json(['error' => 'User has already liked or disliked this decision'], 400);
                }
    
                // Créer un nouveau dislike pour cette décision
                $decision->likes()->create([
                    'user_id' => $userId,
                    'like' => false,
                    'dislike' => true,
                ]);
                
                // Mettre à jour le compteur de dislikes dans la table decisions
                $decision->increment('dislikes');
                
                // Récupérer les compteurs de likes et dislikes mis à jour
                $likesCount = $decision->likes()->where('like', true)->count();
                $dislikesCount = $decision->likes()->where('dislike', true)->count();
                
                // Retourner la réponse avec les compteurs de likes et dislikes
                return response()->json([
                    'message' => 'Decision disliked successfully',
                    'likes_count' => $likesCount,
                    'dislikes_count' => $dislikesCount,
                ], 200);
            } else {
                return response()->json(['error' => 'User is not allowed to dislike this decision'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }

}