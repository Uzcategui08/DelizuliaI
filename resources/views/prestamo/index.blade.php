@extends('adminlte::page')

@section('title', 'Préstamos')

@section('content_header')
<h1>Registro</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Préstamos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('prestamos.create') }}" class="btn btn-secondary btn-m float-right" data-placement="left">
                                    {{ __('Crear Nuevo') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Técnico</th>
                                    <th>Descripción</th>
                                    <th>Subcategoría</th>
                                    <th>Valor</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody>
                                    @foreach ($prestamos as $prestamo)
                                        <tr>
                                            <td>{{ $prestamo->id_prestamo }}</td>
                                            <td>{{ \Carbon\Carbon::parse($prestamo->f_prestamos)->format('m/d/Y') }}</td>
                                            <td>{{ $prestamo->empleado->nombre }}</td>
                                            <td>{{ $prestamo->descripcion }}</td>
                                            <td>
                                                @php
                                                    $categoriaSeleccionada = $categorias->firstWhere('id_categoria', $prestamo->subcategoria);
                                                @endphp
                                                {{ $categoriaSeleccionada->nombre ?? 'N/A' }}
                                            </td>
                                            <td>{{ $prestamo->valor }}</td>

                                            <td>
                                                @php
                                                    $estatuses = [
                                                        'pendiente' => 'Pendiente',
                                                        'parcialmente pagado' => 'Parcial',
                                                        'pagado' => 'Pagado'
                                                    ];
                                                @endphp
                                                {{ $estatuses[$prestamo->estatus] ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('prestamos.destroy', $prestamo->id_prestamo) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('prestamos.show', $prestamo->id_prestamo) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('prestamos.edit', $prestamo->id_prestamo) }}">
                                                        <i class="fa fa-fw fa-edit"></i>
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection