@extends('adminlte::page')

@section('title', 'Transferencias de Almacen')

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
                                {{ __('Transferencias') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('transferencias.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
                                  {{ __('Crear Nuevo') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr>
									<th >ID Transferencia</th>
									<th >Producto</th>
                                    <th >SKU</th>
									<th >Almacen Origen</th>
									<th >Almacen Destino</th>
									<th >Cantidad</th>
									<th >Usuario</th>
									<th >Observaciones</th>
                                    <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transferencias as $transferencia)
                                        <tr>
										<td >{{ $transferencia->id_transferencia }}</td>
										<td >{{ $transferencia->producto->id_producto }}</td>
                                        <td >{{ $transferencia->producto->sku }}</td>
										<td >{{ $transferencia->almacenOrigen->nombre }}</td>
										<td >{{ $transferencia->almacenDestino->nombre }}</td>
										<td >{{ $transferencia->cantidad }}</td>
										<td >{{ $transferencia->usuario->name }}</td>
										<td >{{ $transferencia->observaciones }}</td>

                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('transferencias.destroy', $transferencia->id_transferencia) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
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
