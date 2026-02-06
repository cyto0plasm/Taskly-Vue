<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Pest\Support\Arr;

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
     * Base query: tasks the user is allowed to see
     */
    public function visibleTaskQuery(int $userId): Builder
    {
        return Task::query()
            ->with('project') // eager-load project relation
            ->where(function ($q) use ($userId) {

                // CONDITION 1: user created the task
                $q->where('creator_id', $userId)

                    // OR CONDITION 2:
                    ->orWhereHas('project.collaborators', function ($c) use ($userId) {

                        // user is a collaborator on the task's project
                        $c->where('user_id', $userId);
                    });
            });
    }


    /**
     * Apply filters (status, project, search)
     */
    public function applyFilters(
        Builder $query,
        array $filters = []
    ): Builder {
        //filter on status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        //Filters tasks that belong to a specific project.
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        //Searches the title column for a substring.
        if (!empty($filters["search"])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
        return $query;
    }
    /**
     * Status counts for all visible tasks
     */
    public function statusCounts(int $userId): array
    {
        $counts = $this->visibleTaskQuery($userId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

       return [
    'done'        => $counts['done'] ?? 0,
    'in_progress' => $counts['in_progress'] ?? 0,
    'pending'     => $counts['pending'] ?? 0,
];
    }

    /**
     * Create task with permission check
     */
    public function createTask(array $data, int $userId): Task
    {
        if (!empty($data['project_id'])) {
            $this->assertProjectAccess($data['project_id'], $userId);
            }
            return Task::create([
                ...$data,
                'creator_id' => $userId
            ]);
    }
    /**
     * Fetch single task with permission check
     */
    public function findVisibleTask(int $taskId, int $userId ):Task{
        $task = Task::with('project')->findOrFail($taskId);
        if(!$this->canViewTask($task, $userId)){
            throw new \Exception('You do not have permission to view this task.');
        }
        return $task;
    }

    /* ---------- Permissions ---------- */
    public function assertProjectAccess(int $projectId,int $userId):void{
        $project= Project::findOrFail($projectId);
        //-----------the user owns the project -- || user is a collaborator
        $allowed= $project->creator_id ===$userId || $project->collaborators()->where('user_id', $userId)->exists();
        // Unauthorized users are immediately blocked
        if(!$allowed){
            throw new \Exception('You do not have permission to access this project.');
        }

    }

    public function canViewTask(Task $task, int $userId):bool{
        if($task->project){
            return
            $task->project->creator_id === $userId ||
            $task->project->collaborators()->where('user_id',$userId)->exists();
        }
        return $task->creator_id === $userId;
    }

    //End Of File
}
