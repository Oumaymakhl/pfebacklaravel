<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Stream;
use Illuminate\Support\Facades\File;


class DocumentController extends Controller
{
    public function importDocument(Request $request)
    {
        // Valider le fichier envoyé
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120', // 5 Mo (5 * 1024)
         
        ]);
        // Stocker le fichier dans le stockage Laravel
        $filePath = $request->file('file')->store('documents');
    
        // Enregistrer les informations sur le document dans la base de données
        $document = new Document();
        $document->name = $request->file('file')->getClientOriginalName();
        $document->path = $filePath;
        $document->save();
    
        return response()->json(['message' => 'Document imported successfully', 'document' => $document], 201);
    }
    public function exportDocument($documentId)
{
    $document = Document::findOrFail($documentId);

    // Retourner le fichier PDF à télécharger
    return response()->download(storage_path('app/' . $document->path));
} public function signAndDownloadDocument($documentId)
{
    try {
        // Récupérer le document à signer à partir de l'identifiant
        $document = Document::findOrFail($documentId);

        // Vérifier si le fichier PDF existe dans le chemin spécifié
        if (!Storage::exists($document->path)) {
            throw new \Exception('Le fichier PDF n\'existe pas dans le chemin spécifié.');
        }

        // Créer une nouvelle instance FPDI
        $pdf = new Fpdi();

        // Charger le document PDF existant
        $pageCount = $pdf->setSourceFile(Storage::path($document->path));

        // Importer toutes les pages du document PDF d'origine
        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $tplId = $pdf->importPage($pageNumber);
            $pdf->AddPage();
            $pdf->useTemplate($tplId, 0, 0);
        }

        // Charger l'image de la signature
        $signatureImagePath = public_path('sign.png');

        // Ajouter une nouvelle page pour la signature
        $pdf->AddPage();
        $pdf->Image($signatureImagePath, 10, 10, 40, 15); // Ajuster les coordonnées et les dimensions si nécessaire

        // Générer le PDF signé temporairement
        $tempFileName = 'signed_' . $document->name;
        $tempFilePath = storage_path('app/signed_documents/' . $tempFileName);
        $pdf->Output($tempFilePath, 'F');

        // Retourner le fichier signé en tant que réponse de téléchargement
        return response()->download($tempFilePath, $tempFileName)->deleteFileAfterSend();

    } catch (\Exception $e) {
        // En cas d'erreur, retourner une réponse d'erreur
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

 // Chemin de destination pour le téléchargement
public function showDocuments()
{
    // Récupérer tous les documents enregistrés dans la base de données
    $documents = Document::all();

    // Retourner les documents en tant que réponse JSON
    return response()->json($documents);
}

}
