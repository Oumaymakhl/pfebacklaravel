<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckCompanyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $companyId = $request->route('id');
        $company = Company::find($companyId);

        if (!$company || Auth::user()->company_id != $companyId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}