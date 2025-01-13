<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\task_assignment;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use HasApiTokens;

    public function index($project_id): JsonResponse
    {

        $task = Task::select('tasks.id', 'tasks.project_id' ,'tasks.title', 'tasks.description', 'tasks.status', 'tasks.priority','tasks.assign_to', 'tasks.due_date','users.name as user_name')
            ->leftJoin('users','users.id','tasks.created_by')
            ->where('tasks.project_id', $project_id)->get();
        if ($task->isEmpty()) {
            return response()->json([
                'message' => 'no task found',
            ], 404);
        } else {
            return response()->json([
                'message' => 'Tasks fetch successfully...',
                'data' => $task
            ], 200);
        }
    }

    public function store($project_id, Request $request): JsonResponse
    {
        $assign = User::where('role', 'Member')->get();
        $project = Project::find($project_id);
        if (!$project) {
            return response()->json([
                'message' => 'Project not found',
                'success' => false
            ], 404);
        } else {
            $request->validate([
                'title' => 'required|string',
                'description' => 'required',
                'due_date' => 'required|date',
            ]);
            $users = User::where('role', 'Member')->get();
            $assignedUser = $users->firstWhere('id', $request->assign_to);
            if($assignedUser){
                $task = Task::create([
                    'project_id' => $project_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'status' => $request->status,
                    'priority' => $request->priority,
                    'due_date' => Carbon::parse($request->due_date),
                    'assign_to' => $request->assign_to
                ]);

                task_assignment::create([
                    'task_id' => $task->id,
                    'user_id' => $request->assign_to,
                    'status' => 'pending'
                ]);

                return response()->json([
                    'message' => 'Task created successfully...',
                    'success' => true,
                    'data' => $task
                ], 200);
             }else{
                return response()->json([
                    'message' => 'Only Assign to Member',
                    'success' => false
                ],400);
             }
        }
    }

    public function show($project_id, $id): JsonResponse
    {
        $project = Project::find($project_id);

        $task = Task::where('tasks.project_id', $project_id);
        if ($project && $task) {
            $taskData = $task->select('tasks.id', 'tasks.title', 'tasks.description', 'tasks.status', 'tasks.priority', 'tasks.due_date', 'users.name as user_name')
                ->leftJoin('users', 'users.id', 'tasks.created_by')
                ->where('tasks.id', $id)->first();
            if ($taskData != null) {
                return response()->json([
                    'message' => 'success',
                    'success' => true,
                    'data' => $taskData
                ], 200);
            } else {
                return response()->json([
                    'message' => 'no task found',
                    'success' => false
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'no task found',
                'success' => false
            ], 404);
        }
    }

    public function update(Request $request, $project_id, $id): JsonResponse
    {
        $project = Project::find($project_id);

        $task = Task::where('project_id', $project_id);
        if ($project && $task) {
            $taskData = $task->where('id', $id)->first();
            if ($taskData != null) {
                $request->validate([
                    'title' => 'required|string',
                    'description' => 'required',
                    'due_date' => 'required|date',
                ]);
                $update = $taskData->update([
                    'project_id' => $project_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'status' => $request->status,
                    'priority' => $request->priority,
                    'due_date' => Carbon::parse($request->due_date),
                    'created_by' => $request->user()->id
                ]);
                return response()->json([
                    'message' => 'success',
                    'success' => true,
                    'data' => $taskData
                ], 200);
            } else {
                return response()->json([
                    'message' => 'no task found',
                    'success' => false
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'no task found',
                'success' => false
            ], 404);
        }
    }

    public function destroy($project_id, $id): JsonResponse
    {
            $project = Project::find($project_id);

            $task = Task::where('project_id', $project_id)
                ->where('id', $id)->first();
            if ($project && $task) {
                $task->delete();
                return response()->json([
                    'message' => 'Task Delete successfully...',
                    'success' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'no task found',
                    'success' => false
                ], 404);
            }

    }
}
