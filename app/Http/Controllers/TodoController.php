<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $todos = Todo::query()
            ->orderBy('is_completed')
            ->orderByRaw('COALESCE(due_at, reminder_at, created_at) ASC')
            ->get();

        return view('todos.index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $todo = new Todo();

        return view('todos.create', compact('todo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_completed'] = $request->boolean('is_completed');
        $data['completed_at'] = $data['is_completed'] ? now() : null;

        $todo = Todo::create($data);

        if ($request->expectsJson()) {
            $todo->refresh();

            $context = $request->input('context', 'list');
            $view = $context === 'table'
                ? 'todos.partials.table-row'
                : 'todos.partials.item';

            $viewData = ['todo' => $todo];
            if ($view === 'todos.partials.item') {
                $viewData['isCompleted'] = false;
            }

            return response()->json([
                'status' => 'ok',
                'message' => 'Tarea creada correctamente.',
                'context' => $context,
                'html' => view($view, $viewData)->render(),
                'stats' => $this->todoStats(),
            ]);
        }

        return redirect()
            ->route('todos.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo): RedirectResponse
    {
        return redirect()->route('todos.edit', $todo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo): View
    {
        return view('todos.edit', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_completed'] = $request->boolean('is_completed');
        $data['completed_at'] = $data['is_completed'] ? ($todo->completed_at ?? now()) : null;

        $todo->update($data);

        return redirect()
            ->route('todos.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo): RedirectResponse
    {
        $todo->delete();

        return redirect()
            ->route('todos.index')
            ->with('success', 'Tarea eliminada.');
    }

    /**
     * Toggle the completion flag for the given todo.
     */
    public function toggle(Request $request, Todo $todo): JsonResponse|RedirectResponse
    {
        $todo->is_completed ? $todo->markAsPending() : $todo->markAsCompleted();

        if ($request->expectsJson()) {
            $todo->refresh();

            $context = $request->input('context', 'list');
            $view = $context === 'table'
                ? 'todos.partials.table-row'
                : 'todos.partials.item';

            $viewData = ['todo' => $todo];
            if ($view === 'todos.partials.item') {
                $viewData['isCompleted'] = $todo->is_completed;
            }

            return response()->json([
                'status' => 'ok',
                'message' => $todo->is_completed ? 'Tarea marcada como completada.' : 'Tarea reabierta.',
                'todo_id' => $todo->id,
                'is_completed' => $todo->is_completed,
                'context' => $context,
                'html' => view($view, $viewData)->render(),
                'stats' => $this->todoStats(),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Estado de la tarea actualizado.');
    }

    protected function todoStats(): array
    {
        $pendingCount = Todo::where('is_completed', false)->count();
        $completedCount = Todo::where('is_completed', true)->count();
        $overdueCount = Todo::where('is_completed', false)
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        return [
            'pending-todos' => $pendingCount,
            'pending-todos-label' => $pendingCount,
            'completed-todos-label' => $completedCount,
            'overdue-todos' => $overdueCount,
        ];
    }

    /**
     * Validate the incoming request data.
     */
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
        ]);
    }
}
