<?php

// app/Http/Controllers/ReunionController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reunion;

class ReunionController extends Controller
{
    /* public function store(Request $request)
    {
        
    
    
        $data = $request->validate([
            'titre' => 'required',
            'description' => 'nullable',
            'date' => 'required|date',
            'id_admin' => 'required|id_admin',

        ]);

        $reunion = Reunion::create($data);
    
        return response()->json(['message' => 'Réunion créée avec succès', 'reunion' => $reunion], 201);

    } */
    
    public function create_reunion(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'description' => 'nullable',
            'date' => 'required',
            'id_admin' => 'required',
            'statut' =>'required'
        ]);

        $reunions = Reunion::create($request->all());

        return response()->json([
            'reunions' => $reunions,
            'message' => 'reunion created successfully.',
        ], 201);
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

        return response()->json(['message' => 'Réunion mise à jour avec succès', 'reunion' => $reunion], 200);
    }

    public function destroy($id)
    {
        $reunion = Reunion::findOrFail($id);
        $reunion->delete();

        return response()->json(['message' => 'Réunion supprimée avec succès'], 200);
    }
}

