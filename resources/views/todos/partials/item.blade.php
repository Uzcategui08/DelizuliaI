@php
    use Illuminate\Support\Str;

    $isCompleted = $isCompleted ?? false;
    $dueBadge = 'badge-secondary';
    $dueLabel = 'Sin fecha límite';

    if (! $isCompleted && $todo->due_at) {
        if ($todo->due_at->isPast()) {
            $dueBadge = 'badge-danger';
            $dueLabel = 'Vencida ' . $todo->due_at->diffForHumans();
        } elseif ($todo->due_at->isToday()) {
            $dueBadge = 'badge-warning';
            $dueLabel = 'Vence hoy ' . $todo->due_at->format('H:i');
        } else {
            $dueBadge = 'badge-info';
            $dueLabel = 'Vence ' . $todo->due_at->diffForHumans();
        }
    }

    if ($isCompleted) {
        $dueBadge = 'badge-success';
        $dueLabel = 'Completada';
    }

    $reminderLabel = $todo->reminder_at
        ? $todo->reminder_at->diffForHumans()
        : 'Sin recordatorio';
@endphp

<div class="list-group-item px-0 py-3" data-todo-id="todo-{{ $todo->id }}">
    <div class="d-flex align-items-start">
        <div class="mr-3">
            <span class="badge badge-pill {{ $dueBadge }}">{{ $dueLabel }}</span>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center mb-2">
                <h5 class="mb-0 mr-2">{{ $todo->title }}</h5>
                @if($isCompleted)
                    <span class="badge badge-light text-muted">Finalizada {{ optional($todo->completed_at)->diffForHumans() }}</span>
                @else
                    <span class="badge badge-light text-muted">Creada {{ $todo->created_at->diffForHumans() }}</span>
                @endif
            </div>
            @if($todo->description)
                <p class="mb-2 text-muted small">{{ Str::limit($todo->description, 140) }}</p>
            @endif
            <div class="d-flex flex-wrap text-muted small">
                <span class="mr-3">
                    <i class="far fa-calendar-check mr-1"></i>
                    {{ $todo->due_at ? $todo->due_at->format('d/m/Y H:i') : 'Sin fecha' }}
                </span>
                <span class="mr-3">
                    <i class="far fa-bell mr-1"></i>
                    {{ $reminderLabel }}
                </span>
            </div>
        </div>
        <div class="ml-3 text-right">
            <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="mb-2" data-behavior="toggle-todo">
                @csrf
                @method('PATCH')
                <input type="hidden" name="context" value="list">
                <button type="submit" class="btn btn-sm {{ $isCompleted ? 'btn-outline-secondary' : 'btn-success' }}">
                    <i class="fas {{ $isCompleted ? 'fa-undo' : 'fa-check' }} mr-1"></i>
                    {{ $isCompleted ? 'Reabrir' : 'Completar' }}
                </button>
            </form>
            <a href="{{ route('todos.edit', $todo) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
        </div>
    </div>
</div>
