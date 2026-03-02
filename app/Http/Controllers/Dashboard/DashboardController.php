<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected ProjectService $projectService;
    protected TaskService $taskService;
 public function __construct(ProjectService $projectService,TaskService $taskService  ) {
    $this->projectService = $projectService;
    $this->taskService = $taskService;
}

    public function guestView()
{
 return view('dashboard-guest');
}

    public function index()
    {
        return view('dashboard-auth');
    }
 public function authView()
{
    $userId = Auth::id();

    $projectsCount = $this->projectService->visibleProjectQuery($userId)->count();
     $tasksCount = $this->taskService
            ->visibleTaskQuery($userId)
            ->count();

        // Task status counts (done / pending / in_progress)
        $tasksStatusCounts = $this->taskService->statusCounts($userId);

        return view(
            'dashboard-auth',
            compact('projectsCount', 'tasksCount', 'tasksStatusCounts')
        );
}

    }
