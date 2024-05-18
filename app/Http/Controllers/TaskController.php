<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données de la requête
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', // S'assure que l'ID de l'utilisateur existe dans la table des utilisateurs
            'status' => 'required',
            'estimated_time' => 'required',
            'name' => 'required',
            'description' => 'required',
            'time_spent' => 'nullable|integer',
            
        ]);

        // Vérifie s'il y a des erreurs de validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Création de la tâche avec les données fournies
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
        // Validation des données de la requête
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required',
            'estimated_time' => 'required',
            'name' => 'required',
            'description' => 'required',
            'time_spent' => 'nullable|integer',
        ]);

        // Vérifie s'il y a des erreurs de validation
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
        $task = Task::findOrFail($id);
        $task->update(['status' => $request->input('status')]);
        return response()->json($task);
    }
    public function calculateTimeSpent($id)
    {
        // Find the task by ID
        $task = Task::find($id);

        // Check if the task exists
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Calculate the time spent
        // Get the current time
        $currentTime = now();

        // Get the created_at time of the task
        $createdAtTime = $task->created_at;

        // Calculate the time difference in seconds
        $timeSpentInSeconds = $currentTime->diffInSeconds($createdAtTime);

        // Convert the time spent from seconds to hours or minutes (as you prefer)
        $timeSpentInHours = $timeSpentInSeconds / 3600; // Convert seconds to hours
        // You can use other units of time (e.g., minutes) if you prefer

        // Update the task's time_spent with the calculated time spent
        $task->time_spent = round($timeSpentInHours, 2); // rounding to 2 decimal places for better readability
        $task->save();

        // Return the updated task with the calculated time_spent
        return response()->json(['task' => $task], 200);
    }
}