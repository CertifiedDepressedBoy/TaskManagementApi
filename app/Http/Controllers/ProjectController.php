<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index():JsonResponse
    {
        $project = Project::get();
        if($project->count() > 0)
        {
            return response()->json([
                'message' => 'Projects fetch successfully...',
                'data' => $project
            ],200);
        }
        else
        {
            return response()->json([
                'message' => 'No record avaliable'
            ],404);
        }
    }

    public function store(Request $request):JsonResponse
    {
        if($request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required',
                'deadline' => 'required|date'
            ]);
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'created_by' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Projects create successfully...',
                'data' => $request->all()
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Can not create project'
            ], 401);
        }
    }

    public function show($id) : JsonResponse
    {
        $project = Project::where('id',$id)->first();
        if($project)
        {
            return response()->json([
                'message' => 'Projects fetch successfully...',
                'data' => $project
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }
    }

    public function update(Request $request , $id) : JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'deadline' => 'required|date'
        ]);
        $project = Project::where('id',$id)->first();
        if($project && $project->created_by == $request->user()->id)
        {
            $project->update([
                'name' => $request->name,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'created_by' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Projects updated successfully...',
                'data' => $project
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'You are not allowed'
            ], 401);
        }
    }

    public function destroy(Request $request , $id):JsonResponse
    {
        $project = Project::where('id',$id)->first();
        if($project && $project->created_by == $request->user()->id )
        {
            $project->delete();
            return response()->json([
                'message' => 'Projects deleted successfully...',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'You are not allowed'
            ], 401);
        }
    }
}
