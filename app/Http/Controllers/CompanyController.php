<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\admin;

class CompanyController extends Controller
{
   
    public function index()
    {
        $companies = Company::all();
    
    
        return response()->json(['companies' => $companies], 200);
    }
    
 
  
  
    public function show($id)
{
    $company = Company::find($id);
    return response()->json(['company' => $company ], 200);
}

   public function update(Request $request, $id)
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
    if ($request->has('nom') && $request->input('nom') !== $company->nom) {
        $existingCompany = Company::where('nom', $request->input('nom'))->where('id', '!=', $id)->first();
        if ($existingCompany) {
            return response()->json(['message' => 'Company name already exists. Please choose a different name.'], 422);
        }
    }

    if ($request->hasFile('logo')) {
        $logo = $request->file('logo');
        $logoPath = $logo->store('logos'); 
        $company->logo = $logoPath; 
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
