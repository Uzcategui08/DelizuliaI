@extends('adminlte::page')

@section('title', 'Camiones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Camiones</h1>
        <a href="{{ route('camiones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Nuevo
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Placa</th>
                        <th>Km base (último aceite)</th>
                        <th>Activo</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($camiones as $camion)
                        <tr>
                            <td>{{ $camion->nombre }}</td>
                            <td>{{ $camion->placa ?? '-' }}</td>
                            <td>{{ $camion->ultimo_cambio_aceite_km !== null ? number_format($camion->ultimo_cambio_aceite_km, 0, ',', '.') : '-' }}</td>
                            <td>
                                @if ($camion->activo)
                                    <span class="badge badge-success">Sí</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('camiones.edit', $camion) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('camiones.destroy', $camion) }}" style="display:inline-block" onsubmit="return confirm('¿Eliminar este camión?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay camiones.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('camiones.mantenimiento') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Volver a mantenimiento
    </a>
</div>
@stop
