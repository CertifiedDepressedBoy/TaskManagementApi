<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\task_assignment;
use Illuminate\Http\JsonResponse;

class TaskAssignmentController extends Controller
{
    public function work(Request $request,$project_id,$tasks,$user_id) : JsonResponse {
        $project = Project::find($project_id);
        $task = Task::find($tasks);
        if($project && $task){
            Task::where('id', $tasks)->update([
                'status' => $request->status
            ]);
            task_assignment::where('user_id', $user_id)
                            ->where('task_id',$tasks)
                            ->update([
                'status' => $request->status,
            ]);
            return response()->json([
                'message' => 'You done this task...',
                'success' => true
            ],200);
        }else{
            return response() -> json([
                'message' => 'Not Found' ,
                'success' => false
            ],404);
        }
    }
}
