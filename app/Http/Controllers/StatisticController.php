<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reunion;
use App\Models\User;
use App\Models\Task;
use App\Models\company;

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

}

