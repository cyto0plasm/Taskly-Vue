<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ProjectControllerApiVue extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService ) {
        $this->projectService = $projectService;
    }
   /**
     * Fetch all projects for current user
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            // Validate filters
            $validated = $request->validate([
                'status'  => 'nullable|in:pending,in_progress,done',
                'search'  => 'nullable|string|max:255',
                'from'    => 'nullable|date',
                'to'      => 'nullable|date',
                'perPage' => 'nullable|integer|min:1|max:100',
                'page'    => 'nullable|integer|min:1',
            ]);

            $perPage = (int) ($validated['perPage'] ?? 20);
            $page    = (int) ($validated['page'] ?? 1);

            // Base query (permissions handled in service)
            $query = $this->projectService
                ->visibleProjectQuery($userId)
                ->orderBy('position');

            // Apply filters
            $query = $this->projectService->applyFilters($query, $validated);

            // Paginate
            $paginator = $query->paginate(perPage: $perPage, page: $page);
            $pageItems = collect($paginator->items());

            return response()->json([
                'success' => true,
                'data'    => $pageItems,
                'meta'    => [
                    'page'     => $paginator->currentPage(),
                    'perPage'  => $paginator->perPage(),
                    'lastPage' => $paginator->lastPage(),
                    'total'    => $paginator->total(),
                    'hasMore'  => $paginator->hasMorePages(),

                    // counts for current page
                    'statusCounts' => [
                        'done'        => $pageItems->where('status', 'done')->count(),
                        'in_progress' => $pageItems->where('status', 'in_progress')->count(),
                        'pending'     => $pageItems->where('status', 'pending')->count(),
                    ],

                    // counts for all visible projects
                    'allStatusCounts' => $this->projectService->statusCounts($userId),
                ],
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Fetch single project
     */
    public function show(int $id)
    {
        try {
            $project = $this->projectService->findVisibleProject($id, Auth::id());

            return response()->json([
                'success' => true,
                'data'    => $project,
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Create project
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|min:3|max:255',
                'description' => 'nullable|string',
                'start_date'  => 'nullable|date',
                'end_date'    => 'nullable|date',
                'status'      => 'required|in:pending,in_progress,done',
                'position'    => 'nullable|integer',
            ]);

            $project = $this->projectService->createProject($validated, Auth::id());

            return response()->json([
                'success' => true,
                'data'    => $project,
                'message' => 'Project created successfully',
            ], 201);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Update project
     */
    public function update(Request $request, int $id)
    {
        try {
            $project = $this->projectService->findVisibleProject($id, Auth::id());

            $validated = $request->validate([
                'name'        => 'required|string|min:3|max:255',
                'description' => 'nullable|string',
                'start_date'  => 'nullable|date',
                'end_date'    => 'nullable|date',
                'status'      => 'required|in:pending,in_progress,done',
                'position'    => 'nullable|integer',
            ]);

            $project->update($validated);

            return response()->json([
                'success' => true,
                'data'    => $project,
                'message' => 'Project updated successfully',
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Delete project
     */
    public function destroy(int $id)
    {
        try {
            $project = $this->projectService->findVisibleProject($id, Auth::id());
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully',
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Update project status
     */
    public function updateStatus(Request $request, int $id)
    {
        try {
            $project = $this->projectService->findVisibleProject($id, Auth::id());

            $request->validate([
                'status' => 'required|in:pending,in_progress,done',
            ]);

            $project->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'data'    => $project,
                'message' => 'Status updated successfully',
            ]);

        } catch (Throwable $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Reorder projects
     */
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'order'       => 'required|array',
                'order.*.id'  => 'required|integer|exists:projects,id',
                'order.*.position' => 'required|integer',
            ]);

            foreach ($request->order as $item) {
                Project::where('id', $item['id'])->update(['position' => $item['position']]);
            }

            return response()->json(['success' => true]);

        } catch (Throwable $e) {
            return $this->apiError($e);
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
