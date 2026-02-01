<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'title'=> $this->title, 
            'description'=> $this->description,
            'due_date'=> $this->due_date,
            'priority'=> $this->priority,
            'status'=> $this->status,
            'type'=> $this->type,
            'project_id'=> $this->project_id,
            'creator_id'=> $this->creator_id,
            'position'=> $this->position,
      
        // 'project'=>[
        //     'id'=>$this->project->id,
        //     'name'=>$this->project->name,
        // ]
        ];
        
    }
}