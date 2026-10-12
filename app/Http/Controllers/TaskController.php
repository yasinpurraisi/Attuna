<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function update(Task $task)
    {
        //auth
        $task->update(['completed' => ! $task->completed]);

        return back();
    }
}
