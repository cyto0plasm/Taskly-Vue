<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
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
        $userId = Auth::id();
        // Check if "show_all" is requested
        $showAll = $request->has('showAll');

        $tasksQuery = $this->taskService->getUserTasks($userId);

        // Apply pagination or get all
        $tasks = $showAll
            ? $tasksQuery->orderBy('position')->get()  // ✅ Add explicit orderBy
            : $tasksQuery->orderBy('position')->paginate(5);


        $projects = $this->taskService->getUserProjects($userId);
        // dd($projects);
        $statusCounts = $this->taskService->getTaskStatusCounts($userId);
        // dd($tasks);
        return view('Task.tasksIndex', [
            'tasks' => $tasks,
            'firstTask' => $tasks->first(),
            'projects' => $projects,
            'taskStatusDoneCount' => $statusCounts['done'],
            'taskStatusProgressCount' => $statusCounts['in_progress'],
            'taskStatusPendingCount' => $statusCounts['pending'],
            'showAll' => $showAll,
        ]);
    }


    // Alternative: Use Laravel's built-in API resource pagination
   public function allTasksJson(Request $request)
{
    $userId = Auth::id();
    $showAll = $request->query('showAll', false);

    $query = $this->taskService->getUserTasks($userId)
        ->orderBy('position');

    $statusCounts = $this->taskService->getTaskStatusCounts($userId);

    if ($showAll) {
        $tasks = $query->get();
        return response()->json([
            'data' => $tasks,
            'total' => $tasks->count(),
            'statusCounts' => $statusCounts,
        ]);
    }

    // Paginate and include totals
    $tasks = $query->paginate(20);

    return response()->json([
        'data' => $tasks->items(),       // current page tasks
        'current_page' => $tasks->currentPage(),
        'last_page' => $tasks->lastPage(),
        'total' => $tasks->total(),
        'statusCounts' => $statusCounts, // totals for all tasks
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('Task.tasksCreate');
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
                'taskHtml' => $taskHtml,
                'task'     => $task,
            ]);
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
        $task = Task::findOrFail($id);
        $task=$this->taskService->getTaskById($id, Auth::id());

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,in_progress,done',
            'project_id'  => 'nullable|exists:projects,id',
        ]);

        $task = Task::findOrFail($id);
        $task->update($validated);

        // Render updated task HTML
        $taskHtml = view('components.task-button', [
            'state'  => $task->status,
            'title'  => $task->title,
            'taskId' => $task->id,
            'url'    => route('tasks.show', $task->id),
        ])->render();

        return response()->json([
            'success'  => true,
            'message'  => 'Task updated successfully',
            'taskHtml' => $taskHtml,
            'task'     => $task, // send full task for frontend state handling
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        $task = Task::find($id);

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
        $task->refresh();
        // Render partials
        $taskListHtml = view('Task.partials.task-row', compact('task'))->render();
        $taskDetailHtml = view('components.task-details', [
            'task' => $task,
        ])->render();
        return response()->json([
            'success' => true,
            'message' => 'Task marked as complete.',
            'taskId' => $task->id,
            'taskListHtml' => $taskListHtml,
            'taskDetailHtml' => $taskDetailHtml,
            'task' => $task,
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = Task::destroy($id);

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Task deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }
    }

    public function view(): View
    {

        $tasks = Task::orderBy('position')->get();
        return view('Task.taskView', [
            'tasks' => $tasks,

        ]);
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
