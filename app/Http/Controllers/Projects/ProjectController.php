<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;

class ProjectController extends Controller
{
    protected ProjectService $projectService;
    public function __construct(ProjectService $projectService) {
        $this->projectService = $projectService;
    }


    public function index(Request $request)
    {
        $userId = Auth::id();
        $showAll = $request->has('showAll');

        // Tasks query
        $tasksQuery = $this->projectService->visibleProjectQuery($userId)
            ->orderBy('position');

        // Paginated or all
        $tasks = $showAll
            ? $tasksQuery->get()
            : $tasksQuery->paginate(5);

        // Projects via ProjectService
        $projects = $this->projectService->visibleProjectQuery($userId)->get();

        // Task status counts
        $statusCounts = $this->projectService->statusCounts($userId);

        return view('Project.projectsIndex', [
            'projects' => $projects,
            'projectStatusDoneCount' => $statusCounts['done'],
            'projectStatusProgressCount' => $statusCounts['in_progress'],
            'projectStatusPendingCount' => $statusCounts['pending'],
            'showAll' => $showAll,
        ]);
    }




}
