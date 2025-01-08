<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The credential does not match...',
                'success' => false
            ], 401);
        }
        $token = $user->createToken($user->name . "Auth-Token")->plainTextToken;
        return response()->json([
            'message' => "Login Successfully",
            'token_type' => 'Bearer',
            'token' => $token,
            'success' => true
        ], 200);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if ($user) {
            $token = $user->createToken($user->name . "Auth-Token")->plainTextToken;
            return response()->json([
                'message' => "Registration Successfully",
                'token_type' => 'Bearer',
                'token' => $token,
                'success' => true
            ], 201);
        } else {
            return response()->json([
                'message' => 'Something went wrong...',
                'success' => false
            ], 500);
        }
    }
    public function logout(Request $request) : JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Logged out Successfully...',
                'success' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'User not found...',
                'success' => false
            ], 404);
        }
    }

    public function profile(Request $request) : JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'message' => 'Profile fetched',
                'data' => $request->user(),
                'success' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong...',
                'success' => false
            ], 500);
        }
    }

    public function data():JsonResponse
    {
        $user = User::get();
        $project = Project::get();
        $task = Task::get();

        return response()->json([
            'message' => 'success',
            'success' => true ,
            'users' => $user ,
            'projects' => $project,
            'tasks' => $task,
        ]);
    }
}
