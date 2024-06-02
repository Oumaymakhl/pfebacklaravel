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
   
    public function exportDocumentWithSignature(Request $request)
    {
        $request->validate([
            'documentId' => 'required|exists:documents,id',
            'signature' => 'required|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        $document = Document::findOrFail($request->documentId);
        $signatureFilePath = $request->file('signature')->store('signatures');

        $documentWithSignature = $this->addSignatureToDocument($document->path, $signatureFilePath);

        return response()->streamDownload(function () use ($documentWithSignature) {
            echo $documentWithSignature;
        }, 'document_signed.pdf');
    }

    private function addSignatureToDocument($documentPath, $signatureFilePath)
    {
        $documentFullPath = storage_path('app/' . $documentPath);
        $signatureFullPath = storage_path('app/' . $signatureFilePath);

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($documentFullPath);

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $templateId = $pdf->importPage($pageNumber);
            $pdf->addPage();
            $pdf->useTemplate($templateId);

            // Ajouter la signature uniquement sur la dernière page
            if ($pageNumber == $pageCount) {
                $pdf->Image($signatureFullPath, 10, 250, 50, 30); // Ajustez les coordonnées et dimensions si nécessaire
            }
        }

        return $pdf->Output('S');
    }

public function showDocuments()
{
    // Récupérer tous les documents enregistrés dans la base de données
    $documents = Document::all();

    // Retourner les documents en tant que réponse JSON
    return response()->json($documents);
}
public function exportDocument($documentId)
{
    $document = Document::findOrFail($documentId);

    return response()->download(storage_path('app/' . $document->path));
}
}
