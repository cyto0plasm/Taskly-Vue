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
public function index():View {
     $userId = Auth::id();
    $userProjects=$this->projectService->getUserProjects($userId)->simplePaginate(15);
    return view('project.projectsIndex',['projects'=>$userProjects]);
}
public function create(Request $request) {
  
}

    //store project
 public function store(Request $request)
{
    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date'  => 'nullable|date',
        'end_date'    => 'nullable|date',
        'status'      => 'required|in:pending,in_progress,done',
    ]);

    $project=$this->projectService->StoreUserProject($validated);
    
    if($project){
        return response()->json([
            'successs'=>true,
            'message'=>'Project created successfully',
            'project'=>$project
        ]);
    }

    return response()->json([
        'success'=>false,
        'message'=>'Failed to create project'
    ],500);
}

    //show project
    public function show(Request $request) {
        
    }

    //update project
    public function update($id):View {
        return view('project.projectEdit');
    }
    //edit project
    public function edit(Request $request, $id) {
       
    }
    //delete project
    public function delete($id) {}

    public function view() {
        return view('project.projectView');
    }
}