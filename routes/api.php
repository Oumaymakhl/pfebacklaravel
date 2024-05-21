<?php
use \App\Http\Middleware\CheckCompanyAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SadminController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReunionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LikeController;

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
Route::post('/admin/login', [AdminController::class, 'authenticate']);
Route::post('/forget', [Controller::class, 'forgetpassword']);
Route::post('/user/login', [Controller::class, 'login']);
Route::get('/companies/index', [CompanyController::class, 'index']); 
Route::get('/companies/show/{id}', [CompanyController::class, 'show']); 
Route::put('/companies/update/{id}', [CompanyController::class, 'update']); 
Route::delete('/companies/destroy/{id}', [CompanyController::class, 'destroy']); 
Route::get('/finds/{id}', [ReunionController::class, 'getEtat']);
Route::post('/finds', [ReunionController::class, 'setEtat']);

Route::post('/user/signup', [Controller::class, 'signup']); // Changed this line
Route::get('/user', [Controller::class, 'index']); // Liste des utilisateurs
Route::get('/user/{id}', [Controller::class, 'show']); // Afficher un utilisateur spécifique
Route::put('/user/{id}', [Controller::class, 'update']); // Mettre à jour les informations d'un utilisateur
Route::delete('/user/{id}', [Controller::class, 'destroy']); // Supprimer un utilisateur
Route::post('/reunions', [ReunionController::class, 'create_reunion']); // Create
Route::get('/reunions', [ReunionController::class, 'index']); // Read
Route::put('/reunions/{id}', [ReunionController::class, 'update']); // Update
Route::delete('/reunions/{id}', [ReunionController::class, 'destroy']); // Delete
Route::get('/companies/{companyId}/reunions/{reunionId}/invited-users', [ReunionController::class, 'getInvitedUsers']);
Route::post('/reunions/invite-users', [ReunionController::class, 'inviteUsers'])->name('reunions.inviteUsers');

Route::post('import-document', [DocumentController::class, 'importDocument']);
Route::get('export-document/{documentId}', [DocumentController::class, 'exportDocument']);
Route::get('download-signed-document/{documentId}', [DocumentController::class, 'downloadSignedDocument']);
Route::post('sign-document/{documentId}', [DocumentController::class, 'signDocument']); 
Route::get('/documents/{documentId}/sign-and-download', [DocumentController::class, 'signAndDownloadDocument']);
Route::get('show-documents', [DocumentController::class, 'showDocuments']);
Route::get('/decisions', [DecisionController::class, 'index']);
Route::post('/decisions', [DecisionController::class, 'store']);
Route::get('/decisions/{id}', [DecisionController::class, 'show']);
Route::put('/decisions/{id}', [DecisionController::class, 'update']);
Route::delete('/decisions/{id}', [DecisionController::class, 'destroy']);
// Route pour récupérer les likes d'une décision spécifique
// routes/api.php


//Route::get('decisions/{decision}/likes', [LikeController::class, 'getLikesForDecision']);
//Route::get('decisions/{decision}/dislikes', [LikeController::class, 'getDislikesForDecision']);


// Route pour liker une décision
// Route pour liker une décision
Route::post('/decisions/{decision}/like', [DecisionController::class, 'likeDecision'])->middleware('auth');

// Route pour disliker une décision
Route::post('/decisions/{decision}/dislike', [DecisionController::class, 'dislikeDecision'])->middleware('auth');


Route::post('/decisions2/{decision}/like/{userId}', [DecisionController::class, 'likeDecision2']);

// Route pour disliker une décision
Route::post('/decisions2/{decision}/dislike/{userId}', [DecisionController::class, 'dislikeDecision2']);

Route::get('/tasks', [TaskController::class, 'index']); // Afficher toutes les tâches
Route::post('/tasks', [TaskController::class, 'store']); // Créer une nouvelle tâche
Route::get('/tasks/{id}', [TaskController::class, 'show']); // Afficher une tâche spécifique
Route::put('/tasks/{id}', [TaskController::class, 'update']); // Mettre à jour une tâche
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); // Supprimer une tâche
Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);


Route::get('/statistics/totals', [StatisticController::class, 'getTotals']);
Route::get('/statistics/average-reunions-per-user', [StatisticController::class, 'getAverageReunionsPerUser']);
Route::get('/statistics/tasks-by-status', [StatisticController::class, 'getTasksByStatus']);
Route::get('/statistics/task-completion-rate-by-user', [StatisticController::class, 'taskCompletionRateByUser']);
Route::get('/statistics/average-tasks-per-user', [StatisticController::class, 'getAverageTasksPerUser']);
Route::get('/statistics/users-by-company', [StatisticController::class, 'getUsersByCompany']);
Route::get('/statistics/tasks-completed-vs-incomplete', [StatisticController::class, 'getTasksCompletedVsIncomplete']);
Route::get('/statistics/tasks-per-user', [StatisticController::class, 'getTasksPerUser']);
Route::get('/statistics/completed-tasks-per-user', [StatisticController::class, 'getCompletedTasksPerUser']);

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
Route::post('user/logout', [Controller::class, 'userLogout']);
Route::post('admin/logout', [AdminController::class, 'adminLogout']);
Route::put('/tasks/{id}/calculate-time-spent', [TaskController::class, 'calculateTimeSpent']);
Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
Route::post('user/logout', [Controller::class, 'userLogout']);
Route::post('admin/logout', [AdminController::class, 'adminLogout']);
Route::put('/tasks/{id}/calculate-time-spent', [TaskController::class, 'calculateTimeSpent']);
Route::patch('/tasks/{id}', [TaskController::class, 'updateStatus']);



Route::post('/reset-password-request', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
Route::post('/change-password', [ChangePasswordController::class, 'passwordResetProcess']);

Route::get('company/details/{id}', [CompanyController::class, 'showCompanyDetails']);
Route::get('/profile', [AdminController::class, 'profile']);
Route::match(['get', 'post'], '/profile', [adminController::class, 'profile']);
Route::put('/updateprofil', [adminController::class, 'updateprofile']);

Route::get('/likes', [LikeController::class, 'index']); // Route pour récupérer tous les likes
Route::get('/users/{id}/name', [Controller::class, 'getUserNameById']);
