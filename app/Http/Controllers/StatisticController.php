<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reunion;
use App\Models\User;
use App\Models\Task;
use App\Models\Admin;
use App\Models\Decision;
use App\Models\Document;
use App\Models\Like;
use App\Models\company;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth; 

class StatisticController extends Controller
{
   // Fonction pour obtenir le nombre total de réunions, tâches et utilisateurs
   public function getTotals()
   {
       $reunionsCount = Reunion::count();
       $tasksCount = Task::count();
       $usersCount = User::count();

       return response()->json([
           'reunions_count' => $reunionsCount,
           'tasks_count' => $tasksCount,
           'users_count' => $usersCount,
       ], 200);
   }

   // Fonction pour obtenir la moyenne du nombre de réunions par utilisateur
   public function getAverageReunionsPerUser()
   {
       $averageReunionsPerUser = Reunion::count() / User::count();

       return response()->json([
           'average_reunions_per_user' => $averageReunionsPerUser,
       ], 200);
   }

   // Fonction pour obtenir le nombre total de tâches pour chaque statut
   public function getTasksByStatus()
   {
       $tasksByStatus = Task::select('status', \DB::raw('count(*) as count'))
                           ->groupBy('status')
                           ->get();

       return response()->json([
           'tasks_by_status' => $tasksByStatus,
       ], 200);
   }
   public function taskCompletionRateByUser()
    {
        $users = User::withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
            $query->where('status', 'completed');
        }])->get();

        return response()->json(['task_completion_rate_by_user' => $users], 200);
    }

    public function getAverageTasksPerUser()
{
    $averageTasksPerUser = Task::count() / User::count();

    return response()->json([
        'average_tasks_per_user' => $averageTasksPerUser,
    ], 200);
}
public function getUsersByCompany()
{
    $usersByCompany = User::select('company_id', \DB::raw('count(*) as count'))
                           ->groupBy('company_id')
                           ->get();

    return response()->json([
        'users_by_company' => $usersByCompany,
    ], 200);
}
public function getTasksCompletedVsIncomplete()
{
    $tasksCompleted = Task::where('status', 'completed')->count();
    $tasksToDo = Task::where('status', 'to_do')->count();

    return response()->json([
        'completed_tasks_count' => $tasksCompleted,
        'tasks_to_do_count' => $tasksToDo,
    ], 200);
}
public function getTasksPerUser()
{
    $tasksPerUser = Task::select('user_id', \DB::raw('count(*) as count'))
                        ->groupBy('user_id')
                        ->get();

    return response()->json([
        'tasks_per_user' => $tasksPerUser,
    ], 200);
}
public function getCompletedTasksPerUser()
{
    $completedTasksPerUser = Task::where('status', 'completed')
                                  ->select('user_id', \DB::raw('count(*) as count'))
                                  ->groupBy('user_id')
                                  ->get();

    return response()->json([
        'completed_tasks_per_user' => $completedTasksPerUser,
    ], 200);
}
 // Function to get the count of admins
 public function getAdminCount()
 {
     $adminCount = Admin::count();
     return response()->json(['admin_count' => $adminCount], 200);
 }

 // Function to get the count of documents
 public function getDocumentCount()
 {
     $documentCount = Document::count();
     return response()->json(['document_count' => $documentCount], 200);
 }

 // Function to get the count of decisions
 public function getDecisionCount()
 {
     $decisionCount = Decision::count();
     return response()->json(['decision_count' => $decisionCount], 200);
 }

 // Function to compare likes and dislikes
 public function getLikeDislikeComparison()
 {
     $likeCount = Like::where('like', 1)->count();
     $dislikeCount = Like::where('like', 0)->count();

     return response()->json([
         'like_count' => $likeCount,
         'dislike_count' => $dislikeCount,
     ], 200);
 }
 
 public function getDecisionCountAdmin(Request $request)
{
    try {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid token'], 401);
    }

    $type = $payload['type'];
    $adminId = $payload['sub'];

    if ($type !== 'admin') {
        return response()->json(['error' => 'Only admins can get decision count.'], 403);
    }

    // Récupérer l'administrateur associé à l'ID
    $admin = Admin::find($adminId);

    // Vérifier si l'administrateur existe et s'il est associé à une entreprise
    if (!$admin || !$admin->company_id) {
        return response()->json(['error' => 'Admin must be associated with a company.'], 403);
    }

    // Récupérer les décisions associées à l'entreprise de l'administrateur
    $decisionCount = Decision::where('company_id', $admin->company_id)->count();

    return response()->json(['decision_count' => $decisionCount], 200);
}


    public function getTaskCountAdmin(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $type = $payload['type'];
        $id = $payload['sub'];

        if ($type !== 'admin') {
            return response()->json(['error' => 'Only admins can view this information.'], 403);
        }

        // Récupérer l'administrateur associé à l'ID
        $admin = Admin::find($id);

        // Vérifier si l'administrateur a une entreprise associée
        if (!$admin || !$admin->company_id) {
            return response()->json(['error' => 'Admin must be associated with a company.'], 403);
        }

        // Récupérer les utilisateurs associés à la même entreprise que l'administrateur
        $users = User::where('company_id', $admin->company_id)->get();

        // Compter les tâches effectuées par chaque utilisateur
        $totalTasksByAdmin = 0;
        foreach ($users as $user) {
            $totalTasksByAdmin += $user->tasks()->count();
        }

        return response()->json(['total_tasks_by_admin' => $totalTasksByAdmin]);
    }
}

