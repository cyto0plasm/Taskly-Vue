<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    protected TaskService $taskService;
    protected ProjectService $projectService;

    public function __construct(TaskService $taskService,ProjectService $projectService)
    {
        $this->taskService = $taskService;
        $this->projectService = $projectService;
    }
    /**
     * Display a listing of the resource.
     */
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $showAll = $request->has('showAll');

        // Tasks query
        $tasksQuery = $this->taskService->visibleTaskQuery($userId)
            ->orderBy('position');

        // Paginated or all
        $tasks = $showAll
            ? $tasksQuery->get()
            : $tasksQuery->paginate(5);

        // Projects via ProjectService
        $projects = $this->projectService->getUserProjects($userId)->get();

        // Task status counts
        $statusCounts = $this->taskService->statusCounts($userId);

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


    public function CalenderView(){

    }
}
