<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
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
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
    
        $userId = $validatedData['user_id'];
    
        $tasks = Task::where('user_id', $userId)->get();
    
        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Aucune tâche trouvée pour l\'utilisateur spécifié.'], 404);
        }
    
        return response()->json(['userId' => $userId, 'tasks' => $tasks], 200);
    }
    public function getUsersByAdminCompanyId(Request $request)
    {
        // Extract and validate the token
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Check if the user is an admin
        $type = $payload['type'];
        $id = $payload['sub'];

        if ($type !== 'admin') {
            return response()->json(['error' => 'Only admins can access this resource.'], 403);
        }

        // Retrieve the authenticated admin
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        // Check if the admin is associated with a company
        if (!$admin->company_id) {
            return response()->json(['error' => 'Admin must be associated with a company.'], 403);
        }

        // Retrieve users from the same company
        $users = User::where('company_id', $admin->company_id)->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found for this company'], 404);
        }

        return response()->json(['users' => $users], 200);
    }
    
    public function getUserTasks(Request $request)
    {
        try {
            // Extraction du token JWT de la requête
            $token = JWTAuth::parseToken();
            
            // Récupération de l'utilisateur à partir du token JWT
            $user = $token->authenticate();
            
            // Récupérer les tâches associées à l'utilisateur
            $tasks = Task::where('user_id', $user->id)->get();
            
            if ($tasks->isEmpty()) {
                return response()->json(['message' => 'No tasks found for the specified user.'], 404);
            }
            
            // Retourner les tâches
            return response()->json(['tasks' => $tasks], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }}