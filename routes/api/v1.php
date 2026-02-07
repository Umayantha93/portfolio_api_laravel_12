<?php
use App\Http\Controllers\Api\V1\CompleteTaskController;
use App\Http\Controllers\Api\V1\TaskController;

    Route::apiResource('/tasks', TaskController::class);
    Route::patch('tasks/{task}/complete', CompleteTaskController::class);
?>