<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $project = Project::select('projects.id as projects_id','projects.name','projects.description','projects.deadline', 'projects.created_at','users.name as user_name')
                    ->leftJoin('users','users.id','projects.created_by')
                    ->get();
        if ($project->count() > 0) {
            return response()->json([
                'message' => 'Projects fetch successfully...',
                'data' => $project
            ], 200);
        } else {
            return response()->json([
                'message' => 'No record avaliable'
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        if ($request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required',
                'deadline' => 'required|after_or_equal:now'
            ]);
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'deadline' => Carbon::parse($request->deadline) ,
                'created_by' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Projects create successfully...',
                'success' => true,
                'data' => $project
            ], 200);
        } else {
            return response()->json([
                'message' => 'Can not create project',
                'success' => false
            ], 401);
        }
    }

    public function show($id): JsonResponse
    {
        $project = Project::select('projects.id', 'projects.name', 'projects.description', 'projects.deadline', 'projects.created_at', 'users.name as user_name')
            ->leftJoin('users', 'users.id', 'projects.created_by')
            ->where('projects.id', $id)->first();
        if ($project) {
            return response()->json([
                'message' => 'Projects fetch successfully...',
                'success' => true,
                'data' => $project
            ], 200);
        } else {
            return response()->json([
                'message' => 'Project not found',
                'success' => false
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required',
            'deadline' => 'required|date'
        ]);
        $project = Project::where('id', $id)->first();
        if ($project && $project->created_by == $request->user()->id) {
            $project->update([
                'name' => $request->name,
                'description' => $request->description,
                'deadline' => Carbon::parse($request->deadline),
                'created_by' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Projects updated successfully...',
                'success' => true,
                'data' => $project
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not allowed',
                'success' => false
            ], 401);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $project = Project::where('id', $id)->first();
        if ($project) {
            $project->delete();
            return response()->json([
                'message' => 'Projects deleted successfully...',
                'success' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'You are not allowed',
                'success' => false
            ], 401);
        }
    }
}
