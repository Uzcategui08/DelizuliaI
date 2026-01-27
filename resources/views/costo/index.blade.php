@extends('adminlte::page')

@section('title', 'Costos')

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
                                {{ __('Costos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('costos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
                                    @foreach ($costos as $costo)
                                        <tr>
                                            <td>{{ $costo->id_costos }}</td>
                                            <td>{{ \Carbon\Carbon::parse($costo->f_costos)->format('m/d/Y') }}</td>
                                            <td>{{ $costo->empleado->nombre }}</td>
                                            <td>{{ $costo->descripcion }}</td>
                                            <td>{{ $costo->categoria->nombre }}</td>
                                            <td>{{ $costo->valor }}</td>
                                            <?php 
                                                $estatus = $costo->estatus;
                                                $estatuses = [
                                                    'pendiente' => 'Pendiente',
                                                    'parcialmente_pagado' => 'Parcial',
                                                    'pagado' => 'Pagado'
                                                ];
                                            ?>
                                            <td>{{ $estatuses[$estatus] ?? 'N/A' }}</td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('costos.destroy', $costo->id_costos) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('costos.show', $costo->id_costos) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('costos.edit', $costo->id_costos) }}">
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