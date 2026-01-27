@extends('adminlte::page')

@section('title', 'Gastos')

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
                                {{ __('Gastos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('gastos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Técnico</th>
                                        <th>Descripción</th>
                                        <th>Subcategoría</th>
                                        <th>Valor</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gastos as $gasto)
                                        <tr>
                                            <td>{{ $gasto->id_gastos }}</td>
                                            <td>{{ \Carbon\Carbon::parse($gasto->f_gastos)->format('m/d/Y') }}</td>
                                            <td>{{ $gasto->empleado->nombre }}</td>
                                            <td>{{ $gasto->descripcion }}</td>
                                            <td>{{ $gasto->categoria ? $gasto->categoria->nombre : 'Sin categoría' }}</td>
                                            <td>{{ $gasto->valor }}</td>
                                            <?php 
                                                $estatus = $gasto->estatus;
                                                $estatuses = [
                                                    'pendiente' => 'Pendiente',
                                                    'parcialmente_pagado' => 'Parcial',
                                                    'pagado' => 'Pagado'
                                                ];
                                            ?>
                                            <td>{{ $estatuses[$estatus] ?? 'N/A' }}</td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('gastos.destroy', $gasto->id_gastos) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('gastos.show', $gasto->id_gastos) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('gastos.edit', $gasto->id_gastos) }}">
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
@stop