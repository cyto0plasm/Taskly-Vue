<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\Request;

class DashboardControllerApiVue extends Controller
{
    public function __construct(
        protected ProjectService $projectService,
        protected TaskService    $taskService,
    ) {}

    /*
    |==========================================================================
    | authView  —  fast aggregation stats (used by summary banner + cards)
    |==========================================================================
    */
    public function authView(Request $request)
    {
        $userId   = $request->user()->id;
        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $weekEnd  = now()->addDays(7)->toDateString();

        // ── Projects (1 query) ────────────────────────────────────────────
        $projectStats = $this->projectService
            ->visibleProjectQuery($userId)
            ->selectRaw("
                COUNT(*)                                                AS total,
                SUM(status = 'done')                                    AS completed,
                SUM(status = 'pending')                                 AS pending,
                SUM(status = 'in_progress')                             AS in_progress,
                SUM(status != 'done' AND end_date IS NOT NULL
                    AND end_date < ?)                                   AS overdue,
                SUM(status != 'done' AND end_date IS NOT NULL
                    AND end_date = ?)                                   AS due_today,
                SUM(status != 'done' AND end_date IS NOT NULL
                    AND end_date = ?)                                   AS due_tomorrow,
                SUM(status != 'done' AND end_date IS NOT NULL
                    AND end_date BETWEEN ? AND ?)                       AS upcoming_week
            ", [$today, $today, $tomorrow, $today, $weekEnd])
            ->first();

        // ── Tasks (1 query) ───────────────────────────────────────────────
        $taskStats = $this->taskService
            ->visibleTaskQuery($userId)
            ->selectRaw("
                COUNT(*)                                                AS total,
                SUM(status = 'done')                                    AS completed,
                SUM(status = 'pending')                                 AS pending,
                SUM(status = 'in_progress')                             AS in_progress,
                SUM(status != 'done' AND due_date IS NOT NULL
                    AND due_date < ?)                                   AS overdue,
                SUM(status != 'done' AND due_date IS NOT NULL
                    AND due_date = ?)                                   AS due_today,
                SUM(status != 'done' AND due_date IS NOT NULL
                    AND due_date = ?)                                   AS due_tomorrow,
                SUM(status != 'done' AND due_date IS NOT NULL
                    AND due_date BETWEEN ? AND ?)                       AS upcoming_week
            ", [$today, $today, $tomorrow, $today, $weekEnd])
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'projectsCount'            => (int) ($projectStats->total         ?? 0),
                'completedProjects'        => (int) ($projectStats->completed     ?? 0),
                'pendingProjects'          => (int) ($projectStats->pending       ?? 0),
                'projectsProgress'         => (int) ($projectStats->in_progress   ?? 0),
                'upcomingProjectDeadlines' => (int) ($projectStats->upcoming_week ?? 0),

                'tasksCount'               => (int) ($taskStats->total            ?? 0),
                'completedTasks'           => (int) ($taskStats->completed        ?? 0),
                'pendingTasks'             => (int) ($taskStats->pending          ?? 0),
                'tasksProgress'            => (int) ($taskStats->in_progress      ?? 0),
                'upcomingTaskDeadlines'    => (int) ($taskStats->upcoming_week    ?? 0),

                'overdueCount'             => (int) ($projectStats->overdue       ?? 0)
                                           + (int) ($taskStats->overdue           ?? 0),
                'dueTodayCount'            => (int) ($projectStats->due_today     ?? 0)
                                           + (int) ($taskStats->due_today         ?? 0),
                'dueTomorrowCount'         => (int) ($projectStats->due_tomorrow  ?? 0)
                                           + (int) ($taskStats->due_tomorrow      ?? 0),
            ],
        ]);
    }

    /*
    |==========================================================================
    | widgetsView  —  list data for the 3 dashboard widgets
    |
    |  Loaded separately (after stats) so the page isn't blocked by heavier
    |  queries. Called once; cached in the store the same way as stats.
    |==========================================================================
    */
    public function widgetsView(Request $request)
    {
        $userId = $request->user()->id;
        $today  = now()->toDateString();

        // ── 1. RECENT ACTIVITY ────────────────────────────────────────────
        //  Last 5 touched items across projects + tasks, merged & sorted in PHP
        //  (UNION inside DB would work too but this keeps service encapsulation)

        $recentProjects = $this->projectService
            ->visibleProjectQuery($userId)
            ->select('id', 'name', 'status', 'updated_at')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'type'       => 'project',
                'id'         => $p->id,
                'name'       => $p->name,
                'status'     => $p->status,
                'updated_at' => $p->updated_at,
            ]);

        $recentTasks = $this->taskService
            ->visibleTaskQuery($userId)
            ->select('id', 'title', 'status', 'updated_at')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($t) => [
                'type'       => 'task',
                'id'         => $t->id,
                'name'       => $t->name,
                'status'     => $t->status,
                'updated_at' => $t->updated_at,
            ]);

        $recentActivity = $recentProjects
            ->concat($recentTasks)
            ->sortByDesc('updated_at')
            ->take(5)
            ->values();

        // ── 2. PROJECTS NEAR COMPLETION (≥ 80 %) ─────────────────────────
        //  We need task counts per project — one query with a subquery each,
        //  or a JOIN. Using selectRaw with correlated subqueries is clean
        //  and avoids loading all tasks into PHP memory.

        $nearCompletion = $this->projectService
            ->visibleProjectQuery($userId)
            ->where('status', '!=', 'done')
            ->whereRaw("(
                SELECT COUNT(*) FROM tasks
                WHERE tasks.project_id = projects.id
            ) > 0")
            ->selectRaw("
                id,
                name,
                status,
                end_date,
                (
                    SELECT COUNT(*) FROM tasks
                    WHERE tasks.project_id = projects.id
                )                                               AS total_tasks,
                (
                    SELECT COUNT(*) FROM tasks
                    WHERE tasks.project_id = projects.id
                      AND tasks.status = 'done'
                )                                               AS done_tasks
            ")
            ->havingRaw("done_tasks / total_tasks >= 0.80")
            ->orderByRaw("done_tasks / total_tasks DESC")
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'status'          => $p->status,
                'end_date'        => $p->end_date,
                'total_tasks'     => (int) $p->total_tasks,
                'done_tasks'      => (int) $p->done_tasks,
                'tasks_left'      => (int) $p->total_tasks - (int) $p->done_tasks,
                'completion_pct'  => $p->total_tasks > 0
                    ? round(($p->done_tasks / $p->total_tasks) * 100)
                    : 0,
            ]);

        // ── 3. OVERDUE ITEMS (projects + tasks, newest overdue first) ──────
        $overdueProjects = $this->projectService
            ->visibleProjectQuery($userId)
            ->where('status', '!=', 'done')
            ->whereNotNull('end_date')
            ->where('end_date', '<', $today)
            ->select('id', 'name', 'status', 'end_date')
            ->orderBy('end_date')          // oldest overdue at top
            ->get()
            ->map(fn ($p) => [
                'type'      => 'project',
                'id'        => $p->id,
                'name'      => $p->name,
                'status'    => $p->status,
                'due_date'  => $p->end_date,
                'days_late' => now()->diffInDays($p->end_date),
            ]);

        $overdueTasks = $this->taskService
            ->visibleTaskQuery($userId)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->select('id', 'title', 'status', 'due_date')
            ->orderBy('due_date')
            ->get()
            ->map(fn ($t) => [
                'type'      => 'task',
                'id'        => $t->id,
                'title'      => $t->title,
                'status'    => $t->status,
                'due_date'  => $t->due_date,
                'days_late' => now()->diffInDays($t->due_date),
            ]);

        // Merge + sort by most days late first
        $overdueItems = $overdueProjects
            ->concat($overdueTasks)
            ->sortByDesc('days_late')
            ->values();

        // ─────────────────────────────────────────────────────────────────
        return response()->json([
            'success' => true,
            'data'    => [
                'recentActivity' => $recentActivity,
                'nearCompletion' => $nearCompletion,
                'overdueItems'   => $overdueItems,
            ],
        ]);
    }
}
