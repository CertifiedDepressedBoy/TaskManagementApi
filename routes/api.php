<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Api\AuthController;

Route::post('login',[AuthController::class,'login'])->name('login');
Route::post('register',[AuthController::class,'register'])->name('register');



Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');

    Route::get('data', [AuthController::class, 'data']);

    //projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    //tasks
    Route::get('projects/{project_id}/tasks', [TaskController::class,'index']);
    Route::get('projects/{project_id}/tasks/{task}', [TaskController::class,'show']);

    Route::middleware('admin')->group(function(){
        //projects
        Route::post('/projects', [ProjectController::class,'store']);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

        //tasks
        Route::post('/projects/{project_id}/tasks',[TaskController::class,'store']);
        Route::put('/projects/{project_id}/tasks/{task}',[TaskController::class,'update']);
        Route::delete('/projects/{project_id}/tasks/{task}',[TaskController::class,'destroy']);
    });


    // Route::apiResource('projects', ProjectController::class);
    // Route::apiResource('projects/{project_id}/tasks', TaskController::class);
});
