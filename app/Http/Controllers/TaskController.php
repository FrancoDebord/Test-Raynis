<?php
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Routing\Controller;


class TaskController extends Controller
{
    // Récupérer toutes les tâches
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }

    // Créer une nouvelle tâche
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|boolean',
            'fichier' => 'nullable|file'
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
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
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



?>