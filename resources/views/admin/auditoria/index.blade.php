@extends('adminlte::page')

@section('title', 'Auditoría')

@section('content_header')
    <h1>Auditoría (cambios realizados)</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <form class="form-inline" method="GET" action="{{ route('admin.auditoria.index') }}">
                <div class="form-group mr-2">
                    <label class="mr-2" for="event">Evento</label>
                    <select name="event" id="event" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach ($eventOptions as $opt)
                            <option value="{{ $opt }}" @selected(request('event') === $opt)>{{ strtoupper($opt) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label class="mr-2" for="model">Modelo</label>
                    <input type="text" name="model" id="model" class="form-control form-control-sm" value="{{ request('model') }}" placeholder="App\Models\Camion">
                </div>

                <div class="form-group mr-2">
                    <label class="mr-2" for="user_id">Usuario ID</label>
                    <input type="number" name="user_id" id="user_id" class="form-control form-control-sm" value="{{ request('user_id') }}" min="1" style="width: 120px">
                </div>

                <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                <a class="btn btn-sm btn-outline-secondary ml-2" href="{{ route('admin.auditoria.index') }}">Limpiar</a>
            </form>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Evento</th>
                        <th>Modelo</th>
                        <th>ID</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($log->user)
                                    {{ $log->user->name }} ({{ $log->user->email }})
                                @else
                                    <span class="text-muted">Sistema/Desconocido</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = match($log->event) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $badge }}">{{ strtoupper($log->event) }}</span>
                            </td>
                            <td><code>{{ $log->auditable_type }}</code></td>
                            <td>{{ $log->auditable_id }}</td>
                            <td>
                                @if ($log->event === 'updated')
                                    <div><strong>Nuevos:</strong> <code>{{ json_encode($log->new_values) }}</code></div>
                                    <div><strong>Anteriores:</strong> <code>{{ json_encode($log->old_values) }}</code></div>
                                @elseif ($log->event === 'created')
                                    <div><strong>Nuevos:</strong> <code>{{ json_encode($log->new_values) }}</code></div>
                                @else
                                    <div><strong>Antes:</strong> <code>{{ json_encode($log->old_values) }}</code></div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Sin registros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@stop
