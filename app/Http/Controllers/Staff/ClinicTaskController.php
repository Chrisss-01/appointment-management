<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ClinicTask;
use Illuminate\Http\Request;

class ClinicTaskController extends Controller
{
    /**
     * List tasks for the current staff member.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $tasks = ClinicTask::where(function ($q) use ($request) {
                $q->where('assigned_to', $request->user()->id)
                  ->orWhere('assigned_by', $request->user()->id);
            })
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'high' THEN 2 WHEN priority = 'medium' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->paginate(15);

        return view('staff.tasks', compact('tasks', 'status'));
    }

    /**
     * Store a new clinic task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['assigned_by'] = $request->user()->id;
        $validated['assigned_to'] = $validated['assigned_to'] ?? $request->user()->id;

        ClinicTask::create($validated);

        return back()->with('success', 'Task created successfully.');
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, ClinicTask $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($validated['status'] === 'completed') {
            $task->markCompleted();
        } else {
            $task->update(['status' => $validated['status']]);
        }

        return back()->with('success', 'Task status updated.');
    }

    /**
     * Delete a task.
     */
    public function destroy(Request $request, ClinicTask $task)
    {
        if ($task->assigned_by !== $request->user()->id) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'Task deleted.');
    }
}
