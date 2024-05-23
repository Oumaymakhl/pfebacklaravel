<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required',
            'estimated_time' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'time_spent' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $task = Task::create($request->all());

        return response()->json(['task' => $task], 201);
    }

    public function index()
    {
        $tasks = Task::all();
        return response()->json(['tasks' => $tasks], 200);
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json(['task' => $task], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required',
            'estimated_time' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'time_spent' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->update($request->all());
        return response()->json(['task' => $task], 200);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        // Trouver la tâche par son ID
        $task = Task::findOrFail($id);

        // Mettre à jour le statut de la tâche
        $task->update(['status' => $request->input('status')]);

        // Retourner la tâche mise à jour
        return response()->json($task);
    }

    public function calculateTimeSpent($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $currentTime = now();
        $createdAtTime = $task->created_at;
        $timeSpentInSeconds = $currentTime->diffInSeconds($createdAtTime);
        $timeSpentInHours = $timeSpentInSeconds / 3600;
        $task->time_spent = round($timeSpentInHours, 2);
        $task->save();

        return response()->json(['task' => $task], 200);
    }
    public function findTasksByUser(Request $request)
    {
        // Valider les données d'entrée pour s'assurer que 'user_id' est présent
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
    
        // Extraire l'ID utilisateur des données validées
        $userId = $validatedData['user_id'];
    
        // Récupérer les tâches associées à l'utilisateur
        $tasks = Task::where('user_id', $userId)->get();
    
        // Vérifiez si des tâches sont trouvées
        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found for the specified user.'], 404);
        }
    
        // Retourner les tâches
        return response()->json(['tasks' => $tasks], 200);
    }
}
