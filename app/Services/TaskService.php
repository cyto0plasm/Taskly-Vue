<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    /**
     * Get all tasks accessible to a given user.
     */
   public function getUserTasks(int $userId)
{
    return Task::with('project')
        ->where('creator_id', $userId)
        ->orderBy('position');
}
   public function getUserTasksApi(int $userId)
{
    return Task::where('creator_id', $userId);
}
public function getCollaboratingProjects(int $userId)
{
    return Project::whereHas('collaborators', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })->get();
}


     /**
     * Get all projects the user owns or collaborates on.
     */
    public function getUserProjects(int $userId): Collection
    {
        return Project::where('creator_id', $userId)
            ->orWhereHas('collaborators', fn($q) => $q->where('user_id', $userId))
            ->get();
    }

    /**
     * Get only tasks with projects for this user.
     */
    public function getTasksWithProjects(int $userId): Collection
    {
        return Task::with('project')
            ->whereHas('project', function ($q) use ($userId) {
                $q->where('creator_id', $userId)
                  ->orWhereHas('collaborators', fn($c) => $c->where('user_id', $userId));
            })
            ->get();
    }

    /**
     * Get only tasks that have no project (personal tasks).
     */
    public function getTasksWithoutProjects(int $userId): Collection
    {
        return Task::whereNull('project_id')
            ->where('creator_id', $userId)
            ->get();
    }

    /**
     * Count tasks grouped by status for all user projects.
     */
    public function getTaskStatusCountsForProjects(int $userId): array
    {
        $projectIds = $this->getUserProjects($userId)->pluck('id');

        $counts = Task::selectRaw('status, COUNT(*) as count')
        
            ->whereIn('project_id', $projectIds)
            ->groupBy('status')
            ->pluck('count', 'status');
// dd($counts);
        return [
            'done'        => $counts['done'] ?? 0,
            'in_progress' => $counts['in_progress'] ?? 0,
            'pending'     => $counts['pending'] ?? 0,
        ];
    }
    public function getTaskStatusCounts(int $userId): array
{
    $counts = Task::selectRaw('status, COUNT(*) as count')
        ->where('creator_id', $userId)
        ->groupBy('status')
        ->pluck('count', 'status');

    return [
        'done'        => $counts['done'] ?? 0,
        'in_progress' => $counts['in_progress'] ?? 0,
        'pending'     => $counts['pending'] ?? 0,
    ];
}



    // create task with permissions check 
    public function createTask(array $data, int $userId)
{
    // Validate project ownership or collaboration
    if (!empty($data['project_id'])) {
        $project = Project::find($data['project_id']);

        if (!$project) {
            throw new \Exception('Project not found.');
        }

        // Ensure user is owner or collaborator
        $isAllowed = 
            $project->creator_id === $userId ||
            $project->collaborators()->where('user_id', $userId)->exists();

        if (!$isAllowed) {
            throw new \Exception('You do not have permission to add tasks to this project.');
        }
    }

    $data['creator_id'] = $userId;

    return Task::create($data);
}
// get task by id with permissions check
public function getTaskById(int $taskId, int $userId)
{
    $task = Task::with('project')->find($taskId);

    if (!$task) {
        throw new \Exception('Task not found.');
    }

    // Ensure user has access to this task
    if ($task->project) {
        $project = $task->project;
        $isAllowed = 
            $project->creator_id === $userId ||
            $project->collaborators()->where('user_id', $userId)->exists();
    } else {
        // Tasks with no project are only visible to their creator
        $isAllowed = $task->creator_id === $userId;
    }

    if (!$isAllowed) {
        throw new \Exception('You do not have permission to view this task.');
    }

    // Add a convenience field for display
    $task->project_name = $task->project->name ?? null;

    return $task;
}



}