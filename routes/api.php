<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SadminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReunionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\LoginController;
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

Route::get('/decisions', [DecisionController::class, 'index']);
Route::post('/decisions', [DecisionController::class, 'store']);
Route::get('/decisions/{id}', [DecisionController::class, 'show']);
Route::put('/decisions/{id}', [DecisionController::class, 'update']);
Route::delete('/decisions/{id}', [DecisionController::class, 'destroy']);
// Route pour récupérer les likes d'une décision spécifique
Route::get('/decisions/{decision}/likes', [DecisionController::class, 'getLikesForDecision']);

// Route pour récupérer les dislikes d'une décision spécifique
Route::get('/decisions/{decision}/dislikes', [DecisionController::class, 'getDislikesForDecision']);

// Route pour liker une décision
Route::post('/decisions/{decision}/like', [DecisionController::class, 'likeDecision']);

// Route pour disliker une décision
Route::post('/decisions/{decision}/dislike', [DecisionController::class, 'dislikeDecision']);
Route::post('/decisions2/{decision}/like/{userId}', [DecisionController::class, 'likeDecision2']);

// Route pour disliker une décision
Route::post('/decisions2/{decision}/dislike/{userId}', [DecisionController::class, 'dislikeDecision2']);

Route::get('/tasks', [TaskController::class, 'index']); // Afficher toutes les tâches
Route::post('/tasks', [TaskController::class, 'store']); // Créer une nouvelle tâche
Route::get('/tasks/{id}', [TaskController::class, 'show']); // Afficher une tâche spécifique
Route::put('/tasks/{id}', [TaskController::class, 'update']); // Mettre à jour une tâche
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // Supprimer une tâche

Route::get('/statistics/totals', [StatisticController::class, 'getTotals']);
Route::get('/statistics/average-reunions-per-user', [StatisticController::class, 'getAverageReunionsPerUser']);
Route::get('/statistics/tasks-by-status', [StatisticController::class, 'getTasksByStatus']);
Route::get('/statistics/task-completion-rate-by-user', [StatisticController::class, 'taskCompletionRateByUser']);


Route::post('user/logout', [Controller::class, 'userLogout']);
Route::post('admin/logout', [AdminController::class, 'adminLogout']);

Route::put('/tasks/{id}/calculate-time-spent', [TaskController::class, 'calculateTimeSpent']);
Route::post('/login', [LoginController::class, 'authenticate']);

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);