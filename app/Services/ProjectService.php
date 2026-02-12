<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class ProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    /**
     * Base query: projects the user is allowed to see
     */
    public function visibleProjectQuery(int $userId): Builder
    {
        return Project::query()
            ->with('tasks', 'collaborators') // eager load tasks + collaborators
            ->where(function ($q) use ($userId) {

                // user owns the project
                $q->where('creator_id', $userId)

                    // user collaborates on the project
                    ->orWhereHas('collaborators', function ($c) use ($userId) {
                        $c->where('user_id', $userId);
                    });
            });
    }
    /**
     * fetch single project with tasks
     */
    public function findVisibleProject(int $projectId, int $userId): Project
    {
        $project = Project::with('tasks', 'collaborators')->findOrFail($projectId);

        if (!$this->canViewProject($project, $userId)) {
            throw new \Exception('You do not have permission to view this project.');
        }

        return $project;
    }
    /**
     * Status counts for all visible projects
     */
    public function statusCounts(int $userId): array
    {
        $counts = $this->visibleProjectQuery($userId)
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
     * create project
     */
    public function createProject(array $data, int $userId): Project
    {
        $data['creator_id'] = $userId;
        $data['position'] = $data['position'] ?? 0;

        return Project::create($data);
    }
    /**
     * Apply filters to project query
     */
    public function applyFilters(Builder $query, array $filters = []): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, fn($q, $search) => $q->where('name', 'like', "%$search%"))
            ->when($filters['from'] ?? null, fn($q, $from) => $q->whereDate('start_date', '>=', $from))
            ->when($filters['to'] ?? null, fn($q, $to) => $q->whereDate('end_date', '<=', $to));
    }




    /* ---------- Permissions ---------- */
    public function assertProjectAccess(int $projectId, int $userId): void
    {
        $project = Project::findOrFail($projectId);

        $allowed = $project->creator_id === $userId || $project->collaborators()->where('user_id', $userId)->exists();

        if (!$allowed) {
            throw new \Exception('You do not have permission to access this project.');
        }
    }

    public function canViewProject(Project $project, int $userId): bool
    {
        return $project->creator_id === $userId || $project->collaborators()->where('user_id', $userId)->exists();
    }
}
