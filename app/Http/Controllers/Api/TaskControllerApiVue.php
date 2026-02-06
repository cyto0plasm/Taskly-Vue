<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class TaskControllerApiVue extends Controller
{
    protected TaskService $taskService;
    public function __construct(TaskService $taskService)
    {
    $this->taskService= $taskService;
    }
    /**
     * Fetch all tasks for current user
     */
    public function index(Request $request)
    {
         try {
        $userId  = Auth::id();
        $perPage = (int) $request->query('perPage', 20);
        $page    = (int) $request->query('page', 1);

        //base query (gets all tasks user owns or collaberates on ) and
        $query = $this->taskService
            ->visibleTaskQuery($userId)
            ->orderBy('position');

        //  Filters (optional, still service-owned)
        $query = $this->taskService->applyFilters(
            $query,
            $request->only(['status', 'project_id', 'search'])
        );

        //  Paginate
        $paginator = $query->paginate(
            perPage: $perPage,
            page: $page
        );

        return response()->json([
    'success' => true,
    'data' => collect($paginator->items()),
    'meta' => [
        'page'      => $paginator->currentPage(),
        'perPage'   => $paginator->perPage(),
        'lastPage'  => $paginator->lastPage(),
        'total'     => $paginator->total(),
        'hasMore'   => $paginator->hasMorePages(),

        // counts for **current page**
        'statusCounts' => [
            'done' => collect($paginator->items())->where('status', 'done')->count(),
            'in_progress' => collect($paginator->items())->where('status', 'in_progress')->count(),
            'pending' => collect($paginator->items())->where('status', 'pending')->count(),
        ],

        // counts for **all tasks user can see**
        'allStatusCounts' => $this->taskService->statusCounts($userId),
    ],
]);



        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Fetch single task
     */
    public function show(int $id)
    {
        try {
            $task = Task::with('project')
                ->where('id', $id)
                ->where(function ($q) {
                    $q->where('creator_id', Auth::id())
                      ->orWhereHas('project.collaborators', fn($c) => $c->where('user_id', Auth::id()));
                })
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data'    => $task,
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Create task
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'       => 'required|string|min:3|max:255',
                'description' => 'nullable|string',
                'due_date'    => 'nullable|date',
                'priority'    => 'required|in:low,medium,high',
                'status'      => 'required|in:pending,in_progress,done',
                'project_id'  => 'nullable|exists:projects,id',
            ]);

            $task = Task::create([
                ...$validated,
                'creator_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $task,
                'message' => 'Task created successfully',
            ], 201);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Update task
     */
    public function update(Request $request, int $id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->authorizeTask($task);

            $validated = $request->validate([
                'title'       => 'required|string|max:255|min:3',
                'description' => 'nullable|string',
                'due_date'    => 'nullable|date',
                'priority'    => 'required|in:low,medium,high',
                'status'      => 'required|in:pending,in_progress,done',
                'project_id'  => 'nullable|exists:projects,id',
            ]);

            $task->update($validated);

            return response()->json([
                'success' => true,
                'data'    => $task,
                'message' => 'Task updated successfully',
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Delete task
     */
    public function destroy(int $id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->authorizeTask($task);
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, int $id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->authorizeTask($task);

            $request->validate([
                'status' => 'required|in:pending,in_progress,done',
            ]);

            $task->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'data'    => $task,
                'message' => 'Status updated successfully',
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Reorder tasks
     */
    public function reorder(Request $request)
{
    try{
    $request->validate([
        'order' => 'required|array',
        'order.*.id' => 'required|integer|exists:tasks,id',
        'order.*.position' => 'required|integer',
    ]);

    foreach ($request->order as $item) {
        Task::where('id', $item['id'])->update(['position' => $item['position']]);
    }

    return response()->json(['success' => true]);}
    catch (Throwable $e) {
            return $this->apiError($e);
        }
}


    /**
     * Authorization helper
     */
    protected function authorizeTask(Task $task)
    {
        $userId = Auth::id();

        if ($task->project) {
            if ($task->project->creator_id !== $userId &&
                !$task->project->collaborators()->where('user_id', $userId)->exists()) {
                abort(403, 'Unauthorized');
            }
        } elseif ($task->creator_id !== $userId) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Standard API error handling
     */
    protected function apiError(Throwable $e, int $status = 400)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: 'Something went wrong',
        ], $status);
    }
}
