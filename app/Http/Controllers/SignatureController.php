<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function upload(Request $request)
    {
        // Valider et enregistrer la signature
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Assurez-vous que le fichier est une image
        ]);

        // Enregistrer la signature dans le stockage
        $signaturePath = $request->file('signature')->store('signatures');

        // Retourner le chemin de la signature
        return response()->json(['signature_path' => $signaturePath]);
    }
}
