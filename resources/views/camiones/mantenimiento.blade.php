@extends('adminlte::page')

@section('title', 'Camiones - Mantenimiento')

@section('content_header')
    <h1>Mantenimiento de Camiones</h1>
@stop

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title mb-0">Registro semanal (viernes)</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('camiones.kilometrajes.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="camion_id">Camión</label>
                            <select name="camion_id" id="camion_id" class="form-control" required>
                                <option value="">-- Selecciona --</option>
                                @foreach ($camiones as $camion)
                                    <option value="{{ $camion->id }}" @selected(old('camion_id') == $camion->id)>
                                        {{ $camion->nombre }}@if($camion->placa) ({{ $camion->placa }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', now()->toDateString()) }}" required>
                            <small class="text-muted">Solo se aceptan viernes.</small>
                        </div>

                        <div class="form-group">
                            <label for="kilometraje">Kilometraje (odómetro)</label>
                            <input type="number" name="kilometraje" id="kilometraje" class="form-control" value="{{ old('kilometraje') }}" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="nota">Nota (opcional)</label>
                            <textarea name="nota" id="nota" class="form-control" rows="2">{{ old('nota') }}</textarea>
                        </div>

                        <button class="btn btn-primary btn-block" type="submit">
                            <i class="fas fa-save mr-1"></i> Guardar
                        </button>
                    </form>

                    <hr>
                    <a class="btn btn-outline-secondary btn-block" href="{{ route('camiones.index') }}">
                        <i class="fas fa-truck mr-1"></i> Administrar camiones
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="card-title mb-0">Estado de aceite (umbral: {{ number_format($umbralKm, 0, ',', '.') }} km)</span>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Camión</th>
                                <th>Último registro</th>
                                <th>Km desde cambio de aceite</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($camiones as $camion)
                                @php
                                    $ultimo = $camion->ultimoKilometraje;
                                    $kmDesde = $camion->kmDesdeCambioAceite();
                                    $requiere = $camion->requiereCambioAceite($umbralKm);
                                @endphp
                                <tr class="{{ $requiere ? 'table-danger' : '' }}">
                                    <td>
                                        <strong class="{{ $requiere ? 'text-danger' : '' }}">{{ $camion->nombre }}</strong>
                                        @if($camion->placa)
                                            <div class="text-muted">{{ $camion->placa }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($ultimo)
                                            <div><strong>{{ number_format($ultimo->kilometraje, 0, ',', '.') }}</strong> km</div>
                                            <div class="text-muted">{{ $ultimo->fecha?->format('d/m/Y') }}</div>
                                        @else
                                            <span class="text-muted">Sin registros</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($kmDesde === null)
                                            <span class="text-muted">Configura el km base del último cambio</span>
                                        @else
                                            <span class="{{ $requiere ? 'text-danger font-weight-bold' : '' }}">
                                                {{ number_format($kmDesde, 0, ',', '.') }} km
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($kmDesde === null)
                                            <span class="badge badge-secondary">Pendiente de configurar</span>
                                        @elseif ($requiere)
                                            <span class="badge badge-danger">Cambio de aceite</span>
                                        @else
                                            <span class="badge badge-success">OK</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('camiones.cambio-aceite', $camion) }}" onsubmit="return confirm('¿Marcar cambio de aceite para este camión? Esto fijará el km base al último registro.');" style="display:inline-block">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary" type="submit" {{ $ultimo ? '' : 'disabled' }}>
                                                <i class="fas fa-oil-can mr-1"></i> Marcar cambio
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay camiones activos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
