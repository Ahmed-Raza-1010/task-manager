<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->is_admin) {
            return response()->json(Task::with('user')->get()); // ✅ Admin sees ALL tasks
        }

        return response()->json(Task::where('user_id', $user->id)->get()); // ✅ Normal user sees only their tasks
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info("Before validate");
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        Log::info("after validate");

        $task = new Task([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);
        Log::info("after creation");

        if ($request->hasFile('image')) {
            $task->image = $request->file('image')->store('tasks', 'public');
        }
        Log::info("after creation with img");

        $task->save();

        Log::info("SAVED");
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Ensure only the task owner or an admin can edit the task
        Log::info('start of update');
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        Log::info('Edit Request Data:', $request->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        Log::info('validation of update');
        // Update task details
        $task->title = $request->title;
        $task->description = $request->description;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($task->image) {
                Storage::delete('public/' . $task->image);
            }
            $task->image = $request->file('image')->store('tasks', 'public');
        }
        Log::info('Before of update');

        $task->save();
        
        Log::info('Finally Updated');
        
        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}
