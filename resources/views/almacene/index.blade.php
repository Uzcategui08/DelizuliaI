@extends('adminlte::page')

@section('title', 'Almacenes')

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
                            <span id="card_title">{{ __('Almacenes') }}</span>
                            <div class="float-right">
                                <a href="{{ route('almacenes.create') }}" class="btn btn-secondary btn-m float-right" data-placement="left">
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
                                        <th>ID Almacén</th>
                                        <th>Nombre</th>
                                        <th>Cantidad de productos</th>
                                        <th>Valor total de productos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($almacenes as $almacene)
                                        <tr>
                                            <td>{{ $almacene->id_almacen }}</td>
                                            <td>{{ $almacene->nombre }}</td>
                                            <td>
                                                {{ $almacene->inventarios->sum('cantidad') }}
                                            </td>
                                            <td>
                                                ${{ number_format($almacene->inventarios->sum(function($inv) { 
                                                    $precio = $inv->producto->precio ?? 0; 
                                                    $kilos = $inv->producto->kilos_promedio ?? 1; 
                                                    return $inv->cantidad * $precio * $kilos; 
                                                }), 2) }}
                                            </td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('almacenes.destroy', $almacene->id_almacen) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-success" href="{{ route('almacenes.edit', $almacene->id_almacen) }}"><i class="fa fa-fw fa-edit"></i></a>
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