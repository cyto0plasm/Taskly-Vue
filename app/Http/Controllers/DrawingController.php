<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class DrawingController extends Controller
{
    public function upsert(Request $request)
{
    $request->validate([
        'type' => 'required|in:task,project',
        'id'   => 'required|integer',
        'data' => 'required'
    ]);

    $modelClass = $request->type === 'task'
        ? Task::class
        : Project::class;

    $model = $modelClass::findOrFail($request->id);

    $drawing = $model->drawing()->updateOrCreate(
        [],
        [
            'data' => $request->data,
            'version' => '1'
        ]
    );

    return response()->json([
        'success' => true,
        'data' => $drawing
    ]);
}
public function show(Request $request)
{
    $request->validate([
        'type' => 'required|in:task,project',
        'id'   => 'required|integer',
        ]);

        $modelClass = $request->type === 'task'
        ? Task::class
        : Project::class;

        $model = $modelClass::findOrFail($request->id);
        // dd($model->drawing);

  return response()->json([
    'success' => true,
    'data' => optional($model->drawing)->data
]);
}
}
