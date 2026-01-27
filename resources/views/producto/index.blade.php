@extends('adminlte::page')

@section('title', 'Productos')

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
                            {{ __('Productos') }}
                        </span>

                        <div class="float-right">
                            <a href="{{ route('productos.create') }}" class="btn btn-secondary btn-m float-right" data-placement="left">
                                {{ __('Crear Nuevo') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                    <table  class="table table-striped table-bordered dataTable">
                            <thead class="thead">
                                <tr>
                                

                                    <th>ID Producto</th>
                                    <th>Nombre</th>
                                    <th>Marca</th>
                                    <th>Tipo de producto</th>
                                    <th>Código de barras</th>
                                    <th>Precio</th>

                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                <tr>
                              

                                    <td>{{ $producto->id_producto }}</td>
                                    <td>{{ $producto->item }}</td>
                                    <td>{{ $producto->marca }}</td>
                                    <td>{{ $producto->t_llave }}</td>
                                    <td>{{ $producto->sku }}</td>
                                    <td>{{ $producto->precio }}</td>

                                    <td>
                                        <form onsubmit="return confirmDelete(this)" action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                            <a class="btn btn-sm btn-primary " href="{{ route('productos.show', $producto->id_producto) }}"><i class="fa fa-fw fa-eye"></i> </a>
                                            <a class="btn btn-sm btn-success" href="{{ route('productos.edit', $producto->id_producto) }}"><i class="fa fa-fw fa-edit"></i> </a>
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
@stop
