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
    $request->validate([
        'title' => 'required',
        'description' => 'required',
    ]);

    try {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid token'], 401);
    }

    $type = $payload['type'];
    $id = $payload['sub'];

    if ($type !== 'admin') {
        return response()->json(['error' => 'Only admins can create decisions.'], 403);
    }

    $user = Admin::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if (!$user->company_id) {
        return response()->json(['error' => 'Admin must be associated with a company to create decisions.'], 403);
    }

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


    


}