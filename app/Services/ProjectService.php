<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getUserProjects(int $userId)
{
    return Project::where('creator_id', $userId)
        ->orderBy('created_at', 'desc');
}


public function StoreUserProject(array $data){
    try{

        $data["creator_id"]=Auth::id();
        return Project::create($data);

    }catch(\Exception $e){

        Log::error('project creationg failed',['error'=>$e->getMessage()]);
        return null;
        
    }
}






}