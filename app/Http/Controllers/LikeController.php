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
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    
        $userId = $payload['sub'];
        $userType = $payload['type'];
    
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        $decision = Decision::findOrFail($decisionId);
    
        if ($userType === 'user' && $user->company_id === $decision->company_id) {
            $existingLike = $decision->likes()->where('user_id', $userId)->first();
            if ($existingLike) {
                return response()->json(['error' => 'User has already liked or disliked this decision'], 400);
            }
    
            // Vérifier si l'utilisateur a déjà disliké cette décision
            $existingDislike = $decision->likes()->where('user_id', $userId)->where('dislike', true)->first();
            if ($existingDislike) {
                return response()->json(['error' => 'User has already disliked this decision'], 400);
            }
    
            $decision->likes()->create([
                'user_id' => $userId,
                'like' => true,
                'dislike' => false,
            ]);
    
            $decision->increment('likes');
    
            return response()->json(['message' => 'Decision liked successfully'], 200);
        } else {
            return response()->json(['error' => 'User is not allowed to like this decision'], 403);
        }
    }
    
    public function dislikeDecision($decisionId, Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    
        $userId = $payload['sub'];
        $userType = $payload['type'];
    
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        $decision = Decision::findOrFail($decisionId);
    
        if ($userType === 'user' && $user->company_id === $decision->company_id) {
            $existingLike = $decision->likes()->where('user_id', $userId)->first();
            if ($existingLike) {
                return response()->json(['error' => 'User has already liked or disliked this decision'], 400);
            }
    
            $existingLike = $decision->likes()->where('user_id', $userId)->where('like', true)->first();
            if ($existingLike) {
                return response()->json(['error' => 'User has already liked this decision'], 400);
            }
    
            $decision->likes()->create([
                'user_id' => $userId,
                'like' => false,
                'dislike' => true,
            ]);
    
            $decision->increment('dislikes');
    
            return response()->json(['message' => 'Decision disliked successfully'], 200);
        } else {
            return response()->json(['error' => 'User is not allowed to dislike this decision'], 403);
        }
    }
    
}
    
    
    
