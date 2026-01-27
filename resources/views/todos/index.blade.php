@extends('adminlte::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Tareas pendientes')

@section('content_header')
<h1>Lista To-Do</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <span class="card-title">Tareas y recordatorios</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#todoQuickPanel" aria-expanded="false" aria-controls="todoQuickPanel">
                                <i class="fas fa-plus mr-1"></i> Nueva tarea rápida
                            </button>
                            <a href="{{ route('todos.create') }}" class="btn btn-outline-secondary">Formulario completo</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div id="todoQuickAlert" class="alert alert-danger d-none"></div>
                        <div class="collapse mb-3" id="todoQuickPanel">
                            <div class="card card-body bg-light">
                                <form id="todoQuickForm" action="{{ route('todos.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="context" value="table">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="quick_title" class="font-weight-bold">Título</label>
                                            <input type="text" name="title" id="quick_title" class="form-control" placeholder="Nueva tarea" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="quick_due_at" class="font-weight-bold">Fecha límite</label>
                                            <input type="datetime-local" name="due_at" id="quick_due_at" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-8">
                                            <label for="quick_description" class="font-weight-bold">Descripción</label>
                                            <input type="text" name="description" id="quick_description" class="form-control" placeholder="Detalle rápido (opcional)">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="quick_reminder_at" class="font-weight-bold">Recordatorio</label>
                                            <input type="datetime-local" name="reminder_at" id="quick_reminder_at" class="form-control">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Guardar tarea
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Descripción</th>
                                        <th>Fecha límite</th>
                                        <th>Recordar el</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="todoTableBody">
                                    @forelse($todos as $todo)
                                        @include('todos.partials.table-row', ['todo' => $todo])
                                    @empty
                                        <tr data-empty-row>
                                            <td colspan="6" class="text-center text-muted">No hay tareas registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const quickForm = document.getElementById('todoQuickForm');
    const alertBox = document.getElementById('todoQuickAlert');
    const tableBody = document.getElementById('todoTableBody');
    const quickSubmit = quickForm ? quickForm.querySelector('button[type="submit"]') : null;

    const hideAlert = () => {
        if (!alertBox) {
            return;
        }

        alertBox.classList.add('d-none');
        alertBox.textContent = '';
        alertBox.classList.remove('alert-success');
        alertBox.classList.add('alert-danger');
    };

    const showAlert = (message, type = 'danger') => {
        if (!alertBox) {
            return;
        }

        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
        alertBox.classList.toggle('alert-danger', type === 'danger');
        alertBox.classList.toggle('alert-success', type === 'success');
    };

    const ensureEmptyRow = () => {
        if (!tableBody) {
            return;
        }

        const hasRows = tableBody.querySelector('tr[data-todo-id]');
        const existingEmpty = tableBody.querySelector('tr[data-empty-row]');

        if (!hasRows && !existingEmpty) {
            const emptyRow = document.createElement('tr');
            emptyRow.dataset.emptyRow = '';
            emptyRow.innerHTML = '<td colspan="6" class="text-center text-muted">No hay tareas registradas.</td>';
            tableBody.appendChild(emptyRow);
        }

        if (hasRows && existingEmpty) {
            existingEmpty.remove();
        }
    };

    const setButtonState = (button, disabled) => {
        if (button) {
            button.disabled = disabled;
        }
    };

    if (quickForm) {
        quickForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideAlert();
            setButtonState(quickSubmit, true);

            const formData = new FormData(quickForm);

            try {
                const response = await fetch(quickForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (response.ok) {
                    const payload = await response.json();
                    if (payload?.status === 'ok') {
                        if (tableBody && payload.html) {
                            tableBody.insertAdjacentHTML('afterbegin', payload.html);
                        }
                        ensureEmptyRow();
                        quickForm.reset();
                        showAlert(payload.message ?? 'Tarea creada correctamente.', 'success');
                    } else {
                        showAlert(payload?.message ?? 'No se pudo crear la tarea.');
                    }
                } else if (response.status === 422) {
                    const errorData = await response.json();
                    const messages = Object.values(errorData.errors ?? {}).flat();
                    showAlert(messages.join('\n') || 'Hay campos que necesitan tu atención.');
                } else {
                    showAlert('Ocurrió un error al guardar la tarea. Intenta nuevamente.');
                }
            } catch (error) {
                console.error(error);
                showAlert('No fue posible conectar con el servidor. Revisa tu conexión.');
            } finally {
                setButtonState(quickSubmit, false);
            }
        });
    }

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.dataset.behavior !== 'toggle-todo') {
            return;
        }

        event.preventDefault();
        hideAlert();

        const toggleButton = form.querySelector('button[type="submit"]');
        const row = form.closest('tr[data-todo-id]');
        setButtonState(toggleButton, true);

        try {
            const response = await fetch(form.action, {
                method: form.getAttribute('method') || 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            if (response.ok) {
                const payload = await response.json();
                if (payload?.status === 'ok' && payload.html) {
                    if (row) {
                        row.insertAdjacentHTML('beforebegin', payload.html);
                        row.remove();
                    }
                    ensureEmptyRow();
                    showAlert(payload.message ?? 'Estado de la tarea actualizado.', 'success');
                } else {
                    showAlert(payload?.message ?? 'No se pudo actualizar la tarea.');
                }
            } else if (response.status === 422) {
                const errorData = await response.json();
                const messages = Object.values(errorData.errors ?? {}).flat();
                showAlert(messages.join('\n') || 'Hay campos que necesitan tu atención.');
            } else {
                showAlert('Ocurrió un error al actualizar la tarea. Intenta nuevamente.');
            }
        } catch (error) {
            console.error(error);
            showAlert('No fue posible conectar con el servidor. Revisa tu conexión.');
        } finally {
            setButtonState(toggleButton, false);
        }
    });

    ensureEmptyRow();
});
</script>
@endpush
