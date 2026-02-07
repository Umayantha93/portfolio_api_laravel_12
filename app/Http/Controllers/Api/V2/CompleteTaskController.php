<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompleteTaskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CompleteTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->is_completed = $request->is_completed;
        $task->save();
        return $task->toResource();
    }
}
