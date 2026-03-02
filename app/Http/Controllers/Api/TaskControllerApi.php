<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskControllerApi extends Controller
{
     protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
 public function index(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $userId = $user->id;

    // Fetch tasks as a paginated collection
    $tasks = $this->taskService->getUserTasksApi($userId)->get();

    // Fetch status counts
    $statusCounts = $this->taskService->getTaskStatusCounts($userId);

    // Return Resource Collection with meta
    return TaskResource::collection($tasks)
        ->additional([
            'meta' => [
                'status_counts' => [
                    'done' => $statusCounts['done'] ?? 0,
                    'in_progress' => $statusCounts['in_progress'] ?? 0,
                    'pending' => $statusCounts['pending'] ?? 0,
                ]
            ]
        ]);
}

    public function index2()
    {

    return response()->json(['ok' => true]);
    }




    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date'    => 'nullable|date',
        'priority'    => 'required|in:low,medium,high',
        'status'      => 'required|in:pending,in_progress,done',
        'project_id'  => 'nullable|exists:projects,id',
    ]);

    try {
        $task = Task::create(array_merge($validated, ['creator_id' => Auth::id()]));

        // Render task List Blade component as HTML
        $taskHtml = view('components.task-button', [
            'state'  => $task->status,
            'title'  => $task->title,
            'taskId' => $task->id,
            'url'    => route('tasks.show', $task->id),
        ])->render();

        return response()->json([
            'success'  => true,
            'message'  => 'Task created successfully',
            'taskHtml' => $task,
        ],201);

    } catch (\Exception $e) {
        Log::error('Task creation failed', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create task',
        ], 500);
    }
}




    /**
     * Display the specified resource.
     */
   public function show(int $id, TaskService $taskService)
{
    try {
        $task = $taskService->getTaskById($id, Auth::id());

        return response()->json($task);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task=Task::findOrFail($id);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,in_progress,done',
            'project_id'  => 'nullable|exists:projects,id',
        ]);
        $task=Task::findOrFail($id);
        $task->update($validated);
        return response()->json([
            'success'=>true,
            'message'=>'Task updated successfully',
            'task'=>$task,
        ]);

    }

  public function updateStatus($id, Request $request)
{
    $task = Task::find($id);
    $projects = Project::all();

    if (!$task) {
        return response()->json([
            'success' => false,
            'message' => 'Task not found.'
        ]);
    }

    if ($task->status === 'done') {
        return response()->json([
            'success' => false,
            'message' => 'This task is already marked as done.'
        ]);
    }

    // Update and save
    $task->status = $request->input('status', 'done');
    $task->save();


    return response()->json([
        'success' => true,
        'message' => 'Task status updated successfully.',
        'task'    => $task,
    ]);
}



    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $deleted = Task::destroy($id); // returns number of deleted rows

        if ($deleted) {
        return response()->json(['success' => true, 'message' => 'Task deleted']);
    } else {
        return response()->json(['success' => false, 'message' => 'Task not found'], 404);
    }
    }

    public function reorder(Request $request)
{
    foreach ($request->order as $item) {
        \App\Models\Task::where('id', $item['id'])
            ->update(['position' => $item['position']]);
    }

    return response()->json(['status' => 'success']);
}

}
