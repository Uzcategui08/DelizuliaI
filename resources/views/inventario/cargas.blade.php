@extends('adminlte::page')

@section('title', 'Cargas/Descargas')

@section('content_header')
<h1>Registro de Ajustes de Inventario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Cargas/Descargas') }}
                        </span>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dataTable">
                            <thead class="thead">
                                <tr>
                                    <th class="text-center align-middle">Usuario</th>
                                    <th class="text-center align-middle">ID Producto</th>
                                    <th class="text-center align-middle">Producto</th>
                                    <th class="text-center align-middle">Almacén</th>
                                    <th class="text-center align-middle">Tipo</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Cantidad anterior</th>
                                    <th class="text-center align-middle">Cantidad nueva</th>
                                    <th class="text-center align-middle">Motivo</th>
                                    <th class="text-center align-middle">Fecha</th>
                                    <th class="text-center align-middle">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cargas as $ajuste)
                                <tr>
                                    <td class="text-center align-middle">{{ $ajuste->user->name}}</td>
                                    <td class="text-center align-middle">{{ $ajuste->producto->id_producto }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->producto->item }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->almacene->nombre }}</td>
                                    <td class="text-center align-middle">
                                    @if($ajuste->tipo_ajuste == 'compra' || $ajuste->tipo_ajuste == 'ajuste')
                                            <span class="badge bg-success">Carga</span>
                                        @elseif($ajuste->descripcion == 'Llave de venta')
                                            <span class="badge bg-info">Llave de venta</span>
                                        @else
                                            <span class="badge bg-danger">Descarga</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">{{ $ajuste->diferencia }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->cantidad_anterior }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->cantidad_nueva }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->descripcion }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->created_at->format('m/d/Y') }}</td>
                                    <td class="text-center align-middle">
                                        <form action="{{ route('ajustes.destroy', $ajuste->id) }}" method="POST" class="delete-ajuste-form" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
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
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delegación para todos los formularios de eliminación
            document.querySelectorAll('.delete-ajuste-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta acción eliminará el ajuste de inventario de forma permanente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@stop
