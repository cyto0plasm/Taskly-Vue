<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationController extends Controller
{
      public function validate(Request $request, string $entity)
    {
        $rules = match ($entity) {
            'task' => [
                'title'       => 'required|string|max:255|min:3',
                'description' => 'nullable|string',
                'due_date'    => 'nullable|date',
                'priority'    => 'required|in:low,medium,high',
                'status'      => 'required|in:pending,in_progress,done',
                'project_id'  => 'nullable|exists:projects,id',
            ],

            'project' => [
                'name' => 'required|string|min:3',
                'description'=>'nullable|string',
                'start_date'=>'nullable|date',
                'end_date'=>'nullable|date',
                'status'=>'required|in:pending,in_progress,done',
                'creator_id'=>'exists:users,id',
            ],

            default => abort(404),
        };

        // 🔥 IMPORTANT: validate ONLY sent fields
        $validator = Validator::make(
            $request->all(),
            array_intersect_key($rules, $request->all())
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json(['valid' => true]);
    }
}