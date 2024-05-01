<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\admin;

class CompanyController extends Controller
{
   /* public function index()
    {
        $companies = Company::with('admins')->get();
        return response()->json($companies);
    }*/
    public function index()
    {
        $companies = Company::all();
    
    
        return response()->json(['companies' => $companies], 200);
    }
    
 
  
  /*public function show($id)
    {
        $company = Company::with('admins')->findOrFail($id);
        return response()->json($company);
    }*/
    public function show($id)
{
    $company = Company::find($id);
    return response()->json(['company' => $company ], 200);
}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   
    
 /* public function update(Request $request, $id)
  {
    $companies = Company::find($id);

    if (!$companies) {
        return response()->json(['message' => 'company not found.'], 404);
    }

    $data =$request->validate([
          'nom' => 'required',
          'subdomaine' => 'required',
          'logo' => 'nullable',
          'adresse' => 'required'
        
      ]);

     

      $companies->update( $data);

      return response()->json(['message' => 'company updated successfully', 'company' => $companies], 200);
    
  }*/public function update(Request $request, $id)
{
    $company = Company::find($id);

    if (!$company) {
        return response()->json(['message' => 'Company not found.'], 404);
    }

    $request->validate([
        'nom' => 'required',
        'subdomaine' => 'required',
        'adresse' => 'required'
    ]);
 // Traitement du logo
 if ($request->hasFile('logo')) {
        $logo = $request->file('logo');
        $logoPath = $logo->store('logos'); // Stocker le fichier dans le répertoire 'storage/app/logos'
        $company->logo = $logoPath; // Enregistrez le chemin du fichier (URL) dans la base de données
    }

 
    $company->nom = $request->input('nom');
    $company->subdomaine = $request->input('subdomaine');
    $company->adresse = $request->input('adresse');

    $company->save();

    return response()->json(['message' => 'Company updated successfully', 'company' => $company], 200);
}
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
    
        $admin = $company->admin;
    
        $company->delete();
    
        if ($company->admin) {
            $company->admin->delete();
        }

        
        return response()->json([ 'message' => 'Entreprise supprimée avec succès.'],
         204);
    } 
    
}