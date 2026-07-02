<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * List the current user's tasks.
     */
    public function index(): View
    {
        $tasks = Auth::user()->tasks()->latest()->get();

        return view('tasks.index', ['tasks' => $tasks]);
    }

    /**
     * Show the "new task" form.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Persist a new task.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,doing,done'],
        ]);

        Auth::user()->tasks()->create($data);

        return redirect()->route('tasks.index');
    }

    /**
     * Show a single task.
     */
    public function show(Task $task): View
    {
        $this->authorizeTask($task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the edit form for a task.
     */
    public function edit(Task $task): View
    {
        $this->authorizeTask($task);

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update a task.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,doing,done'],
        ]);

        $task->update($data);

        return redirect()->route('tasks.show', $task);
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $task->delete();

        return redirect()->route('tasks.index');
    }

    /**
     * Make sure the task belongs to the authenticated user.
     */
    protected function authorizeTask(Task $task): void
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
