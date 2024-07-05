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
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MeetingController;

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
Route::post('/chat/send', [ChatController::class, 'sendMessage']);
Route::get('/chat/group', [ChatController::class, 'getMessages']);
Route::post('/signup', [SadminController::class, 'signup']);
Route::post('/admin/signup', [AdminController::class, 'ajoutadmin']);
Route::post('/admin/login', [LoginController::class, 'authenticate']);
Route::post('/forget', [Controller::class, 'forgetpassword']);
Route::get('/companies/index', [CompanyController::class, 'index']); 
Route::get('/companies/show/{id}', [CompanyController::class, 'show']); 
Route::put('/companies/update/{id}', [CompanyController::class, 'update']); 
Route::delete('/companies/destroy/{id}', [CompanyController::class, 'destroy']); 
Route::get('/finds/{id}', [ReunionController::class, 'getEtat']);
Route::post('/finds', [ReunionController::class, 'setEtat']);

Route::post('/user/signup', [Controller::class, 'ajoutparticipant']); // Changed this line
Route::get('/user', [Controller::class, 'index']); // Liste des utilisateurs
Route::get('/user/{id}', [Controller::class, 'show']); // Afficher un utilisateur spécifique
Route::put('/user/{id}', [Controller::class, 'update']); // Mettre à jour les informations d'un utilisateur
Route::delete('/user/{id}', [Controller::class, 'destroy']); // Supprimer un utilisateur
Route::post('/reunion', [ReunionController::class, 'create']); // Create
Route::get('/reunions', [ReunionController::class, 'index']); // Read
Route::put('/reunions/{id}', [ReunionController::class, 'update']); // Update
Route::delete('/reunions/{id}', [ReunionController::class, 'destroy']); // Delete
Route::get('/companies/{companyId}/reunions/{reunionId}/invited-users', [ReunionController::class, 'getInvitedUsers']);
Route::post('/reunions/invite-users', [ReunionController::class, 'inviteUsers'])->name('reunions.inviteUsers');
Route::get('/reunion/{id}', [ReunionController::class, 'show']);
Route::post('reunion/{reunionId}/confirm-participation', [ReunionController::class, 'confirmParticipation']);
Route::post('/set-etat', [ReunionController::class, 'setEtat']);
Route::get('reunions/{id}/participants-status', [ReunionController::class, 'getEtat']);

Route::post('/reunion/{id}/confirm', [ReunionController::class, 'confirmParticipation']);
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

Route::post('/decisions/{decision}/like', [LikeController::class, 'likeDecision'])->name('decisions.like');
Route::post('/decisions/{decision}/dislike', [LikeController::class, 'dislikeDecision'])->name('decisions.dislike');


Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/user/task/{userId}', [TaskController::class,'findTasksByUser']);


Route::get('/statistics/totals', [StatisticController::class, 'getTotals']);
Route::get('/statistics/average-reunions-per-user', [StatisticController::class, 'getAverageReunionsPerUser']);
Route::get('/statistics/tasks-by-status', [StatisticController::class, 'getTasksByStatus']);
Route::get('/statistics/task-completion-rate-by-user', [StatisticController::class, 'taskCompletionRateByUser']);
Route::get('/statistics/average-tasks-per-user', [StatisticController::class, 'getAverageTasksPerUser']);
Route::get('/statistics/users-by-company', [StatisticController::class, 'getUsersByCompany']);
Route::get('/statistics/tasks-completed-vs-incomplete', [StatisticController::class, 'getTasksCompletedVsIncomplete']);
Route::get('/statistics/tasks-per-user', [StatisticController::class, 'getTasksPerUser']);
Route::get('/statistics/completed-tasks-per-user', [StatisticController::class, 'getCompletedTasksPerUser']);


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


Route::middleware('auth:api')->get('/user/tasks', 'TaskController@getUserTasks');




Route::post('/reset-password-request', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
Route::post('/change-password', [ChangePasswordController::class, 'passwordResetProcess']);

Route::get('company/details/{id}', [CompanyController::class, 'showCompanyDetails']);
Route::match(['get', 'post'], '/profile', [LoginController::class, 'profile']);
Route::put('/updateprofil', [LoginController::class, 'updateprofile']);

Route::get('/likes', [LikeController::class, 'index']); 
Route::get('/users/{id}/name', [Controller::class, 'getUserNameById']);

Route::post('documents/{documentId}/add-signature-and-download', [DocumentController::class, 'addSignatureAndDownload']);
Route::post('signatures/upload', [SignatureController::class, 'upload']);
Route::post('documents/export-with-signature', [DocumentController::class, 'exportDocumentWithSignature']);

Route::middleware('auth:api')->get('admin/company/users', [TaskController::class, 'getUsersByAdminCompanyId']);

Route::post('/meetings', [MeetingController::class, 'create'])->name('meetings.create');

// Récupérer toutes les réunions
Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');

// Mettre à jour une réunion
Route::put('/meetings/{id}', [MeetingController::class, 'update'])->name('meetings.update');

// Supprimer une réunion
Route::delete('/meetings/{id}', [MeetingController::class, 'destroy'])->name('meetings.destroy');
