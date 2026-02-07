<?php
use App\Http\Controllers\Api\V2\CompleteTaskController;
use App\Http\Controllers\Api\V2\TaskController;

    Route::apiResource('/tasks', TaskController::class);
    Route::patch('tasks/{task}/complete', CompleteTaskController::class);
?>