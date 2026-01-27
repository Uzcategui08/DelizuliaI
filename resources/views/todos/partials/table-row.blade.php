@php
    use Illuminate\Support\Str;
@endphp

<tr data-todo-id="todo-{{ $todo->id }}">
    <td>{{ $todo->title }}</td>
    <td>{{ Str::limit($todo->description, 80) }}</td>
    <td>{{ optional($todo->due_at)->format('d/m/Y H:i') ?: '—' }}</td>
    <td>{{ optional($todo->reminder_at)->format('d/m/Y H:i') ?: '—' }}</td>
    <td>
        <span class="badge {{ $todo->is_completed ? 'badge-success' : 'badge-warning' }}">
            {{ $todo->is_completed ? 'Completada' : 'Pendiente' }}
        </span>
    </td>
    <td class="d-flex gap-2 flex-wrap">
        <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="mr-1" data-behavior="toggle-todo">
            @csrf
            @method('PATCH')
            <input type="hidden" name="context" value="table">
            <button type="submit" class="btn btn-sm {{ $todo->is_completed ? 'btn-outline-secondary' : 'btn-success' }}">
                {{ $todo->is_completed ? 'Marcar pendiente' : 'Marcar completada' }}
            </button>
        </form>
        <a href="{{ route('todos.edit', $todo) }}" class="btn btn-sm btn-primary mr-1">Editar</a>
        <form action="{{ route('todos.destroy', $todo) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta tarea?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
        </form>
    </td>
</tr>
