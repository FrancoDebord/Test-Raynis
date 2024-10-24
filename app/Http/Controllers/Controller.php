<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Task;
use Illuminate\Routing\Controller;





class AuthController extends controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nom' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(compact('token'));
    }


    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }

    // Créer une nouvelle tâche
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre_tache' => 'required|string|max:255',
            'description_tache' => 'required|string',
            'date_creation' => 'required|date',
            'status' => 'required|boolean',
            'file' => 'nullable|file'
        ]);

        $task = new Task($validated);

        // Gestion du fichier uploadé
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $task->file_path = $filePath;
        }

        $task->save();
        return response()->json($task, 201);
    }

    // Récupérer une tâche spécifique
    public function show(Task $task)
    {
        return response()->json($task, 200);
    }

    // Mettre à jour une tâche
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'status' => 'required|boolean',
            'file' => 'nullable|file'
        ]);

        $task->update($validated);

        // Si un nouveau fichier est uploadé
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads', 'public');
            $task->file_path = $filePath;
        }

        return response()->json($task, 200);
    }

    // Supprimer une tâche
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    // Télécharger le fichier d'une tâche
    public function downloadFile(Task $task)
    {
        if ($task->file_path) {
            return response()->download(storage_path('app/public/' . $task->file_path));
        }
        return response()->json(['error' => 'Fichier non trouvé'], 404);
    }
}
