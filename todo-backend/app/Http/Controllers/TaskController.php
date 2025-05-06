<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function index()
    {
        if (Auth::user()) {
            return response()->json([
                'tasksData' => Task::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get(),
                // 'taskContent' => $request
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $insertData = [
            'user_id' => Auth::user()->id,
            'title' =>  $request->title,
            'description' => $request->description
        ];

        $newTask = Task::create($insertData);
        return response()->json([
            'success' => true,
            'tasksData' => $newTask
            // 'taskContent' => $request
        ]);
    }

    public function update(Request $request, Task $task)
    {
        if ($request->title) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);
            $task->title = $request->title;
            $task->description = $request->description;
        } else {
            $validated = $request->validate([
                'is_completed' => 'required|boolean',
            ]);

            $task->is_completed = $request->is_completed;
        }
        $task->save();

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function destroy(Request $request, Task $task)
    {
        $validated = $request->validate([
            'is_deleted' => 'required|boolean',
        ]);
        $task->is_deleted = $validated['is_deleted'];
        $task->save();

        return response()->json(['success' => true, 'task' => $task]);
    }
}
