<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SadminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/sadmin/signup', [SadminController::class, 'signup']);
Route::post('/sadmin/login', [SadminController::class, 'login']);
Route::post('/admin/signup', [AdminController::class, 'signup']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/user/signup', [Controller::class, 'signup']);
Route::post('/user/login', [Controller::class, 'login']);

 Route::get('/companies/index', [CompanyController::class, 'index']); 
 Route::get('/companies/show/{id}', [CompanyController::class, 'show']); 
Route::put('/companies/update/{id}', [CompanyController::class, 'update']); 
Route::delete('/companies/destroy/{id}', [CompanyController::class, 'destroy']); 

