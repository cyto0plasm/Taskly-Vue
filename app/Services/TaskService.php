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
   public function applyFilters(Builder $query, array $filters = []): Builder
{
    return $query
        ->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        })

        // has project / no project
        ->when(isset($filters['has_project']), function ($q) use ($filters) {
            if ($filters['has_project']) {
                $q->whereNotNull('project_id');
            } else {
                $q->whereNull('project_id');
            }
        })

        // specific project
        ->when($filters['project_id'] ?? null, function ($q, $projectId) {
            $q->where('project_id', $projectId);
        })

        // due date filters
        ->when($filters['due'] ?? null, function ($q, $due) {
            match ($due) {
                'today' => $q->whereDate('due_date', now()),
                'overdue' => $q->whereDate('due_date', '<', now()),
                'upcoming' => $q->whereDate('due_date', '>', now()),
                default => null
            };
        })

        // due date range
        ->when($filters['from'] ?? null, function ($q, $from) {
            $q->whereDate('due_date', '>=', $from);
        })
        ->when($filters['to'] ?? null, function ($q, $to) {
            $q->whereDate('due_date', '<=', $to);
        })

        // search
        ->when($filters['search'] ?? null, function ($q, $search) {
            $q->where('title', 'like', "%{$search}%");
        });
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
