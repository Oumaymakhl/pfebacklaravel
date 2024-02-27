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
        $companies = Company::with('admin')->get();
        return response()->json($companies);
    }
   
 
  
  public function show($id)
    {
        $company = Company::with('admin')->findOrFail($id);
        return response()->json($company);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
  public function update(Request $request, $id)
  {
      $request->validate([
          'nom' => 'required',
          'adresse' => 'required',
          'subdomaine' => 'required',
          'logo' => 'required'
      ]);

      $companies = Company::find($id);

      if (!$companies) {
          return response()->json(['message' => 'company not found.'], 404);
      }

      $companies->update($request->all());

      return response()->json([
          'companies' => $companies,
          'message' => 'companies updated successfully.',
      ], 200);
  }
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
    
        $admin = $company->admin;
    
        $company->delete();
    
        
        if ($admin) {
            $admin->delete();
        }
    
        return response()->json(null, 204);
    }
}
