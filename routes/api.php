<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SadminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReunionController;
use App\Http\Controllers\DocumentController;

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
Route::get('/finds/{id}', [ReunionController::class, 'getEtat']);
Route::post('/finds', [ReunionController::class, 'setEtat']);

Route::get('/user', [Controller::class, 'index']); // Liste des utilisateurs
Route::get('/user/{id}', [Controller::class, 'show']); // Afficher un utilisateur spécifique
Route::put('/user/{id}', [Controller::class, 'update']); // Mettre à jour les informations d'un utilisateur
Route::delete('/user/{id}', [Controller::class, 'destroy']); // Supprimer un utilisateur
Route::delete('/user/{id}', [Controller::class, 'destroy']); // Supprimer un utilisateur
Route::post('/reunions', [ReunionController::class, 'create_reunion']); // Create
Route::get('/reunions', [ReunionController::class, 'index']); // Read
Route::put('/reunions/{id}', [ReunionController::class, 'update']); // Update
Route::delete('/reunions/{id}', [ReunionController::class, 'destroy']); // Delete
Route::get('/companies/{companyId}/reunions/{reunionId}/invited-users', [ReunionController::class, 'getInvitedUsers']);
Route::post('/reunions/invite-users', [ReunionController::class, 'inviteUsers'])->name('reunions.inviteUsers');

Route::post('/documents/import', [DocumentController::class, 'importDocument']);
Route::get('/documents/{id}/export', [DocumentController::class, 'exportDocument']);
Route::get('/documents/{id}/sign', [DocumentController::class, 'signDocument']);
Route::get('/documents/{id}/download', [DocumentController::class, 'downloadSignedDocument']);
