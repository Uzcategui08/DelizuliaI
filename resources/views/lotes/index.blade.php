@extends('adminlte::page')

@section('title', 'Lotes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Lotes</h1>
        <a href="{{ route('lotes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo lote
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha inicio</th>
                        <th>Productos</th>
                        <th>Días</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lotes as $lote)
                        <tr>
                            <td>{{ $lote->id }}</td>
                            <td>{{ $lote->nombre }}</td>
                            <td>{{ optional($lote->fecha_inicio)->format('Y-m-d') }}</td>
                            <td>{{ $lote->productos_count }}</td>
                            <td>{{ $lote->dias_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('lotes.show', $lote) }}" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay lotes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($lotes, 'links'))
            <div class="card-footer">
                {{ $lotes->links() }}
            </div>
        @endif
    </div>
@stop
