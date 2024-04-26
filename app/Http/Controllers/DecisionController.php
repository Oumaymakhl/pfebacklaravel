<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use Illuminate\Http\Request;
use App\Models\Like;

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
        $decision = Decision::findOrFail($decisionId);

        // Ajouter le like pour la décision
        $decision->likes()->create([
            'user_id' => $request->user()->id,
            'like' => true,
            'dislike' => false,
        ]);

        return response()->json(['message' => 'Decision liked successfully'], 200);
    }

    public function dislikeDecision($decisionId, Request $request)
    {
        $decision = Decision::findOrFail($decisionId);

        // Ajouter le dislike pour la décision
        $decision->likes()->create([
            'user_id' => $request->user()->id,
            'like' => false,
            'dislike' => true,
        ]);

        return response()->json(['message' => 'Decision disliked successfully'], 200);
    }
    public function likeDecision2($decisionId, $userId)
    {
        $decision = Decision::findOrFail($decisionId);

        // Ajouter le like pour la décision
        $decision->likes()->create([
            'user_id' => $userId,
            'like' => true,
            'dislike' => false,
        ]);

        return response()->json(['message' => 'Decision liked successfully'], 200);
    }

    public function dislikeDecision2($decisionId, $userId)
    {
        $decision = Decision::findOrFail($decisionId);

        // Ajouter le dislike pour la décision
        $decision->likes()->create([
            'user_id' => $userId,
            'like' => false,
            'dislike' => true,
        ]);

        return response()->json(['message' => 'Decision disliked successfully'], 200);
    }
    public function getLikesForDecision(Decision $decision)
    {
        $likes = $decision->likes()->where('like', true)->get();
        return response()->json(['likes' => $likes], 200);
    }

    public function getDislikesForDecision(Decision $decision)
    {
        $dislikes = $decision->likes()->where('dislike', true)->get();
        return response()->json(['dislikes' => $dislikes], 200);
    }
}
