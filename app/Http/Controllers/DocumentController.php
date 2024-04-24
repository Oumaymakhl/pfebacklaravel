<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use setasign\Fpdi\Fpdi;
class DocumentController extends Controller
{
    public function importDocument(Request $request)
    {
        // Valider le fichier envoyé
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048', // PDF et taille maximale de 2 Mo
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
}public function signDocument($documentId)
{
    try {
        // Récupérer le document à signer
        $document = Document::findOrFail($documentId);

        // Créer une nouvelle instance FPDI
        $pdf = new Fpdi();
        $pdf->AddPage();

        // Charger le document PDF existant
        $pdf->setSourceFile(storage_path('app/' . $document->path));
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId, 0, 0);

        // Ajouter une nouvelle page pour la signature
        $pdf->AddPage();

        // Charger l'image de la signature
        $signatureImagePath = public_path('sign.png');
        $pdf->Image($signatureImagePath, 10, 10, 40, 15); // Adjust the coordinates and dimensions as needed

        // Générer le PDF signé
        $outputPath = storage_path('signed_document.pdf');
        $pdf->Output($outputPath, 'F');

        // Retourner le chemin du document signé
        return response()->json(['message' => 'Document signed successfully', 'signed_document_path' => $outputPath]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function downloadSignedDocument($documentId)
{
    // Récupérer le document signé
    $document = Document::findOrFail($documentId);

    // Retourner le document signé en tant que réponse de téléchargement
    return response()->download(storage_path('signed_document.pdf'));
}
public function showDocuments()
{
    // Récupérer tous les documents enregistrés dans la base de données
    $documents = Document::all();

    // Retourner les documents en tant que réponse JSON
    return response()->json($documents);
}

}
